<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use Illuminate\Support\Facades\Gate;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect('/home')->with('error', 'บัญชีนี้ยังไม่ได้ผูกกับข้อมูลพนักงาน');
        }

        $viewEmployeeId = $employee->id;
        $employeesList = collect(); // ใช้ Collection ว่างไว้ก่อน

        // 🌟 กำหนดรายชื่อพนักงานที่สามารถเลือกดูได้ (Dropdown)
        if (Gate::allows('is-hr')) {
            // HR เห็นทุกคน
            $employeesList = Employee::where('status', 'Active')->get();
        } elseif (Gate::allows('is-manager')) {
            // Manager เห็นตัวเอง และลูกน้องที่รายงานตรง
            $employeesList = Employee::where('status', 'Active')
                ->where(function($q) use ($employee) {
                    $q->where('manager_id', $employee->id)
                      ->orWhere('id', $employee->id);
                })->get();
        }

        // 🌟 ตรวจสอบสิทธิ์เมื่อมีการเลือกดูพนักงานคนอื่น
        if ($request->has('employee_id')) {
            $requestedId = $request->employee_id;
            
            if (Gate::allows('is-hr')) {
                $viewEmployeeId = $requestedId;
            } elseif (Gate::allows('is-manager')) {
                // เช็คว่า ID ที่ขอมา อยู่ในลิสต์ลูกน้องตัวเองหรือไม่
                if ($employeesList->contains('id', $requestedId)) {
                    $viewEmployeeId = $requestedId;
                }
            }
        }

        $viewEmployee = Employee::findOrFail($viewEmployeeId);

        return view('schedules.my-schedule', compact('viewEmployee', 'employeesList'));
    }

    public function getEvents(Request $request)
    {
        $employeeId = $request->employee_id;
        $start = $request->start; 
        $end = $request->end;

        $schedules = EmployeeSchedule::with('shift')
            ->where('employee_id', $employeeId)
            ->whereBetween('work_date', [$start, $end])
            ->get();

        $events = [];
        foreach ($schedules as $schedule) {
            if ($schedule->is_day_off) {
                $events[] = [
                    'title' => '🏖️ วันหยุด (Day Off)',
                    'start' => $schedule->work_date,
                    'color' => '#6c757d', 
                    'allDay' => true
                ];
            } else {
                $shiftName = $schedule->shift ? $schedule->shift->name : 'ไม่ได้กำหนดกะ';
                $clockIn = $schedule->expected_clock_in ? \Carbon\Carbon::parse($schedule->expected_clock_in)->format('H:i') : '';
                $clockOut = $schedule->expected_clock_out ? \Carbon\Carbon::parse($schedule->expected_clock_out)->format('H:i') : '';
                
                $events[] = [
                    'title' => "💼 {$shiftName} ({$clockIn}-{$clockOut})",
                    'start' => $schedule->work_date,
                    'color' => '#0d6efd',
                    'allDay' => true
                ];
            }
        }

        return response()->json($events);
    }

    public function tableView(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect('/home')->with('error', 'บัญชีนี้ยังไม่ได้ผูกกับข้อมูลพนักงาน');
        }

        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');
        
        $viewEmployeeId = $employee->id;
        $employeesList = collect();

        // 🌟 กำหนดรายชื่อพนักงานที่สามารถเลือกดูได้ (Dropdown) - เหมือนหน้า index
        if (Gate::allows('is-hr')) {
            $employeesList = Employee::where('status', 'Active')->orderBy('first_name')->get();
        } elseif (Gate::allows('is-manager')) {
            $employeesList = Employee::where('status', 'Active')
                ->where(function($q) use ($employee) {
                    $q->where('manager_id', $employee->id)
                      ->orWhere('id', $employee->id);
                })->orderBy('first_name')->get();
        }

        // 🌟 ตรวจสอบสิทธิ์เมื่อมีการเลือกดูพนักงานคนอื่น
        if ($request->has('employee_id')) {
            $requestedId = $request->employee_id;
            
            if (Gate::allows('is-hr')) {
                $viewEmployeeId = $requestedId;
            } elseif (Gate::allows('is-manager')) {
                if ($employeesList->contains('id', $requestedId)) {
                    $viewEmployeeId = $requestedId;
                }
            }
        }

        $viewEmployee = Employee::findOrFail($viewEmployeeId);

        $schedules = EmployeeSchedule::with('shift')
            ->where('employee_id', $viewEmployeeId)
            ->whereMonth('work_date', $month)
            ->whereYear('work_date', $year)
            ->orderBy('work_date', 'asc')
            ->get();

        return view('schedules.table', compact('viewEmployee', 'schedules', 'employeesList', 'month', 'year'));
    }
}