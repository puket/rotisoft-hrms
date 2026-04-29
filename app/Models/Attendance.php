<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Tenantable;

class Attendance extends Model
{
    use HasFactory;
    use Tenantable;

    protected $fillable = ['employee_id', 'work_date', 'clock_in', 'clock_out', 'status'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
// เปิดหน้าฟอร์มขอแก้ไขเวลา
    public function createRequest()
    {
        return view('attendances.request');
    }

    // บันทึกคำร้องลงฐานข้อมูล
    public function storeRequest(Request $request)
    {
        $request->validate([
            'work_date' => 'required|date|before_or_equal:today', // ห้ามขอแก้วันในอนาคต
            'requested_clock_in' => 'nullable|date_format:H:i',
            'requested_clock_out' => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:255',
        ], [
            'work_date.before_or_equal' => 'ไม่สามารถขอแก้ไขเวลาของวันในอนาคตได้',
            'reason.required' => 'กรุณาระบุเหตุผลในการขอแก้ไขเวลา (เช่น ลืมสแกนนิ้ว, พบลูกค้า)'
        ]);

        $employee = auth()->user()->employee;

        // เช็คว่ามีคำร้องของวันเดียวกันที่กำลัง Pending อยู่ไหม (ป้องกันส่งซ้ำ)
        $pendingExists = \App\Models\AttendanceRequest::where('employee_id', $employee->id)
            ->where('work_date', $request->work_date)
            ->where('status', 'Pending')
            ->exists();

        if ($pendingExists) {
            return redirect()->back()->with('error', 'คุณมีคำร้องของวันที่เลือกที่กำลังรออนุมัติอยู่แล้ว ⏳');
        }

        // บันทึกคำร้อง
        \App\Models\AttendanceRequest::create([
            'employee_id' => $employee->id,
            'work_date' => $request->work_date,
            'requested_clock_in' => $request->requested_clock_in,
            'requested_clock_out' => $request->requested_clock_out,
            'reason' => $request->reason,
            'status' => 'Pending'
        ]);

        return redirect('/attendance')->with('success', 'ส่งคำร้องขอแก้ไขเวลาเรียบร้อยแล้ว โปรดรอหัวหน้างานอนุมัติ ✅');
    }
    
}