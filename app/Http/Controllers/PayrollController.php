<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('is-hr');

        $period = $request->input('period', Carbon::now()->format('Y-m'));

        $payrolls = Payroll::with('employee.department')
                           ->where('period', $period)
                           ->get();

        return view('payrolls.run', compact('period', 'payrolls'));
    }

    public function calculate(Request $request)
    {
        Gate::authorize('is-hr');
        
        $request->validate(['period' => 'required|date_format:Y-m']);
        $period = $request->period;

        $startOfMonth = Carbon::createFromFormat('Y-m', $period)->startOfMonth()->toDateString();
        $endOfMonth = Carbon::createFromFormat('Y-m', $period)->endOfMonth()->toDateString();

        $config = [
            'grace_period_mins' => 15,
            'work_days_per_month' => 30,
            'work_hours_per_day' => 8,
            'social_security_percent' => 0.05,
            'social_security_max' => 750
        ];

        $employees = Employee::with('salary')->where('status', 'Active')->whereHas('salary')->get();

        if ($employees->count() == 0) {
            return redirect()->back()->with('error', 'ไม่พบพนักงานที่มีการตั้งค่าฐานเงินเดือน');
        }

        $count = 0;

        \DB::transaction(function () use ($employees, $period, $startOfMonth, $endOfMonth, $config, &$count) {
            
            // 🌟 วนลูปพนักงาน เรามี $emp->company_id ให้ใช้ตรงนี้แหละครับ!
            foreach ($employees as $emp) {
                $baseSalary = $emp->salary->base_salary;
                $dailyRate = $baseSalary / $config['work_days_per_month'];
                $hourlyRate = $dailyRate / $config['work_hours_per_day'];

                $isMD = is_null($emp->manager_id);
                $isManager = Employee::where('manager_id', $emp->id)->exists();
                $role = $isMD ? 'MD' : ($isManager ? 'Manager' : 'Staff');

                \App\Models\Payroll::where('employee_id', $emp->id)->where('period', $period)->delete();

                // 🌟 1. ยัด company_id ลง Header ของสลิปเงินเดือน
                $payroll = \App\Models\Payroll::create([
                    'company_id' => $emp->company_id, // 👈 เพิ่มตรงนี้
                    'employee_id' => $emp->id,
                    'period' => $period,
                    'base_salary' => $baseSalary,
                    'ot_amount' => 0,
                    'allowance' => 0,
                    'late_deduction' => 0,
                    'tax_amount' => 0,
                    'social_security' => 0,
                    'net_salary' => 0,
                    'status' => 'Draft'
                ]);

                $otAmount = 0; 
                $totalDeductionAmount = 0;
                $unpaidLeaveDeduction = 0;
                $allowance = 0;

                $unpaidLeaves = \App\Models\Leave::with('leaveType')
                    ->where('employee_id', $emp->id)
                    ->where('status', 'Approved')
                    ->whereHas('leaveType', function($query) {
                        $query->where('is_unpaid', true); 
                    })
                    ->where(function($query) use ($startOfMonth, $endOfMonth) {
                        $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                              ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth]);
                    })->get();

                foreach($unpaidLeaves as $leave) {
                    $leaveStart = Carbon::parse($leave->start_date);
                    $leaveEnd = Carbon::parse($leave->end_date);
                    $leaveDays = $leaveStart->diffInDays($leaveEnd) + 1; 
                    $deduction = ($leaveDays * $dailyRate);
                    $unpaidLeaveDeduction += $deduction;

                    // 🌟 2. ยัด company_id ลงรายการหักเงิน
                    \App\Models\PayrollItem::create([
                        'company_id' => $emp->company_id, // 👈 เพิ่มตรงนี้
                        'payroll_id' => $payroll->id,
                        'item_name' => 'หักลาไม่รับเงิน',
                        'item_type' => 'Deduction',
                        'amount' => $deduction,
                        'description' => "ลาประเภท {$leave->leaveType->name} จำนวน {$leaveDays} วัน",
                    ]);
                }

                if ($role === 'Staff') {
                    $otSetting = \App\Models\OtSetting::where('effective_date', '<=', $endOfMonth)
                                    ->where('is_active', true)
                                    ->orderBy('effective_date', 'desc')->first();

                    $schedules = \App\Models\EmployeeSchedule::where('employee_id', $emp->id)
                                    ->whereBetween('work_date', [$startOfMonth, $endOfMonth])->get();

                    foreach ($schedules as $schedule) {
                        $attendance = \App\Models\Attendance::where('employee_id', $emp->id)
                                        ->where('work_date', $schedule->work_date)->first();

                        if ($attendance && $attendance->clock_in && $attendance->clock_out) {
                            
                            $hasOtPlan = \App\Models\OtRequest::where('employee_id', $emp->id)
                                            ->where('work_date', $schedule->work_date)
                                            ->where('status', 'Approved')->exists();

                            if ($hasOtPlan && $otSetting) {
                                $actualIn = Carbon::parse($attendance->clock_in);
                                $actualOut = Carbon::parse($attendance->clock_out);
                                $shiftIn = Carbon::parse($schedule->expected_clock_in);
                                $shiftOut = Carbon::parse($schedule->expected_clock_out);

                                $dailyPreMins = 0; $dailyPostMins = 0;

                                $otLimitBefore = $shiftIn->copy()->subMinutes($otSetting->break_mins);
                                if ($actualIn->lessThan($otLimitBefore)) {
                                    $dailyPreMins = $actualIn->diffInMinutes($otLimitBefore);
                                }

                                $otStartAfter = $shiftOut->copy()->addMinutes($otSetting->break_mins);
                                if ($actualOut->greaterThan($otStartAfter)) {
                                    $dailyPostMins = $otStartAfter->diffInMinutes($actualOut);
                                }

                                $totalMins = $dailyPreMins + $dailyPostMins;

                                if ($totalMins >= $otSetting->min_ot_mins) {
                                    $rate = $schedule->is_day_off ? $otSetting->holiday_rate : $otSetting->workday_rate;
                                    $dailyAmount = (($totalMins / 60) * $hourlyRate * $rate);

                                    // 🌟 3. ยัด company_id ลงรายการ OT
                                    \App\Models\PayrollOtDetail::create([
                                        'company_id' => $emp->company_id, // 👈 เพิ่มตรงนี้
                                        'payroll_id' => $payroll->id,
                                        'work_date' => $schedule->work_date,
                                        'pre_shift_mins' => $dailyPreMins,
                                        'post_shift_mins' => $dailyPostMins,
                                        'total_hours' => $totalMins / 60,
                                        'multiplier' => $rate,
                                        'amount' => $dailyAmount,
                                    ]);
                                    $otAmount += $dailyAmount;
                                }
                            }

                            $shiftIn = Carbon::parse($schedule->expected_clock_in);
                            $actualIn = Carbon::parse($attendance->clock_in);
                            $lateMins = $shiftIn->diffInMinutes($actualIn, false);

                            if ($lateMins > $config['grace_period_mins']) {
                                $lateAmt = $lateMins * ($hourlyRate / 60);
                                
                                // 🌟 4. ยัด company_id ลงรายการหักมาสาย
                                \App\Models\PayrollItem::create([
                                    'company_id' => $emp->company_id, // 👈 เพิ่มตรงนี้
                                    'payroll_id' => $payroll->id,
                                    'item_name' => 'หักมาสาย',
                                    'item_type' => 'Deduction',
                                    'amount' => $lateAmt,
                                    'description' => "สาย {$lateMins} นาที เมื่อวันที่ {$schedule->work_date}",
                                ]);
                                $totalDeductionAmount += $lateAmt;
                            }

                        } else if (!$schedule->is_day_off) {
                            $absentAmt = ($dailyRate);
                            
                            // 🌟 5. ยัด company_id ลงรายการหักขาดงาน
                            \App\Models\PayrollItem::create([
                                'company_id' => $emp->company_id, // 👈 เพิ่มตรงนี้
                                'payroll_id' => $payroll->id,
                                'item_name' => 'หักขาดงาน',
                                'item_type' => 'Deduction',
                                'amount' => $absentAmt,
                                'description' => "ไม่พบข้อมูลลงเวลาวันที่ {$schedule->work_date}",
                            ]);
                            $totalDeductionAmount += $absentAmt;
                        }
                    }
                }

                $socialSecurity = min($baseSalary * $config['social_security_percent'], $config['social_security_max']);
                $taxAmount = $baseSalary * 0.03; 

                $totalDeductionsAll = $totalDeductionAmount + $unpaidLeaveDeduction;
                $netSalary = ($baseSalary + $otAmount + $allowance) - ($totalDeductionsAll + $taxAmount + $socialSecurity);

                $payroll->update([
                    'ot_amount' => $otAmount,
                    'late_deduction' => $totalDeductionsAll,
                    'tax_amount' => $taxAmount,
                    'social_security' => $socialSecurity,
                    'net_salary' => $netSalary,
                ]);

                $count++;
            }
        });

        return redirect('/payrolls?period=' . $period)->with('success', "🎉 คำนวณเงินเดือนรอบ {$period} สำเร็จ! ประมวลผลจำนวน {$count} รายการ");
    }

    public function myPayslips()
    {
        $user = auth()->user();

        if (!$user->employee) {
            return redirect('/dashboard')->with('error', 'ไม่พบข้อมูลพนักงาน');
        }

        $payrolls = Payroll::with(['items', 'otDetails'])
            ->where('employee_id', $user->employee->id)
            ->orderBy('period', 'desc')
            ->paginate(12);

        return view('payrolls.my_payslips', compact('payrolls'));
    }

    public function downloadPDF($id)
    {
        $payroll = Payroll::with(['employee.department', 'items', 'otDetails'])
            ->where('id', $id)
            ->where('employee_id', auth()->user()->employee->id)
            ->firstOrFail();

        $data = [
            'pr' => $payroll,
            'title' => 'สลิปเงินเดือน'
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payrolls.pdf_template', $data);

        $pdf->setOption([
            'fontDir' => storage_path('fonts'),
            'fontCache' => storage_path('fonts'),
            'tempDir' => storage_path('fonts'),
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'THSarabunNew' 
        ]);

        return $pdf->download('Payslip_' . $payroll->period . '.pdf');
    }
}