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
// 👑 โซน 1: เจ้าของระบบ RotiSoft (Super Admin)
// ==========================================
Route::middleware(['auth', 'can:is-super-admin'])->group(function () {
    Route::resource('companies', App\Http\Controllers\CompanyController::class);
});

// ==========================================
// 🏢 โซน 2: แอดมินบริษัท (Tenant Admin) - ตั้งค่านโยบาย
// ==========================================
Route::middleware(['auth', 'can:is-tenant-admin'])->group(function () {
    // โครงสร้างองค์กร
    Route::resource('departments', App\Http\Controllers\DepartmentController::class);
    Route::resource('positions', App\Http\Controllers\PositionController::class);
    
    // ตั้งค่าพื้นฐานบริษัท
    Route::resource('holidays', \App\Http\Controllers\HolidayController::class);
    // Route::resource('leave-types', \App\Http\Controllers\LeaveTypeController::class); // ถ้ามี Controller
    
    // ตั้งค่า OT
    Route::get('/admin/ot-settings', [App\Http\Controllers\OtSettingController::class, 'index'])->name('ot-settings.index');
    Route::post('/admin/ot-settings', [App\Http\Controllers\OtSettingController::class, 'store'])->name('ot-settings.store');
    Route::put('/admin/ot-settings/{id}', [App\Http\Controllers\OtSettingController::class, 'update'])->name('ot-settings.update');
    Route::delete('/admin/ot-settings/{id}', [App\Http\Controllers\OtSettingController::class, 'destroy'])->name('ot-settings.destroy');
});

// ==========================================
// 👩‍💼 โซน 3: ฝ่ายบุคคล (HR) - ปฏิบัติการรายวัน
// ==========================================
Route::middleware(['auth', 'can:is-hr'])->group(function () {
    // ระบบจัดการพนักงาน (เพิ่ม/แก้ไข/ลบ)
    Route::get('/employees', [App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [App\Http\Controllers\EmployeeController::class, 'create']);
    Route::post('/employees', [App\Http\Controllers\EmployeeController::class, 'store']);
    Route::get('/employees/{id}/edit', [App\Http\Controllers\EmployeeController::class, 'edit']);
    Route::put('/employees/{id}', [App\Http\Controllers\EmployeeController::class, 'update']);
    Route::get('/employees/{id}', [App\Http\Controllers\EmployeeController::class, 'show'])->name('employees.show');

    // บันทึกประวัติส่วนตัวพนักงาน
    Route::post('/employees/{id}/educations', [App\Http\Controllers\EmployeeController::class, 'storeEducation'])->name('employees.educations.store');
    Route::post('/employees/{id}/experiences', [App\Http\Controllers\EmployeeController::class, 'storeExperience'])->name('employees.experiences.store');
    Route::post('/employees/{id}/trainings', [App\Http\Controllers\EmployeeController::class, 'storeTraining'])->name('employees.trainings.store');
    Route::post('/employees/{id}/documents', [App\Http\Controllers\EmployeeController::class, 'storeDocument'])->name('employees.documents.store');

    // ระบบกะการทำงาน (สร้างกะ และ จัดกะให้พนักงาน)
    Route::resource('shifts', App\Http\Controllers\ShiftController::class);
    Route::get('/shift-assignments', [\App\Http\Controllers\ShiftAssignmentController::class, 'index']);
    Route::post('/shift-assignments', [\App\Http\Controllers\ShiftAssignmentController::class, 'store']);

    // ระบบเงินเดือน (Payroll & Salaries)
    Route::get('/salaries', [\App\Http\Controllers\SalaryController::class, 'index']);
    Route::post('/salaries', [\App\Http\Controllers\SalaryController::class, 'store']);
    Route::get('/payrolls', [\App\Http\Controllers\PayrollController::class, 'index']);
    Route::post('/payrolls/calculate', [\App\Http\Controllers\PayrollController::class, 'calculate']);

    // รายงาน
    Route::get('/attendance-report', [App\Http\Controllers\AttendanceController::class, 'report']);
});

// ==========================================
// 👔 โซน 4: หัวหน้างาน (Manager) - ระบบ MSS (อนุมัติ)
// ==========================================
Route::middleware(['auth', 'can:is-manager'])->group(function () {
    // อนุมัติการลา
    Route::get('/leave-approvals', [App\Http\Controllers\LeaveController::class, 'approvals']);
    Route::post('/leaves/{id}/status', [App\Http\Controllers\LeaveController::class, 'updateStatus']);
    
    // อนุมัติลงเวลา
    Route::get('/attendance-approvals', [App\Http\Controllers\AttendanceController::class, 'approvals']);
    Route::post('/attendance-requests/{id}/status', [App\Http\Controllers\AttendanceController::class, 'updateStatus']);
    
    // อนุมัติ OT
    Route::get('/ot-approvals', [App\Http\Controllers\OtRequestController::class, 'approvals']);
    Route::post('/ot-requests/{id}/status', [App\Http\Controllers\OtRequestController::class, 'updateStatus']);
});

// ==========================================
// 🙋‍♂️ โซน 5: พนักงานทุกคน (Staff / ESS) - ล็อกอินแล้วเข้าได้เลย
// ==========================================
Route::middleware(['auth'])->group(function () {
    // หน้าแรก และ โปรไฟล์
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index']);
    Route::post('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword']);
    Route::get('/organization-chart', [\App\Http\Controllers\EmployeeController::class, 'orgChart']);

    // API สำหรับ Dropdown
    Route::get('/get-departments/{company_id}', [App\Http\Controllers\EmployeeController::class, 'getDepartments']);
    Route::get('/get-positions/{department_id}', [App\Http\Controllers\EmployeeController::class, 'getPositions']);
    Route::get('/get-managers/{company_id}', [App\Http\Controllers\EmployeeController::class, 'getManagers']);

    // การลงเวลา (ESS)
    Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index']);
    Route::post('/attendance/clock-in', [App\Http\Controllers\AttendanceController::class, 'clockIn']);
    Route::post('/attendance/clock-out', [App\Http\Controllers\AttendanceController::class, 'clockOut']);
    Route::get('/attendance/request', [App\Http\Controllers\AttendanceController::class, 'createRequest']);
    Route::post('/attendance/request', [App\Http\Controllers\AttendanceController::class, 'storeRequest']);

    // การลางาน (ESS)
    Route::get('/leaves', [App\Http\Controllers\LeaveController::class, 'index']);
    Route::post('/leaves', [App\Http\Controllers\LeaveController::class, 'store']);

    // การขอ OT (ESS)
    Route::get('/ot-requests', [App\Http\Controllers\OtRequestController::class, 'index']);
    Route::post('/ot-requests', [App\Http\Controllers\OtRequestController::class, 'store']);

    // ตารางงาน (ESS)
    Route::get('/my-schedule', [\App\Http\Controllers\ScheduleController::class, 'index']);
    Route::get('/api/schedules', [\App\Http\Controllers\ScheduleController::class, 'getEvents']);
    Route::get('/schedules/table', [\App\Http\Controllers\ScheduleController::class, 'tableView'])->name('schedules.table');

    // สลิปเงินเดือน (ESS)
    Route::get('/my-payslips', [App\Http\Controllers\PayrollController::class, 'myPayslips'])->name('my-payslips');
    Route::get('/my-payslips/{id}/download', [App\Http\Controllers\PayrollController::class, 'downloadPDF'])->name('payrolls.download-pdf');
});

