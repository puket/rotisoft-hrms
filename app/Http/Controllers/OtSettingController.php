<?php

namespace App\Http\Controllers;

use App\Models\OtSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OtSettingController extends Controller
{
    public function index()
    {
        Gate::authorize('access-admin'); // เฉพาะ Admin/HR
        $settings = OtSetting::orderBy('effective_date', 'desc')->get();
        return view('admin.ot_settings.index', compact('settings'));
    }

public function store(Request $request)
    {
        Gate::authorize('access-admin');
        
        $validated = $request->validate([
            'effective_date' => 'required|date',
            'workday_rate' => 'required|numeric|min:1',
            'holiday_rate' => 'required|numeric|min:1',
            'min_ot_mins' => 'required|integer|min:0',
            'break_mins' => 'required|integer|min:0',
            'note' => 'nullable|string',
        ]);

        // 🌟 ใช้ข้อมูลที่ผ่านการ Validate แล้วเท่านั้น (มันจะไม่มี _token ติดมา)
        OtSetting::create($validated);

        return redirect()->back()->with('success', 'บันทึกการตั้งค่า OT ใหม่เรียบร้อยแล้ว');
    }
}