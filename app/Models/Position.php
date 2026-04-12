<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    // ตำแหน่งนี้ สังกัดอยู่แผนกอะไร
    public function department() {
        return $this->belongsTo(Department::class);
    }

    // ตำแหน่งนี้ มีพนักงานคนไหนทำอยู่บ้าง
    public function employees() {
        return $this->hasMany(Employee::class);
    }
}