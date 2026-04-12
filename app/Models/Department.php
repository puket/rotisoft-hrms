<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;
    protected $guarded = []; // อนุญาตให้เพิ่มข้อมูลได้ทุกคอลัมน์

    // 1 แผนก มีหลายตำแหน่ง
    public function positions() {
        return $this->hasMany(Position::class);
    }

    // 1 แผนก มีพนักงานหลายคน
    public function employees() {
        return $this->hasMany(Employee::class);
    }
}