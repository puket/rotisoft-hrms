<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Tenantable;

class Salary extends Model
{
    use HasFactory;
    use Tenantable;

    // บรรทัดนี้สำคัญมากครับ ถ้าไม่มีระบบจะไม่ยอมบันทึกลงฐานข้อมูล
    protected $fillable = ['employee_id', 'base_salary', 'bank_name', 'account_number', 'tax_id'];

    public function employee() { return $this->belongsTo(Employee::class); }
}