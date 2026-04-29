<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Tenantable;

class OtSetting extends Model
{
    use Tenantable;
    
    protected $fillable = [
        'employee_type',
        'effective_date',
        'workday_rate',
        'holiday_rate',
        'break_mins',
        'min_ot_mins',
        'is_active'
    ];

}