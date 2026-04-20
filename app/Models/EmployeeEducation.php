<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    protected $table = 'employee_educations'; // 🌟 เพิ่มบรรทัดนี้เพื่อบังคับชื่อตาราง
    
    protected $fillable = ['employee_id', 'degree', 'major', 'institution', 'graduation_year', 'gpa'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}