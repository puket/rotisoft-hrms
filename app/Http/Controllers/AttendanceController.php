<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // หน้าหลักของระบบลงเวลา
    public function index()
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect('/home')->with('error', 'บัญชีนี้ยังไม่ได้ผูกกับข้อมูลพนักงาน');
        }

        $today = Carbon::today()->toDateString();

        // 1. ดึงข้อมูลลงเวลาของ "วันนี้"
        $todayAttendance = Attendance::where('employee_id', $employee->id)
                                     ->where('work_date', $today)
                                     ->first();

        // 2. ดึงประวัติย้อนหลัง
        $history = Attendance::where('employee_id', $employee->id)
                             ->orderBy('work_date', 'desc')
                             ->paginate(10);

        // 3. ดึงข้อมูลประวัติการส่งคำร้องขอแก้ไขเวลา
        $requests = \App\Models\AttendanceRequest::where('employee_id', $employee->id)
                            ->orderBy('created_at', 'desc')
                            ->paginate(5, ['*'], 'req_page');

        return view('attendances.index', compact('todayAttendance', 'history', 'requests'));
    }

    // บันทึกเวลาเข้างาน (Clock In)
    public function clockIn(Request $request)
    {
        $employee = auth()->user()->employee;
        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        // เช็คเผื่อว่ากดเบิ้ล
        $exists = Attendance::where('employee_id', $employee->id)->where('work_date', $today)->exists();
        
        if (!$exists) {
            Attendance::create([
                'employee_id' => $employee->id,
                'work_date' => $today,
                'clock_in' => $now,
                'status' => 'Present' // (เดี๋ยวอนาคตเราค่อยมาเขียนเงื่อนไขเช็ค "สาย/Late" ได้ครับ)
            ]);
            return redirect()->back()->with('success', 'บันทึกเวลาเข้างานสำเร็จ! ☀️');
        }

        return redirect()->back()->with('error', 'คุณได้บันทึกเวลาเข้างานของวันนี้ไปแล้ว');
    }

    // บันทึกเวลาออกงาน (Clock Out)
    public function clockOut(Request $request)
    {
        $employee = auth()->user()->employee;
        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        $attendance = Attendance::where('employee_id', $employee->id)
                                ->where('work_date', $today)
                                ->first();

        if ($attendance && is_null($attendance->clock_out)) {
            $attendance->update([
                'clock_out' => $now
            ]);
            return redirect()->back()->with('success', 'บันทึกเวลาออกงานสำเร็จ! กลับบ้านปลอดภัยครับ 🌙');
        }

        return redirect()->back()->with('error', 'ไม่สามารถบันทึกเวลาออกงานได้');
    }

    // เปิดหน้าฟอร์มขอแก้ไขเวลา
    public function createRequest()
    {
        return view('attendances.request');
    }

    // บันทึกคำร้องลงฐานข้อมูล
    public function storeRequest(Request $request)
    {
        $request->validate([
            'work_date' => 'required|date|before_or_equal:today', // ห้ามขอแก้วันในอนาคต
            'requested_clock_in' => 'nullable|date_format:H:i',
            'requested_clock_out' => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:255',
        ], [
            'work_date.before_or_equal' => 'ไม่สามารถขอแก้ไขเวลาของวันในอนาคตได้',
            'reason.required' => 'กรุณาระบุเหตุผลในการขอแก้ไขเวลา (เช่น ลืมสแกนนิ้ว, พบลูกค้า)'
        ]);

        $employee = auth()->user()->employee;

        // เช็คว่ามีคำร้องของวันเดียวกันที่กำลัง Pending อยู่ไหม (ป้องกันส่งซ้ำ)
        $pendingExists = \App\Models\AttendanceRequest::where('employee_id', $employee->id)
            ->where('work_date', $request->work_date)
            ->where('status', 'Pending')
            ->exists();

        if ($pendingExists) {
            return redirect()->back()->with('error', 'คุณมีคำร้องของวันที่เลือกที่กำลังรออนุมัติอยู่แล้ว ⏳');
        }

        // บันทึกคำร้อง
        \App\Models\AttendanceRequest::create([
            'employee_id' => $employee->id,
            'work_date' => $request->work_date,
            'requested_clock_in' => $request->requested_clock_in,
            'requested_clock_out' => $request->requested_clock_out,
            'reason' => $request->reason,
            'status' => 'Pending'
        ]);

        return redirect('/attendance')->with('success', 'ส่งคำร้องขอแก้ไขเวลาเรียบร้อยแล้ว โปรดรอหัวหน้างานอนุมัติ ✅');
    }

    // ==========================================
    // 🌟 ส่วนของหัวหน้างาน (MSS - Manager Approvals)
    // ==========================================

    // แสดงรายการคำร้องที่รออนุมัติ (เฉพาะลูกน้องในทีม)
    public function approvals()
    {
        Gate::authorize('is-manager');
        $user = auth()->user();

        $requests = \App\Models\AttendanceRequest::whereHas('employee', function($q) use ($user) {
            $q->where('manager_id', $user->employee->id);
        })->where('status', 'Pending')->orderBy('created_at', 'asc')->paginate(10);

        return view('attendances.approvals', compact('requests'));
    }

    // อัปเดตสถานะคำร้อง (Approve / Reject)
    public function updateStatus(Request $request, $id)
    {
        Gate::authorize('is-manager');

        $req = \App\Models\AttendanceRequest::findOrFail($id);
        $status = $request->status;

        // 1. อัปเดตสถานะในตารางคำร้อง
        $req->update([
            'status' => $status,
            'approved_by' => auth()->user()->employee->id
        ]);

        // 2. ถ้า "อนุมัติ" ให้นำเวลาไปบันทึก/อัปเดต ในตารางลงเวลาจริง (attendances)
        if ($status === 'Approved') {
            // ค้นหาว่าวันนั้นเคยมีประวัติลงเวลาไหม ถ้าไม่มีให้สร้างใหม่ (firstOrNew)
            $attendance = Attendance::firstOrNew([
                'employee_id' => $req->employee_id,
                'work_date' => $req->work_date
            ]);

            // อัปเดตเฉพาะเวลาที่มีการขอแก้มา (ถ้าช่องไหนว่างแปลว่าไม่ได้ขอแก้)
            if ($req->requested_clock_in) {
                $attendance->clock_in = $req->requested_clock_in;
            }
            if ($req->requested_clock_out) {
                $attendance->clock_out = $req->requested_clock_out;
            }
            
            $attendance->status = 'Present (แก้ไขแล้ว)'; 
            $attendance->save();
        }

        $actionText = $status == 'Approved' ? '✅ อนุมัติ' : '❌ ปฏิเสธ';
        return redirect()->back()->with('success', "{$actionText} คำร้องขอแก้ไขเวลาเรียบร้อยแล้ว");
    }
    
    // ==========================================
    // 🌟 ส่วนของ HR / Admin (Attendance Report)
    // ==========================================

    public function report(Request $request)
    {
        // 🔒 ล็อคสิทธิ์: ต้องเป็น HR หรือ Admin เท่านั้น (ตามสิทธิ์ view-all-employees ที่เราเคยสร้างไว้)
        Gate::authorize('view-all-employees');

        // รับค่าวันที่ค้นหา (ถ้าไม่เลือกให้ใช้วันนี้) และคำค้นหา
        $date = $request->input('date', Carbon::today()->toDateString());
        $search = $request->input('search');

        // 1. ค้นหาพนักงานที่ Active ทั้งหมด
        $query = \App\Models\Employee::with('department', 'position')->where('status', 'Active');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('employee_code', 'LIKE', "%{$search}%");
            });
        }

        // แบ่งหน้าทีละ 15 คน
        $employees = $query->paginate(15)->withQueryString();

        // 2. ดึงข้อมูลการลงเวลา "เฉพาะของพนักงานในหน้านี้" และ "เฉพาะวันที่เลือก"
        $attendances = Attendance::where('work_date', $date)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->keyBy('employee_id'); // จัดกลุ่มด้วย ID พนักงานเพื่อง่ายต่อการค้นหา

        // 3. คำนวณสรุปสถิติของวันนั้น
        $totalEmployees = \App\Models\Employee::where('status', 'Active')->count();
        $presentCount = Attendance::where('work_date', $date)->count();
        $absentCount = $totalEmployees - $presentCount;

        return view('attendances.report', compact('employees', 'attendances', 'date', 'search', 'totalEmployees', 'presentCount', 'absentCount'));
    }

}