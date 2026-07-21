<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Tenantable
{
    protected static function bootTenantable()
    {
        // 🌟 1. Global Scope: คัดกรองข้อมูลตอน "ดึง (Select)"
        //
        // สำคัญ: ต้อง addGlobalScope() แบบไม่มีเงื่อนไขล้อมรอบเสมอ (ไม่ใช่ if (auth()->check())
        // ครอบตรงนี้) เพราะ Eloquent จะ boot() model แต่ละคลาส "แค่ครั้งเดียวต่อ process"
        // ใน process แบบอายุยืน (tinker, queue worker, scheduler) ถ้า model ถูกแตะครั้งแรก
        // ก่อน login (auth()->check() เป็น false ตอนนั้น) scope จะไม่ถูกลงทะเบียนเลย
        // และจะ "ไม่ทำงานตลอดทั้ง process" แม้จะ login สำเร็จภายหลังก็ตาม
        //
        // ทางที่ถูกคือ addGlobalScope เสมอ แล้วเอาเงื่อนไข auth ไปเช็ค "ข้างใน" closure
        // ซึ่งจะถูกประเมินใหม่ทุกครั้งที่มีการ query จริง จึงปลอดภัยไม่ว่าจะ login ก่อนหรือหลัง
        static::addGlobalScope('company_id', function (Builder $builder) {
            if (auth()->check() && auth()->user()->role !== 'super_admin') {
                // ใช้ $builder->getQuery()->from เพื่อป้องกัน Error กรณีมีการ Join ตาราง
                $builder->where($builder->getQuery()->from . '.company_id', auth()->user()->company_id);
            }
        });

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