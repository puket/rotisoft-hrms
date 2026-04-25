<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;
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

    // 🌟 เพิ่มบรรทัดนี้: แผนกสังกัดอยู่บริษัทไหน
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // 🌟 เพิ่มบรรทัดนี้ (ถ้าใช้): แผนกย่อยสังกัดแผนกหลักไหน
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }
}