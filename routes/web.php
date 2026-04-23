<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\OtRequestController;
use App\Http\Controllers\ShiftController;

// 1. ปิดระบบ Register
Auth::routes(['register' => false]);

// 2. หน้าแรกให้เด้งไปที่ Login เสมอ
Route::get('/', function () {
    return redirect('/login');
});

// ==========================================
// 🔒 โซนหวงห้าม: ต้อง Login เท่านั้นถึงจะเข้าได้
// ==========================================
// ตรวจสอบใน routes/web.php
Route::middleware(['auth', 'can:access-admin'])->group(function () {
    // ต้องมี ->name(...) ต่อท้ายแบบนี้เป๊ะๆ นะครับ
    Route::get('/admin/ot-settings', [App\Http\Controllers\OtSettingController::class, 'index'])->name('ot-settings.index');
    Route::post('/admin/ot-settings', [App\Http\Controllers\OtSettingController::class, 'store'])->name('ot-settings.store');
    
    Route::put('/admin/ot-settings/{id}', [App\Http\Controllers\OtSettingController::class, 'update'])->name('ot-settings.update');
    Route::delete('/admin/ot-settings/{id}', [App\Http\Controllers\OtSettingController::class, 'destroy'])->name('ot-settings.destroy');

});

Route::middleware(['auth', 'can:access-admin'])->group(function () {
    Route::get('/admin/ot-settings', [App\Http\Controllers\OtSettingController::class, 'index'])->name('ot-settings.index');
    Route::post('/admin/ot-settings', [App\Http\Controllers\OtSettingController::class, 'store'])->name('ot-settings.store');
    
    // 🌟 เพิ่ม 2 บรรทัดนี้
    Route::put('/admin/ot-settings/{id}', [App\Http\Controllers\OtSettingController::class, 'update'])->name('ot-settings.update');
    Route::delete('/admin/ot-settings/{id}', [App\Http\Controllers\OtSettingController::class, 'destroy'])->name('ot-settings.destroy');
});
Route::middleware(['auth', 'can:edit-employees'])->group(function () {
    
    // บันทึกประวัติการศึกษา
    Route::post('/employees/{id}/educations', [App\Http\Controllers\EmployeeController::class, 'storeEducation'])->name('employees.educations.store');
    // บันทึกประวัติการทำงาน
    Route::post('/employees/{id}/experiences', [App\Http\Controllers\EmployeeController::class, 'storeExperience']);
    // บันทึกประวัติการฝึกอบรม
    Route::post('/employees/{id}/trainings', [App\Http\Controllers\EmployeeController::class, 'storeTraining']);

    // บันทึกเอกสารแนบและอัปโหลดไฟล์
    Route::post('/employees/{id}/documents', [App\Http\Controllers\EmployeeController::class, 'storeDocument']);

    // 🌟 เพิ่มกลุ่ม Route สำหรับจัดการกะการทำงาน (Shifts)
    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
    Route::put('/shifts/{id}', [ShiftController::class, 'update'])->name('shifts.update');
    Route::delete('/shifts/{id}', [ShiftController::class, 'destroy'])->name('shifts.destroy');

    // ระบบตั้งค่าวันหยุดบริษัท (HR/Admin)
    Route::get('/holidays', [\App\Http\Controllers\HolidayController::class, 'index'])->name('holidays.index');
    Route::post('/holidays', [\App\Http\Controllers\HolidayController::class, 'store'])->name('holidays.store');
    Route::put('/holidays/{id}', [\App\Http\Controllers\HolidayController::class, 'update'])->name('holidays.update');
    Route::delete('/holidays/{id}', [\App\Http\Controllers\HolidayController::class, 'destroy'])->name('holidays.destroy');

});


