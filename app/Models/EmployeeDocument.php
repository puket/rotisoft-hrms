<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Tenantable;

class EmployeeDocument extends Model
{
    use Tenantable;

    protected $table = 'employee_documents'; // 🌟 เพิ่มบรรทัดนี้

    protected $fillable = ['employee_id', 'document_name', 'document_type', 'file_path'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}