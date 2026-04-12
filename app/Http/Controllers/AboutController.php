<?php

namespace App\Http\Controllers; // ตัวพิมพ์ใหญ่เล็กต้องเป๊ะ

use Illuminate\Http\Request;

class AboutController extends Controller // ชื่อ Class ต้องตรงกับชื่อไฟล์
{
    // เพิ่มฟังก์ชันนี้เข้าไปครับ
    public function index()
    {
        return view('about'); // สั่งให้ไปเรียกไฟล์ view ที่ชื่อ about.blade.php
    }
}