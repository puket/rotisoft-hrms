<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class HolidayController extends Controller
{
    // แสดงรายการวันหยุด (แยกตามปี)
    public function index(Request $request)
    {
        // รับค่าปีที่ต้องการดู (ถ้าไม่ส่งมา ให้ใช้ปีปัจจุบัน)
        $year = $request->year ?? date('Y');
        
        // ดึงวันหยุดของปีที่เลือก เรียงตามวันที่
        $holidays = Holiday::whereYear('date', $year)
                        ->orderBy('date', 'asc')
                        ->get();

        return view('admin.holidays.index', compact('holidays', 'year'));
    }

    // บันทึกวันหยุดใหม่
    public function store(Request $request)
    {
        Gate::authorize('edit-employees'); // เฉพาะ HR/Admin

        $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ], [
            'date.required' => 'กรุณาเลือกวันที่',
            'date.unique' => 'วันที่นี้ถูกตั้งเป็นวันหยุดไปแล้ว',
            'name.required' => 'กรุณากรอกชื่อวันหยุด'
        ]);

        Holiday::create($request->all());

        return redirect()->back()->with('success', 'เพิ่มวันหยุดบริษัทเรียบร้อยแล้ว ✅');
    }

    // อัปเดตข้อมูลวันหยุด
    public function update(Request $request, $id)
    {
        Gate::authorize('edit-employees');

        $holiday = Holiday::findOrFail($id);

        $request->validate([
            'date' => 'required|date|unique:holidays,date,' . $holiday->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $holiday->update($request->all());

        return redirect()->back()->with('success', 'อัปเดตข้อมูลวันหยุดเรียบร้อยแล้ว ✅');
    }

    // ลบวันหยุด
    public function destroy($id)
    {
        Gate::authorize('edit-employees');
        Holiday::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'ลบวันหยุดออกจากระบบเรียบร้อยแล้ว 🗑️');
    }
}