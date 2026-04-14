<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\Leave;
use App\Http\Controllers\Controller;

class LeaveController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ตรวจสอบว่า User นี้มีข้อมูลในตารางพนักงานหรือไม่
        if (!$user->employee) {
            return "ไม่พบข้อมูลพนักงานสำหรับบัญชีนี้ (กรุณาใช้บัญชีพนักงานทั่วไปในการทดสอบ)";
        }

        $leaves = $user->employee->leaves()->orderBy('created_at', 'desc')->paginate(10);
        
        return view('leaves.index', compact('leaves'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // ตรวจสอบว่า ถ้าเป็น Admin/HR ให้ใช้ employee_id ที่เลือกจากฟอร์ม
        // แต่ถ้าเป็นพนักงานทั่วไป ให้ใช้ ID ของตัวเองเท่านั้น
        $employeeId = Gate::allows('access-admin') ? $request->employee_id : auth()->user()->employee->id;
        
        // สร้างใบลาโดยผูกกับพนักงานที่ Login อยู่
        Leave::create([
            'employee_id' => $employeeId,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => Gate::allows('access-admin') ? 'Approved' : 'Pending', // ถ้า HR คีย์ให้ คืออนุมัติทันที
            'approved_by' => Gate::allows('access-admin') ? auth()->user()->employee->id : null,
        ]);

        return redirect()->back()->with('success', 'ส่งใบลาเรียบร้อยแล้ว รอหัวหน้าอนุมัติ');
    }

    // แสดงหน้ารายการใบลา (สำหรับหัวหน้า)
    public function approvals(Request $request)
    {
        // 🔒 ล็อคสิทธิ์: ต้องเป็นหัวหน้างานเท่านั้นถึงจะเข้าหน้านี้ได้
        Gate::authorize('is-manager');

        $user = auth()->user();
        
        // รับค่าจากการค้นหา (ถ้าไม่มีให้ตั้งค่าเริ่มต้นเป็นดึงเฉพาะ 'Pending')
        $search = $request->input('search');
        $statusFilter = $request->input('status', 'Pending'); 

        // ดึงใบลาเฉพาะของลูกน้อง
        $query = Leave::whereHas('employee', function($q) use ($user) {
            $q->where('manager_id', $user->employee->id);
        });

        // 🔍 กรองตามสถานะ
        if ($statusFilter !== 'All') {
            $query->where('status', $statusFilter);
        }

        // 🔍 กรองตามชื่อพนักงาน
        if ($search) {
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('leaves.approvals', compact('leaves', 'search', 'statusFilter'));
    }

    // อัปเดตสถานะใบลา
    public function updateStatus(Request $request, $id)
    {
        // 🔒 ล็อคสิทธิ์: ต้องเป็นหัวหน้างานเท่านั้นถึงจะเข้าหน้านี้ได้
        Gate::authorize('is-manager');
        
        $leave = Leave::findOrFail($id);
        
        $leave->update([
            'status' => $request->status, // รับค่า 'Approved' หรือ 'Rejected' จากปุ่มที่กด
            'approved_by' => auth()->user()->employee->id
        ]);

        $actionText = $request->status == 'Approved' ? '✅ อนุมัติ' : '❌ ปฏิเสธ';
        return redirect()->back()->with('success', "{$actionText} ใบลาเรียบร้อยแล้ว");
    }
    
}
