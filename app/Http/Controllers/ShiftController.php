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

        $validated = $request->validate([
            'shift_code' => 'nullable|string|max:50|unique:shifts,shift_code',
            'name' => 'required|string|max:255|unique:shifts,name',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'normal_work_hours' => 'required|numeric|min:0',
            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time' => 'nullable|date_format:H:i',
            'break_hours' => 'nullable|numeric|min:0',
            'ot_before_start_time' => 'nullable|date_format:H:i',
            'ot_before_end_time' => 'nullable|date_format:H:i',
            'ot_after_start_time' => 'nullable|date_format:H:i',
            'ot_after_end_time' => 'nullable|date_format:H:i',
        ], [
            'name.required' => 'กรุณากรอกชื่อกะการทำงาน',
            'name.unique' => 'ชื่อกะนี้มีในระบบแล้ว กรุณาใช้ชื่ออื่น',
            'start_time.required' => 'กรุณาระบุเวลาเริ่มงาน',
            'end_time.required' => 'กรุณาระบุเวลาเลิกงาน',
            'normal_work_hours.required' => 'กรุณาระบุจำนวนชั่วโมงทำงานปกติ',
        ]);

        // ใช้ข้อมูลที่ผ่านการตรวจสอบแล้วบันทึกลงฐานข้อมูลได้เลย
        Shift::create($validated);

        return redirect()->back()->with('success', 'สร้างกะการทำงานใหม่เรียบร้อยแล้ว ✅');
    }

    // อัปเดตข้อมูลกะการทำงาน
    public function update(Request $request, $id)
    {
        Gate::authorize('edit-employees');

        $shift = Shift::findOrFail($id);

        $validated = $request->validate([
            // อนุญาตให้ใช้ shift_code และ name เดิมของตัวเองได้
            'shift_code' => 'nullable|string|max:50|unique:shifts,shift_code,' . $shift->id,
            'name' => 'required|string|max:255|unique:shifts,name,' . $shift->id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'normal_work_hours' => 'required|numeric|min:0',
            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time' => 'nullable|date_format:H:i',
            'break_hours' => 'nullable|numeric|min:0',
            'ot_before_start_time' => 'nullable|date_format:H:i',
            'ot_before_end_time' => 'nullable|date_format:H:i',
            'ot_after_start_time' => 'nullable|date_format:H:i',
            'ot_after_end_time' => 'nullable|date_format:H:i',
        ], [
            'name.required' => 'กรุณากรอกชื่อกะการทำงาน',
            'name.unique' => 'ชื่อกะนี้มีในระบบแล้ว กรุณาใช้ชื่ออื่น',
            'start_time.required' => 'กรุณาระบุเวลาเริ่มงาน',
            'end_time.required' => 'กรุณาระบุเวลาเลิกงาน',
            'normal_work_hours.required' => 'กรุณาระบุจำนวนชั่วโมงทำงานปกติ',
        ]);

        $shift->update($validated);

        return redirect()->back()->with('success', 'อัปเดตข้อมูลกะการทำงานเรียบร้อยแล้ว ✅');
    }

    // ลบกะการทำงาน (สำหรับอนาคตถ้าต้องการใช้)
    public function destroy($id)
    {
        Gate::authorize('edit-employees');
        Shift::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'ลบกะการทำงานเรียบร้อยแล้ว 🗑️');
    }
    
}