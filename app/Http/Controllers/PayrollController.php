<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Barrier; // หรือใช้ชื่อเต็มตามด้านล่าง
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    // หน้าหลักสำหรับเลือกรอบบิล และแสดงผลการรันเงินเดือน
    public function index(Request $request)
    {
        Gate::authorize('edit-employees');

        // ค่าเริ่มต้นคือเดือนปัจจุบัน (เช่น 2026-04)
        $period = $request->input('period', Carbon::now()->format('Y-m'));

        // ดึงข้อมูลเงินเดือนของเดือนที่เลือกมาแสดง
        $payrolls = Payroll::with('employee.department')
                           ->where('period', $period)
                           ->get();

        return view('payrolls.run', compact('period', 'payrolls'));
    }

    // ฟังก์ชันรันเงินเดือน (ประมวลผลคำนวณ)
    public function calculate(Request $request)
    {
        Gate::authorize('edit-employees');
        
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

        // 🌟 ใช้ Transaction เพื่อป้องกันข้อมูลค้างหากเกิด Error ระหว่างทาง
        \DB::transaction(function () use ($employees, $period, $startOfMonth, $endOfMonth, $config, &$count) {
            
            foreach ($employees as $emp) {
                $baseSalary = $emp->salary->base_salary;
                $dailyRate = $baseSalary / $config['work_days_per_month'];
                $hourlyRate = $dailyRate / $config['work_hours_per_day'];

                // เช็คระดับพนักงาน
                $isMD = is_null($emp->manager_id);
                $isManager = Employee::where('manager_id', $emp->id)->exists();
                $role = $isMD ? 'MD' : ($isManager ? 'Manager' : 'Staff');

                // 🌟 [STEP 1] ลบข้อมูลเก่าของเดือนนี้ออกก่อน (ถ้ามี) เพื่อป้องกันข้อมูลซ้ำซ้อน
                \App\Models\Payroll::where('employee_id', $emp->id)->where('period', $period)->delete();

                // 🌟 [STEP 2] สร้างหัวสลิปเงินเดือน (Header) ไว้ก่อนเพื่อให้ได้ ID
                $payroll = \App\Models\Payroll::create([
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

                // ตัวแปรสะสมยอด
                $otAmount = 0; 
                $totalDeductionAmount = 0;
                $unpaidLeaveDeduction = 0;
                $allowance = 0;

                // --- 1. หักลาแบบไม่รับเงิน ---
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

                    // บันทึกลง Item รายละเอียด
                    \App\Models\PayrollItem::create([
                        'payroll_id' => $payroll->id,
                        'item_name' => 'หักลาไม่รับเงิน',
                        'item_type' => 'Deduction',
                        'amount' => $deduction,
                        'description' => "ลาประเภท {$leave->leaveType->name} จำนวน {$leaveDays} วัน",
                    ]);
                }

                // --- 2. เฉพาะระดับ Staff (OT & Attendance) ---
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
                            
                            // คำนวณ OT
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

                                    // 🌟 บันทึก OT Detail (ใช้ $payroll->id ได้แล้ว)
                                    \App\Models\PayrollOtDetail::create([
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

                            // คำนวณมาสาย
                            $shiftIn = Carbon::parse($schedule->expected_clock_in);
                            $actualIn = Carbon::parse($attendance->clock_in);
                            $lateMins = $shiftIn->diffInMinutes($actualIn, false);

                            if ($lateMins > $config['grace_period_mins']) {
                                $lateAmt = $lateMins * ($hourlyRate / 60);
                                \App\Models\PayrollItem::create([
                                    'payroll_id' => $payroll->id,
                                    'item_name' => 'หักมาสาย',
                                    'item_type' => 'Deduction',
                                    'amount' => $lateAmt,
                                    'description' => "สาย {$lateMins} นาที เมื่อวันที่ {$schedule->work_date}",
                                ]);
                                $totalDeductionAmount += $lateAmt;
                            }

                        } else if (!$schedule->is_day_off) {
                            // ขาดงาน
                            $absentAmt = ($dailyRate);
                            \App\Models\PayrollItem::create([
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

                // --- 3. ประกันสังคม & ภาษี ---
                $socialSecurity = min($baseSalary * $config['social_security_percent'], $config['social_security_max']);
                $taxAmount = $baseSalary * 0.03; 

                // --- 4. สรุปยอดและ UPDATE กลับไปยัง Header ---
                $totalDeductionsAll = $totalDeductionAmount + $unpaidLeaveDeduction;
                $netSalary = ($baseSalary + $otAmount + $allowance) - ($totalDeductionsAll + $taxAmount + $socialSecurity);

                $payroll->update([
                    'ot_amount' => $otAmount,
                    'late_deduction' => $totalDeductionsAll, // ในตารางอาจชื่อนี้แต่เราเก็บยอดหักรวม
                    'tax_amount' => $taxAmount,
                    'social_security' => $socialSecurity,
                    'net_salary' => $netSalary,
                ]);

                $count++;
            }
        });

        return redirect('/payrolls?period=' . $period)->with('success', "🎉 คำนวณเงินเดือนรอบ {$period} สำเร็จ! ประมวลผลจำนวน {$count} รายการ");
    }

    // ฟังก์ชันสำหรับพนักงานดูสลิปเงินเดือนตัวเอง (ESS)
    public function myPayslips()
    {
        $user = auth()->user();

        // ตรวจสอบว่าเป็นพนักงานหรือไม่
        if (!$user->employee) {
            return redirect('/dashboard')->with('error', 'ไม่พบข้อมูลพนักงาน');
        }

        // ดึงข้อมูลเงินเดือนเฉพาะของพนักงานคนนี้ เรียงจากเดือนล่าสุดไปเก่าสุด
        $payrolls = Payroll::with(['items', 'otDetails'])
            ->where('employee_id', $user->employee->id)
            // .where('status', 'Approved') // 💡 อนาคตสามารถปลดคอมเมนต์ตรงนี้ เพื่อให้เห็นเฉพาะยอดที่ HR อนุมัติแล้ว
            ->orderBy('period', 'desc')
            ->paginate(12); // ดูย้อนหลังได้หน้าละ 12 เดือน (1 ปี)

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

        // 🌟 [STEP 1] สร้าง Instance ของ PDF และเก็บไว้ในตัวแปร $pdf ก่อน
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payrolls.pdf_template', $data);

        // 🌟 [STEP 2] ตั้งค่า Option ผ่านตัวแปร $pdf ที่เราสร้างขึ้น
        $pdf->setOption([
            'fontDir' => storage_path('fonts'),
            'fontCache' => storage_path('fonts'),
            'tempDir' => storage_path('fonts'),
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'THSarabunNew' // กำหนดฟอนต์เริ่มต้นเป็นภาษาไทย
        ]);

        // 🌟 [STEP 3] สั่งดาวน์โหลด
        return $pdf->download('Payslip_' . $payroll->period . '.pdf');
    }
    
}