Route::get('/update-hr-role-by-dept', function() {
    // 1. ค้นหาแผนกที่เกี่ยวข้องกับ HR (ปรับแก้คำค้นหาได้ตามชื่อที่คุณตั้งในฐานข้อมูล)
    $hrDepartmentIds = \App\Models\Department::where('name', 'LIKE', '%HR%')
        ->orWhere('name', 'LIKE', '%Human Resource%')
        ->orWhere('name', 'LIKE', '%บุคคล%')
        ->pluck('id');

    if ($hrDepartmentIds->isEmpty()) {
        return "<h1 style='color:red; text-align:center; margin-top:50px;'>
                    ❌ ไม่พบข้อมูลแผนก HR ในระบบ<br>
                    <small style='color:gray; font-size: 20px;'>(กรุณาตรวจสอบชื่อแผนกในเมนูจัดการแผนกอีกครั้ง)</small>
                </h1>";
    }

    // 2. ดึงข้อมูลพนักงานที่อยู่ในแผนก HR
    $employees = \App\Models\Employee::whereIn('department_id', $hrDepartmentIds)->get();
    
    $updateCount = 0;

    // 3. วนลูปอัปเดต Role ในตาราง Users
    foreach ($employees as $emp) {
        if ($emp->user && $emp->user->role === 'staff') { // อัปเดตเฉพาะคนที่ยังเป็น staff
            $emp->user->update(['role' => 'hr']);
            $updateCount++;
        }
    }

    return "<h1 style='color:green; text-align:center; margin-top:50px;'>
                ✅ ปรับสิทธิ์พนักงานแผนก HR เรียบร้อย!<br>
                <small style='color:gray; font-size: 20px;'>อัปเดตสิทธิ์จาก staff เป็น hr จำนวน: {$updateCount} บัญชี</small>
            </h1>";
});