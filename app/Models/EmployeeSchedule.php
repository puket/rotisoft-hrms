<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id', 'work_date', 'shift_id', 
        'is_day_off', 'is_holiday', 
        'expected_clock_in', 'expected_clock_out'
    ];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function shift() { return $this->belongsTo(Shift::class); }
}