<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTraining extends Model
{
    protected $table = 'employee_trainings'; // 🌟 เพิ่มบรรทัดนี้
    
    protected $fillable = ['employee_id', 'course_name', 'organizer', 'completion_date', 'certificate_no'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}