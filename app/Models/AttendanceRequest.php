<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Tenantable;


class AttendanceRequest extends Model
{
    use HasFactory;
    use Tenantable;
    
    protected $fillable = [
        'employee_id', 'work_date', 'requested_clock_in', 'requested_clock_out', 
        'reason', 'status', 'approved_by'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
}