<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Tenantable;

class Shift extends Model
{
    use Tenantable;
    
    protected $fillable = [
        'shift_code', // เพิ่มเข้ามา
        'name',
        'start_time',
        'end_time',
        'normal_work_hours', // เพิ่มเข้ามา
        'break_start_time',  // เพิ่มเข้ามา
        'break_end_time',    // เพิ่มเข้ามา
        'break_hours',       // เพิ่มเข้ามา
        'ot_before_start_time', // เพิ่มเข้ามา
        'ot_before_end_time',   // เพิ่มเข้ามา
        'ot_after_start_time',  // เพิ่มเข้ามา
        'ot_after_end_time',    // เพิ่มเข้ามา
    ];

    public function assignments()
    {
        return $this->hasMany(ShiftAssignment::class);
    }
}