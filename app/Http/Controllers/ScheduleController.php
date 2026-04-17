<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use Illuminate\Support\Facades\Gate;

class ScheduleController extends Controller
{
    // แสดงหน้าจอประทิทิน
    public function index(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect('/home')->with('error', 'บัญชีนี้ยังไม่ได้ผูกกับข้อมูลพนักงาน');
        }

        // ค่าเริ่มต้น: ดึง ID ของตัวเอง
        $viewEmployeeId = $employee->id;

        // ถ้ามีสิทธิ์ HR (edit-employees) และมีการเลือกพนักงานคนอื่น ให้เปลี่ยน ID ที่จะดู
        if ($request->has('employee_id') && Gate::allows('edit-employees')) {
            $viewEmployeeId = $request->employee_id;
        }

        $viewEmployee = Employee::findOrFail($viewEmployeeId);

        // ดึงรายชื่อพนักงานทั้งหมดมาให้ HR เลือก (ถ้าเป็นพนักงานปกติ จะได้เป็น Array ว่าง)
        $employeesList = [];
        if (Gate::allows('edit-employees')) {
            $employeesList = Employee::where('status', 'Active')->get();
        }

        return view('schedules.my-schedule', compact('viewEmployee', 'employeesList'));
    }

    // ฟังก์ชันนี้สำหรับส่งข้อมูลให้ FullCalendar นำไปวาดลงปฏิทิน
    public function getEvents(Request $request)
    {
        $employeeId = $request->employee_id;
        
        // FullCalendar จะส่งวันที่ start และ end มาให้อัตโนมัติเวลาเรากดเปลี่ยนเดือน
        $start = $request->start; 
        $end = $request->end;

        $schedules = EmployeeSchedule::with('shift')
            ->where('employee_id', $employeeId)
            ->whereBetween('work_date', [$start, $end])
            ->get();

        $events = [];
        foreach ($schedules as $schedule) {
            if ($schedule->is_day_off) {
                // ถ้าเป็นวันหยุด
                $events[] = [
                    'title' => '🏖️ วันหยุด (Day Off)',
                    'start' => $schedule->work_date,
                    'color' => '#6c757d', // สีเทา
                    'allDay' => true
                ];
            } else {
                // ถ้าเป็นวันทำงาน
                $shiftName = $schedule->shift ? $schedule->shift->name : 'ไม่ได้กำหนดกะ';
                $clockIn = $schedule->expected_clock_in ? \Carbon\Carbon::parse($schedule->expected_clock_in)->format('H:i') : '';
                $clockOut = $schedule->expected_clock_out ? \Carbon\Carbon::parse($schedule->expected_clock_out)->format('H:i') : '';
                
                $events[] = [
                    'title' => "💼 {$shiftName} ({$clockIn}-{$clockOut})",
                    'start' => $schedule->work_date,
                    'color' => '#0d6efd', // สีน้ำเงิน
                    'allDay' => true
                ];
            }
        }

        return response()->json($events);
    }
}