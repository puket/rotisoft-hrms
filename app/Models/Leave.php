<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Tenantable;


class Leave extends Model
{
    use Tenantable;
    
    // เปลี่ยนจาก leave_type เป็น leave_type_id
    protected $fillable = ['employee_id', 'leave_type_id', 'start_date', 'end_date', 'reason', 'status', 'approved_by'];

    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    // เพิ่มฟังก์ชันเชื่อมโยงประเภทการลา
    public function leaveType() {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}