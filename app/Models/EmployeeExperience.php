<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeExperience extends Model
{
    protected $table = 'employee_experiences'; // 🌟 เพิ่มบรรทัดนี้
    
    protected $fillable = ['employee_id', 'company_name', 'job_title', 'start_date', 'end_date', 'job_description'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}