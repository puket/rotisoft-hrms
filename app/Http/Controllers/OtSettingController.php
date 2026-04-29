<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OtSetting;
use Illuminate\Support\Facades\Gate;

class OtSettingController extends Controller
{
    public function index()
    {
        // 🌟 อัปเกรด Gate
        Gate::authorize('is-tenant-admin');
        
        $settings = OtSetting::orderBy('effective_date', 'desc')->get();
        return view('admin.ot_settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        // 🌟 อัปเกรด Gate
        Gate::authorize('is-tenant-admin');

        $request->validate([
            'employee_type' => 'required|in:Daily,Monthly',
            'effective_date' => 'required|date',
            'workday_rate' => 'required|numeric|min:1',
            'holiday_rate' => 'required|numeric|min:1',
            'break_mins' => 'required|integer|min:0',
            'min_ot_mins' => 'required|integer|min:0',
        ]);

        OtSetting::create([
            'employee_type' => $request->employee_type,
            'effective_date' => $request->effective_date,
            'workday_rate' => $request->workday_rate,
            'holiday_rate' => $request->holiday_rate,
            'break_mins' => $request->break_mins,
            'min_ot_mins' => $request->min_ot_mins,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->back()->with('success', 'เพิ่มการตั้งค่า OT เรียบร้อยแล้ว ✅');
    }

    public function update(Request $request, $id)
    {
        // 🌟 อัปเกรด Gate
        Gate::authorize('is-tenant-admin');

        $setting = OtSetting::findOrFail($id);

        $request->validate([
            'employee_type' => 'required|in:Daily,Monthly',
            'effective_date' => 'required|date',
            'workday_rate' => 'required|numeric|min:1',
            'holiday_rate' => 'required|numeric|min:1',
            'break_mins' => 'required|integer|min:0',
            'min_ot_mins' => 'required|integer|min:0',
        ]);

        $setting->update([
            'employee_type' => $request->employee_type,
            'effective_date' => $request->effective_date,
            'workday_rate' => $request->workday_rate,
            'holiday_rate' => $request->holiday_rate,
            'break_mins' => $request->break_mins,
            'min_ot_mins' => $request->min_ot_mins,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->back()->with('success', 'อัปเดตการตั้งค่า OT เรียบร้อยแล้ว ✅');
    }

    public function destroy($id)
    {
        // 🌟 อัปเกรด Gate
        Gate::authorize('is-tenant-admin');
        OtSetting::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'ลบการตั้งค่า OT เรียบร้อยแล้ว 🗑️');
    }
}