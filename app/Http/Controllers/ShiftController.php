<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\Gate;

class ShiftController extends Controller
{
    public function index()
    {
        Gate::authorize('is-hr');

        $shifts = Shift::orderBy('start_time', 'asc')->get();
        return view('shifts.index', compact('shifts'));
    }

    public function store(Request $request)
    {
        Gate::authorize('is-hr');

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

        // 🌟 ยัด company_id ของผู้ใช้ที่ล็อกอินเข้าไปด้วย ตรงๆ เลย
        $validated['company_id'] = auth()->user()->company_id;

        Shift::create($validated);

        return redirect()->back()->with('success', 'สร้างกะการทำงานใหม่เรียบร้อยแล้ว ✅');
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('is-hr');

        $shift = Shift::findOrFail($id);

        $validated = $request->validate([
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

    public function destroy($id)
    {
        Gate::authorize('is-hr');
        Shift::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'ลบกะการทำงานเรียบร้อยแล้ว 🗑️');
    }
}