<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftAssignment;
use App\Models\EmployeeSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class ShiftAssignmentController extends Controller
{
    // หน้าจอแสดงรายชื่อพนักงานและฟอร์มมอบหมายกะ
    public function index()
    {
        Gate::authorize('is-hr');

        $employees = Employee::with('department', 'shift')->where('status', 'Active')->paginate(15);
        $shifts = Shift::orderBy('start_time', 'asc')->get();

        return view('shifts.assign', compact('employees', 'shifts'));
    }

    // ประมวลผลการมอบหมายกะและ Gen ตารางรายวัน
    public function store(Request $request)
    {
        Gate::authorize('is-hr');

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'effective_date' => 'required|date',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $shift = Shift::findOrFail($request->shift_id);
        $effectiveDate = Carbon::parse($request->effective_date);
        
        // 1. บันทึกประวัติการมอบหมายลง History
        ShiftAssignment::create([
            'company_id' => $employee->company_id, // 🌟 1. ยัด company_id ตรงๆ ให้ตารางประวัติ
            'employee_id' => $employee->id,
            'shift_id' => $shift->id,
            'effective_date' => $effectiveDate->toDateString(),
            'assigned_by' => auth()->user()->employee->id ?? null
        ]);

        // 2. อัปเดตกะหลักในโปรไฟล์พนักงาน
        $employee->update(['shift_id' => $shift->id]);

        // 3. Gen ตารางการทำงานรายวัน (ตั้งแต่วันที่มีผล จนถึงสิ้นปี)
        $endOfYear = Carbon::parse($effectiveDate)->endOfYear();
        
        // เคลียร์ตารางงานเก่าที่อาจจะมีอยู่แล้ว (ตั้งแต่วันที่มีผลเป็นต้นไป) เพื่อใส่ของใหม่ทับ
        EmployeeSchedule::where('employee_id', $employee->id)
                        ->where('work_date', '>=', $effectiveDate->toDateString())
                        ->delete();

        // สร้าง Array เพื่อทำการ Bulk Insert
        $schedules = [];
        for ($date = $effectiveDate->copy(); $date->lte($endOfYear); $date->addDay()) {
            // สมมติให้ วันเสาร์-อาทิตย์ เป็นวันหยุดพักผ่อน Default
            $isDayOff = $date->isWeekend(); 

            $schedules[] = [
                'company_id' => $employee->company_id, // 🌟 2. ยัด company_id ตรงๆ ใส่ Array ก่อนทำ Bulk Insert
                'employee_id' => $employee->id,
                'work_date' => $date->toDateString(),
                'shift_id' => $shift->id,
                'is_day_off' => $isDayOff,
                'is_holiday' => false,
                'expected_clock_in' => $isDayOff ? null : $shift->start_time,
                'expected_clock_out' => $isDayOff ? null : $shift->end_time,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // บันทึกลงฐานข้อมูลรวดเดียว
        EmployeeSchedule::insert($schedules);

        return redirect()->back()->with('success', "✅ มอบหมายกะให้ {$employee->first_name} และสร้างตารางล่วงหน้าถึงสิ้นปีเรียบร้อยแล้ว!");
    }
}