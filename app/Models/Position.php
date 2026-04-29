<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Tenantable;


class Position extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Tenantable;
    
    protected $guarded = [];

    // ตำแหน่งนี้ สังกัดอยู่แผนกอะไร
    public function department() {
        return $this->belongsTo(Department::class);
    }

    // ตำแหน่งนี้ มีพนักงานคนไหนทำอยู่บ้าง
    public function employees() {
        return $this->hasMany(Employee::class);
    }

    // 🌟 เพิ่มบรรทัดนี้: ตำแหน่งสังกัดอยู่บริษัทไหน
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