Route::middleware(['auth'])->group(function () {

    // หน้า Dashboard (หลังล็อกอิน)
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // หน้า ระบบพนักงาน
    Route::get('/employees', [EmployeeController::class, 'index']);
    // หน้าฟอร์มเพิ่มพนักงานใหม่
    Route::get('/employees/create', [EmployeeController::class, 'create']);
    // บันทึกข้อมูลพนักงานใหม่ลงฐานข้อมูล
    Route::post('/employees', [EmployeeController::class, 'store']);
    
    Route::get('/employees/{id}', [EmployeeController::class, 'show']);
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit']);
    Route::put('/employees/{id}', [EmployeeController::class, 'update']);

    //หน้า ระบบลางาน
    Route::get('/leaves', [LeaveController::class, 'index']);
    Route::post('/leaves', [LeaveController::class, 'store']);
    // หน้าแสดงรายการใบลาที่รออนุมัติ (สำหรับหัวหน้า)
    Route::get('/leave-approvals', [LeaveController::class, 'approvals']);
    // จัดการเปลี่ยนสถานะใบลา (Approve / Reject)
    Route::post('/leaves/{id}/status', [LeaveController::class, 'updateStatus']);

    // หน้า My Profile และ เปลี่ยนรหัสผ่าน
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile/password', [ProfileController::class, 'updatePassword']);

    // ระบบบันทึกเวลาเข้า-ออกงาน (ESS)
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut']);
        
    // ส่งคำร้องขอแก้ไขเวลา/ลืมลงเวลา
    Route::get('/attendance/request', [AttendanceController::class, 'createRequest']);
    Route::post('/attendance/request', [AttendanceController::class, 'storeRequest']);

    // หน้าจัดการคำร้องขอแก้ไขเวลา (MSS)
    Route::get('/attendance-approvals', [AttendanceController::class, 'approvals']);
    Route::post('/attendance-requests/{id}/status', [AttendanceController::class, 'updateStatus']);    

    // รายงานสรุปการลงเวลา (HR/Admin)
    Route::get('/attendance-report', [AttendanceController::class, 'report']);

    // โครงสร้างองค์กร (Organization Chart)
    Route::get('/organization-chart', [\App\Http\Controllers\EmployeeController::class, 'orgChart']);

    // ระบบจัดการกะการทำงาน (HR/Admin)
    //Route::get('/shifts', [\App\Http\Controllers\ShiftController::class, 'index']);
    //Route::post('/shifts', [\App\Http\Controllers\ShiftController::class, 'store']);
    //Route::delete('/shifts/{id}', [\App\Http\Controllers\ShiftController::class, 'destroy']);
    //Route::put('/shifts/{id}', [App\Http\Controllers\ShiftController::class, 'update'])->name('shifts.update');
    
    // ระบบมอบหมายกะการทำงาน (HR/Admin)
    Route::get('/shift-assignments', [\App\Http\Controllers\ShiftAssignmentController::class, 'index']);
    Route::post('/shift-assignments', [\App\Http\Controllers\ShiftAssignmentController::class, 'store']);
    
    // ปฏิทินตารางทำงาน (My Schedule แบบ Calendar)
    Route::get('/my-schedule', [\App\Http\Controllers\ScheduleController::class, 'index']);
    Route::get('/api/schedules', [\App\Http\Controllers\ScheduleController::class, 'getEvents']);
    
    // 🌟 เพิ่มบรรทัดนี้: หน้าตารางการทำงาน (แบบรายการ)
    Route::get('/schedules/table', [\App\Http\Controllers\ScheduleController::class, 'tableView'])->name('schedules.table');

    // ==========================================
    // ระบบขอทำล่วงเวลา (OT Plan)
    // ==========================================
    // ฝั่งพนักงาน (ESS)
    Route::get('/ot-requests', [OtRequestController::class, 'index']);
    Route::post('/ot-requests', [OtRequestController::class, 'store']);
    
    // ฝั่งหัวหน้างาน (MSS)
    Route::get('/ot-approvals', [OtRequestController::class, 'approvals']);
    Route::post('/ot-requests/{id}/status', [OtRequestController::class, 'updateStatus']);

    // ระบบตั้งค่าฐานเงินเดือน (HR/Admin)
    Route::get('/salaries', [\App\Http\Controllers\SalaryController::class, 'index']);
    Route::post('/salaries', [\App\Http\Controllers\SalaryController::class, 'store']);

    // ระบบรันเงินเดือน (Payroll Run)
    Route::get('/payrolls', [\App\Http\Controllers\PayrollController::class, 'index']);
    Route::post('/payrolls/calculate', [\App\Http\Controllers\PayrollController::class, 'calculate']);

    // หน้าดูสลิปเงินเดือนของพนักงาน (ESS)
    Route::get('/my-payslips', [App\Http\Controllers\PayrollController::class, 'myPayslips'])->name('my-payslips');



});

// ดาวน์โหลดสลิป PDF (สำหรับพนักงาน)
Route::get('/my-payslips/{id}/download', [App\Http\Controllers\PayrollController::class, 'downloadPDF'])
    ->name('payrolls.download-pdf');
    
Route::get('/debug-manager', function() {
    $user = auth()->user();
    
    if (!$user->employee) {
        return "บัญชีนี้ไม่มีข้อมูลพนักงาน (Employee) ผูกอยู่เลยครับ!";
    }

    $subordinates = \App\Models\Employee::where('manager_id', $user->employee->id)->get();

    return response()->json([
        '1. ผู้ใช้งานที่ล็อกอิน' => $user->name,
        '2. User ID (ตาราง users)' => $user->id,
        '3. Employee ID (ตาราง employees)' => $user->employee->id,
        '4. จำนวนลูกน้องที่หาเจอ' => $subordinates->count(),
        '5. รายชื่อลูกน้อง' => $subordinates->pluck('first_name'),
        '6. ผลการเช็คสิทธิ์ is-manager' => \Illuminate\Support\Facades\Gate::allows('is-manager') ? 'ผ่าน (True)' : 'ไม่ผ่าน (False)'
    ], 200, ['Content-Type' => 'application/json;charset=UTF-8'], JSON_UNESCAPED_UNICODE);
});

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');