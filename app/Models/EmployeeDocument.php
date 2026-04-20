<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $table = 'employee_documents'; // 🌟 เพิ่มบรรทัดนี้
    
    protected $fillable = ['employee_id', 'document_name', 'document_type', 'file_path'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}