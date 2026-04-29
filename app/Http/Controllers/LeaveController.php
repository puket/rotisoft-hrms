<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Leave;
use App\Models\Employee;

class LeaveController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user->employee) {
            return "ไม่พบข้อมูลพนักงานสำหรับบัญชีนี้ (กรุณาใช้บัญชีพนักงานทั่วไปในการทดสอบ)";
        }

        $leaves = $user->employee->leaves()->with('leaveType')->orderBy('created_at', 'desc')->paginate(10);
        $leaveTypes = \App\Models\LeaveType::all();

        return view('leaves.index', compact('leaves', 'leaveTypes'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $employeeId = (Gate::allows('is-hr') && $request->filled('employee_id')) 
                        ? $request->employee_id 
                        : auth()->user()->employee->id;
                        
        $employee = Employee::find($employeeId);

        Leave::create([
            'company_id' => $employee->company_id, // 🌟 ยัด company_id
            'employee_id' => $employee->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => Gate::allows('is-hr') ? 'Approved' : 'Pending', 
            'approved_by' => Gate::allows('is-hr') ? auth()->user()->employee->id : null,
        ]);

        return redirect()->back()->with('success', 'ส่งใบลาเรียบร้อยแล้ว รอหัวหน้าอนุมัติ');
    }

   public function approvals(Request $request)
    {
        Gate::authorize('is-manager');

        $user = auth()->user();
        
        $search = $request->input('search');
        $statusFilter = $request->input('status', 'Pending'); 

        $query = Leave::with(['employee', 'leaveType'])->whereHas('employee', function($q) use ($user) {
            $q->where('manager_id', $user->employee->id);
        });

        if ($statusFilter !== 'All') {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('leaves.approvals', compact('leaves', 'search', 'statusFilter'));
    }

    public function updateStatus(Request $request, $id)
    {
        Gate::authorize('is-manager');
        
        $leave = Leave::findOrFail($id);
        
        $leave->update([
            'status' => $request->status, 
            'approved_by' => auth()->user()->employee->id
        ]);

        $actionText = $request->status == 'Approved' ? '✅ อนุมัติ' : '❌ ปฏิเสธ';
        return redirect()->back()->with('success', "{$actionText} ใบลาเรียบร้อยแล้ว");
    }
}