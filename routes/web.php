<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;
// 1. ปิดระบบ Register
Auth::routes(['register' => false]);

// 2. หน้าแรกให้เด้งไปที่ Login เสมอ
Route::get('/', function () {
    return redirect('/login');
});

// ==========================================
// 🔒 โซนหวงห้าม: ต้อง Login เท่านั้นถึงจะเข้าได้
// ==========================================
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
});

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