<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // เปิดหน้า My Profile
    public function index()
    {
        $user = auth()->user();
        $employee = $user->employee;

        // ถ้าไม่มีข้อมูลพนักงาน ให้เด้งกลับไปหน้า Home
        if (!$employee) {
            return redirect('/home')->with('error', 'ไม่พบข้อมูลพนักงานสำหรับบัญชีนี้');
        }

        return view('profile.index', compact('user', 'employee'));
    }

    // ฟังก์ชันสำหรับเปลี่ยนรหัสผ่าน
    public function updatePassword(Request $request)
    {
        // 1. ตรวจสอบข้อมูลที่กรอกมา
        $request->validate([
            'current_password' => ['required', 'current_password'], // ต้องตรงกับรหัสเดิม
            'new_password' => ['required', 'min:6', 'confirmed'], // ต้องกรอกตรงกัน 2 ช่อง
        ], [
            'current_password.current_password' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง',
            'new_password.confirmed' => 'รหัสผ่านใหม่ทั้ง 2 ช่องไม่ตรงกัน',
            'new_password.min' => 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร',
        ]);

        // 2. อัปเดตรหัสผ่านใหม่ลงฐานข้อมูล
        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว ✅');
    }
}