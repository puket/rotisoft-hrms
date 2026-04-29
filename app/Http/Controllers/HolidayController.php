<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? date('Y');
        
        $holidays = Holiday::whereYear('date', $year)
                        ->orderBy('date', 'asc')
                        ->get();

        return view('admin.holidays.index', compact('holidays', 'year'));
    }

    public function store(Request $request)
    {
        // 🌟 อัปเกรด Gate
        Gate::authorize('is-tenant-admin');

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

    public function update(Request $request, $id)
    {
        // 🌟 อัปเกรด Gate
        Gate::authorize('is-tenant-admin');

        $holiday = Holiday::findOrFail($id);

        $request->validate([
            'date' => 'required|date|unique:holidays,date,' . $holiday->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $holiday->update($request->all());

        return redirect()->back()->with('success', 'อัปเดตข้อมูลวันหยุดเรียบร้อยแล้ว ✅');
    }

    public function destroy($id)
    {
        // 🌟 อัปเกรด Gate
        Gate::authorize('is-tenant-admin');
        Holiday::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'ลบวันหยุดออกจากระบบเรียบร้อยแล้ว 🗑️');
    }
}