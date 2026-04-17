<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OtRequest;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class OtRequestController extends Controller
{
    // ==========================================
    // 🌟 ฝั่งพนักงาน (ESS) - ส่งคำร้อง
    // ==========================================
    
    // หน้าฟอร์มและประวัติการขอ OT
    public function index()
    {
        $employee = auth()->user()->employee;
        if (!$employee) return redirect('/home')->with('error', 'ไม่พบข้อมูลพนักงาน');

        $otRequests = OtRequest::where('employee_id', $employee->id)
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);

        return view('ot.index', compact('otRequests'));
    }

    // บันทึกคำร้อง OT ลงฐานข้อมูล
    public function store(Request $request)
    {
        $request->validate([
            'work_date' => 'required|date|after_or_equal:today', // ควรขอ OT ล่วงหน้าหรือวันปัจจุบัน
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:255',
        ], [
            'end_time.after' => 'เวลาสิ้นสุดต้องมากกว่าเวลาเริ่มต้น',
            'work_date.after_or_equal' => 'ไม่สามารถขอ OT ย้อนหลังได้ (กรุณาติดต่อ HR)'
        ]);

        OtRequest::create([
            'employee_id' => auth()->user()->employee->id,
            'work_date' => $request->work_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
            'status' => 'Pending'
        ]);

        return redirect()->back()->with('success', 'ส่งคำร้องขอทำ OT เรียบร้อยแล้ว รอหัวหน้าอนุมัติ ⏳');
    }

    // ==========================================
    // 🌟 ฝั่งหัวหน้างาน (MSS) - อนุมัติ OT
    // ==========================================

    // หน้ารายการรออนุมัติ OT
    public function approvals()
    {
        Gate::authorize('is-manager'); // ต้องเป็นหัวหน้า
        $managerId = auth()->user()->employee->id;

        // ดึงเฉพาะคำร้องของลูกน้องตัวเอง ที่ยัง Pending อยู่
        $requests = OtRequest::whereHas('employee', function($q) use ($managerId) {
            $q->where('manager_id', $managerId);
        })->where('status', 'Pending')->orderBy('work_date', 'asc')->paginate(10);

        return view('ot.approvals', compact('requests'));
    }

    // อัปเดตสถานะ อนุมัติ/ปฏิเสธ
    public function updateStatus(Request $request, $id)
    {
        Gate::authorize('is-manager');
        
        $otReq = OtRequest::findOrFail($id);
        $otReq->update([
            'status' => $request->status,
            'manager_id' => auth()->user()->employee->id
        ]);

        $action = $request->status == 'Approved' ? '✅ อนุมัติ' : '❌ ไม่อนุมัติ';
        return redirect()->back()->with('success', "{$action} การทำ OT เรียบร้อยแล้ว");
    }
}