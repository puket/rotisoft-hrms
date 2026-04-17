<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftAssignment extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'shift_id', 'effective_date', 'assigned_by'];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function shift() { return $this->belongsTo(Shift::class); }
    public function assigner() { return $this->belongsTo(Employee::class, 'assigned_by'); }
}