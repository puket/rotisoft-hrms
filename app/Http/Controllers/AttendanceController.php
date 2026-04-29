<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect('/home')->with('error', 'บัญชีนี้ยังไม่ได้ผูกกับข้อมูลพนักงาน');
        }

        $today = Carbon::today()->toDateString();

        $todayAttendance = Attendance::where('employee_id', $employee->id)
                                     ->where('work_date', $today)
                                     ->first();

        $history = Attendance::where('employee_id', $employee->id)
                             ->orderBy('work_date', 'desc')
                             ->paginate(10);

        $requests = \App\Models\AttendanceRequest::where('employee_id', $employee->id)
                            ->orderBy('created_at', 'desc')
                            ->paginate(5, ['*'], 'req_page');

        return view('attendances.index', compact('todayAttendance', 'history', 'requests'));
    }

    public function clockIn(Request $request)
    {
        $employee = auth()->user()->employee;
        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        $exists = Attendance::where('employee_id', $employee->id)->where('work_date', $today)->exists();
        
        if (!$exists) {
            Attendance::create([
                'company_id' => $employee->company_id, // 🌟 ยัด company_id
                'employee_id' => $employee->id,
                'work_date' => $today,
                'clock_in' => $now,
                'status' => 'Present' 
            ]);
            return redirect()->back()->with('success', 'บันทึกเวลาเข้างานสำเร็จ! ☀️');
        }

        return redirect()->back()->with('error', 'คุณได้บันทึกเวลาเข้างานของวันนี้ไปแล้ว');
    }

    public function clockOut(Request $request)
    {
        $employee = auth()->user()->employee;
        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        $attendance = Attendance::where('employee_id', $employee->id)
                                ->where('work_date', $today)
                                ->first();

        if ($attendance && is_null($attendance->clock_out)) {
            $attendance->update(['clock_out' => $now]);
            return redirect()->back()->with('success', 'บันทึกเวลาออกงานสำเร็จ! กลับบ้านปลอดภัยครับ 🌙');
        }

        return redirect()->back()->with('error', 'ไม่สามารถบันทึกเวลาออกงานได้');
    }

    public function createRequest()
    {
        return view('attendances.request');
    }

    public function storeRequest(Request $request)
    {
        $request->validate([
            'work_date' => 'required|date|before_or_equal:today',
            'requested_clock_in' => 'nullable|date_format:H:i',
            'requested_clock_out' => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:255',
        ]);

        $employee = auth()->user()->employee;

        $pendingExists = \App\Models\AttendanceRequest::where('employee_id', $employee->id)
            ->where('work_date', $request->work_date)
            ->where('status', 'Pending')
            ->exists();

        if ($pendingExists) {
            return redirect()->back()->with('error', 'คุณมีคำร้องของวันที่เลือกที่กำลังรออนุมัติอยู่แล้ว ⏳');
        }

        \App\Models\AttendanceRequest::create([
            'company_id' => $employee->company_id, // 🌟 ยัด company_id
            'employee_id' => $employee->id,
            'work_date' => $request->work_date,
            'requested_clock_in' => $request->requested_clock_in,
            'requested_clock_out' => $request->requested_clock_out,
            'reason' => $request->reason,
            'status' => 'Pending'
        ]);

        return redirect('/attendance')->with('success', 'ส่งคำร้องขอแก้ไขเวลาเรียบร้อยแล้ว โปรดรอหัวหน้างานอนุมัติ ✅');
    }

    public function approvals()
    {
        Gate::authorize('is-manager');
        $user = auth()->user();

        $requests = \App\Models\AttendanceRequest::whereHas('employee', function($q) use ($user) {
            $q->where('manager_id', $user->employee->id);
        })->where('status', 'Pending')->orderBy('created_at', 'asc')->paginate(10);

        return view('attendances.approvals', compact('requests'));
    }

    public function updateStatus(Request $request, $id)
    {
        Gate::authorize('is-manager');

        $req = \App\Models\AttendanceRequest::with('employee')->findOrFail($id);
        $status = $request->status;

        $req->update([
            'status' => $status,
            'approved_by' => auth()->user()->employee->id
        ]);

        if ($status === 'Approved') {
            $attendance = Attendance::firstOrNew([
                'employee_id' => $req->employee_id,
                'work_date' => $req->work_date
            ]);

            $attendance->company_id = $req->employee->company_id; // 🌟 ยัด company_id

            if ($req->requested_clock_in) $attendance->clock_in = $req->requested_clock_in;
            if ($req->requested_clock_out) $attendance->clock_out = $req->requested_clock_out;
            
            $attendance->status = 'Present (แก้ไขแล้ว)'; 
            $attendance->save();
        }

        $actionText = $status == 'Approved' ? '✅ อนุมัติ' : '❌ ปฏิเสธ';
        return redirect()->back()->with('success', "{$actionText} คำร้องขอแก้ไขเวลาเรียบร้อยแล้ว");
    }
    
    public function report(Request $request)
    {
        Gate::authorize('is-hr');

        $date = $request->input('date', Carbon::today()->toDateString());
        $search = $request->input('search');

        $query = \App\Models\Employee::with('department', 'position')->where('status', 'Active');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('employee_code', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->paginate(15)->withQueryString();

        $attendances = Attendance::where('work_date', $date)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->keyBy('employee_id'); 

        $totalEmployees = \App\Models\Employee::where('status', 'Active')->count();
        $presentCount = Attendance::where('work_date', $date)->count();
        $absentCount = $totalEmployees - $presentCount;

        return view('attendances.report', compact('employees', 'attendances', 'date', 'search', 'totalEmployees', 'presentCount', 'absentCount'));
    }
}