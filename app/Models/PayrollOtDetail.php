<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollOtDetail extends Model
{
    // 🌟 เพิ่มบรรทัดนี้เพื่อให้บันทึกข้อมูลได้
    protected $fillable = [
        'payroll_id',
        'work_date',
        'pre_shift_mins',
        'post_shift_mins',
        'total_hours',
        'multiplier',
        'amount'
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}