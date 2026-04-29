<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Tenantable;

class Payroll extends Model
{
    use HasFactory;
    use Tenantable;
    
    protected $fillable = [
        'employee_id', 'period', 'base_salary', 'ot_amount', 'allowance', 
        'late_deduction', 'tax_amount', 'social_security', 'net_salary', 
        'status', 'payment_date'
    ];

    public function employee() { return $this->belongsTo(Employee::class); }

    public function items() {
        return $this->hasMany(PayrollItem::class);
    }

    public function otDetails() {
        return $this->hasMany(PayrollOtDetail::class);
    }
}