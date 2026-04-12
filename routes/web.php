<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;

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

    // หน้ารายชื่อพนักงาน
    Route::get('/employees', [EmployeeController::class, 'index']);
    
    // 💡 ทริค: ในอนาคตถ้าเรามีหน้าสร้างพนักงาน (/employees/create) 
    // หรือหน้าตั้งค่าแผนก (/departments) เราก็จะเอาโค้ดมาพิมพ์ต่อในกรอบนี้ได้เลยครับ

    // หน้าฟอร์มเพิ่มพนักงานใหม่
    Route::get('/employees/create', [EmployeeController::class, 'create']);
    
    // บันทึกข้อมูลพนักงานใหม่ลงฐานข้อมูล
    Route::post('/employees', [EmployeeController::class, 'store']);


    // บันทึกข้อมูลพนักงาน ลางาน
    Route::get('/leaves', [LeaveController::class, 'index']);
    Route::post('/leaves', [LeaveController::class, 'store']);

});