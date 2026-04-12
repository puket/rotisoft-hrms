<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    protected $fillable = ['employee_id', 'leave_type', 'start_date', 'end_date', 'reason', 'status', 'approved_by'];

    public function employee() {
        return $this->belongsTo(Employee::class);
    }
}