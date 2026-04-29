<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Tenantable
{
    protected static function bootTenantable()
    {
        // ทำงานเฉพาะเมื่อมี User Login เข้ามาแล้วเท่านั้น
        if (auth()->check()) {
            
            // 🌟 1. Global Scope: คัดกรองข้อมูลตอน "ดึง (Select)"
            static::addGlobalScope('company_id', function (Builder $builder) {
                // ถ้า Role ไม่ใช่ super_admin (เจ้าของระบบ) ให้บังคับดูได้แค่บริษัทตัวเอง!
                if (auth()->user()->role !== 'super_admin') {
                    // ใช้ $builder->getQuery()->from เพื่อป้องกัน Error กรณีมีการ Join ตาราง
                    $builder->where($builder->getQuery()->from . '.company_id', auth()->user()->company_id);
                }
            });
        }

        // 🌟 2. Model Hook: เติมข้อมูลอัตโนมัติ ตอน "สร้าง (Insert)"
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->role !== 'super_admin') {
                // ถ้าตอน Save ไม่ได้ส่ง company_id มา ให้ดึงจาก User ที่ Login ยัดใส่ให้เลย!
                if (empty($model->company_id)) {
                    $model->company_id = auth()->user()->company_id;
                }
            }
        });
    }
}