<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'work_date', 'start_time', 'end_time', 
        'reason', 'status', 'manager_id'
    ];

    // เชื่อมกลับไปหาพนักงานที่ขอ
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // เชื่อมไปหาหัวหน้าที่อนุมัติ
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }
}