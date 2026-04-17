<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtSetting extends Model
{
    // 🌟 ระบุฟิลด์ที่อนุญาตให้บันทึกข้อมูลได้ (ห้ามใส่ _token ในนี้)
    protected $fillable = [
        'effective_date',
        'workday_rate',
        'holiday_rate',
        'min_ot_mins',
        'break_mins',
        'note',
        'is_active'
    ];
}