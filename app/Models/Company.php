<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // 🌟 บรรทัดพระเอก: ปลดล็อคให้สามารถเซฟข้อมูลจากฟอร์มลง Database ได้
    protected $guarded = [];

    // ==========================================
    // 🔗 การเชื่อมโยงความสัมพันธ์ (Relationships)
    // ==========================================

    // 1 บริษัท มีได้หลายแผนก
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    // 1 บริษัท มีได้หลายตำแหน่งงาน
    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    // 1 บริษัท มีพนักงานได้หลายคน
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}