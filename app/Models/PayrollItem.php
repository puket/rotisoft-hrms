<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    // 🌟 เพิ่มบรรทัดนี้เพื่อให้บันทึกข้อมูลได้
    protected $fillable = [
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