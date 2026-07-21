<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Tenantable;

class PayrollItem extends Model
{
    use Tenantable;

    // 🌟 เพิ่มบรรทัดนี้เพื่อให้บันทึกข้อมูลได้
    protected $fillable = [
        'company_id',
        'payroll_id',
        'item_name',
        'item_type',
        'amount',
        'description'
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}