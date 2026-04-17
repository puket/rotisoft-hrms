<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\Gate;

class ShiftController extends Controller
{
    // แสดงหน้าจัดการกะและรายการกะทั้งหมด
    public function index()
    {
        // 🔒 ล็อคสิทธิ์: เฉพาะ HR เท่านั้น (ใช้สิทธิ์ edit-employees ที่เราเคยสร้างไว้)
        Gate::authorize('edit-employees');

        $shifts = Shift::orderBy('start_time', 'asc')->get();
        return view('shifts.index', compact('shifts'));
    }

    // บันทึกข้อมูลกะใหม่
    public function store(Request $request)
    {
        Gate::authorize('edit-employees');

        $request->validate([
            'name' => 'required|string|max:255|unique:shifts,name',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ], [
            'name.required' => 'กรุณากรอกชื่อกะการทำงาน',
            'name.unique' => 'ชื่อกะนี้มีในระบบแล้ว กรุณาใช้ชื่ออื่น',
            'start_time.required' => 'กรุณาระบุเวลาเริ่มงาน',
            'end_time.required' => 'กรุณาระบุเวลาเลิกงาน',
        ]);

        Shift::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->back()->with('success', 'สร้างกะการทำงานใหม่เรียบร้อยแล้ว ✅');
    }

    // ลบกะการทำงาน (สำหรับอนาคตถ้าต้องการใช้)
    public function destroy($id)
    {
        Gate::authorize('edit-employees');
        Shift::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'ลบกะการทำงานเรียบร้อยแล้ว 🗑️');
    }
}