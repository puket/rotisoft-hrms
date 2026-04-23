<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    // พนักงานคนนี้ อยู่แผนกอะไร
    public function department() {
        return $this->belongsTo(Department::class);
    }

    // พนักงานคนนี้ ทำตำแหน่งอะไร
    public function position() {
        return $this->belongsTo(Position::class);
    }

    // พนักงานคนนี้ ใครเป็นหัวหน้า
    public function manager() {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    // ระบบ Login ของพนักงานคนนี้
    public function user() {
        return $this->belongsTo(User::class);
    }

    // ระบบ leaves
    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    // เชื่อมความสัมพันธ์กับกะการทำงาน
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    
    // ข้อมูลฐานเงินเดือน
    public function salary()
    {
        return $this->hasOne(Salary::class);
    }

    // ประวัติการรับเงินเดือน (Payslips)
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
    
    // ==========================================
    // 🔗 ความสัมพันธ์กับประวัติต่างๆ (1 พนักงาน มีหลายประวัติ)
    // ==========================================
    public function educations()
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function experiences()
    {
        return $this->hasMany(EmployeeExperience::class);
    }

    public function trainings()
    {
        return $this->hasMany(EmployeeTraining::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function subordinates()
    {
        // พนักงานคนนี้ "มีลูกน้องคือ..." (ถ้าเขาเป็นผู้จัดการ)
        return $this->hasMany(Employee::class, 'manager_id');
    }

    
}