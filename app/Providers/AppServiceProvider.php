<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. เจ้าของระบบ (Super Admin)
        \Illuminate\Support\Facades\Gate::define('is-super-admin', function ($user) {
            return $user->role === 'super_admin';
        });

        // 2. แอดมินบริษัท (Tenant Admin) - จัดการตั้งค่าองค์กรและนโยบาย
        \Illuminate\Support\Facades\Gate::define('is-tenant-admin', function ($user) {
            return in_array($user->role, ['super_admin', 'tenant_admin']);
        });

        // 3. ฝ่ายบุคคล (HR) - จัดการพนักงาน กะ เงินเดือน
        \Illuminate\Support\Facades\Gate::define('is-hr', function ($user) {
            return in_array($user->role, ['super_admin', 'tenant_admin', 'hr']);
        });

        // 4. หัวหน้างาน (Manager) - อนุมัติเอกสาร ESS ของลูกน้อง
        \Illuminate\Support\Facades\Gate::define('is-manager', function ($user) {
            if (in_array($user->role, ['super_admin', 'tenant_admin', 'hr'])) return true;
            return $user->employee && \App\Models\Employee::where('manager_id', $user->employee->id)->exists();
        });
    }
    
    public function xxbootx(): void
    {
        // 1. เจ้าของระบบ (Super Admin) - เข้าได้เฉพาะจัดการบริษัท
        Gate::define('is-super-admin', function ($user) {
            return $user->role === 'super_admin';
        });

        // 2. แอดมินบริษัท (Tenant Admin) - จัดการตั้งค่าองค์กรและนโยบาย
        Gate::define('is-tenant-admin', function ($user) {
            // Super Admin ก็ควรเข้ามาช่วยตั้งค่าให้ลูกค้าได้ในบางกรณี
            return in_array($user->role, ['super_admin', 'tenant_admin']);
        });

        // 3. ฝ่ายบุคคล (HR) - จัดการพนักงาน กะ เงินเดือน
        Gate::define('is-hr', function ($user) {
            // ให้สิทธิ์คนที่ role เป็น 'hr', 'tenant_admin' และ 'super_admin'
            return in_array($user->role, ['super_admin', 'tenant_admin', 'hr']);
        });

        // 4. หัวหน้างาน (Manager) - อนุมัติเอกสาร ESS ของลูกน้อง
        Gate::define('is-manager', function ($user) {
            // HR และ Admin สามารถอนุมัติแทนได้
            if (in_array($user->role, ['super_admin', 'tenant_admin', 'hr'])) return true;
            
            // เช็คว่าเป็นหัวหน้างานจริงๆ หรือไม่ (มีลูกน้องผูก manager_id มาหา)
            return $user->employee && \App\Models\Employee::where('manager_id', $user->employee->id)->exists();
        });
    }

    public function bootxxx(): void
    {
        // สิทธิ์เดิม (อาจจะเอาไว้ใช้กับเมนูอื่น)
        Gate::define('access-admin', function ($user) {
            if ($user->email === 'admin@rotisoft.com') return true;
            return $user->employee && (str_contains($user->employee->position->title, 'Manager') || str_contains($user->employee->position->title, 'Director'));
        });

        // 1. สิทธิ์เห็นเมนู 'view-employees-menu' (เฉพาะ Manager, Admin, HR)
        Gate::define('view-employees-menu', function (User $user) {
            // Admin เข้าได้
            if ($user->email === 'admin@rotisoft.com') return true;
            
            // HR เข้าได้
            if ($user->employee && $user->employee->department && $user->employee->department->name === 'HR') return true;
            
            // Manager (คนที่มีลูกน้อง) เข้าได้
            $emp = \App\Models\Employee::where('user_id', $user->id)->first();
            if ($emp && \App\Models\Employee::where('manager_id', $emp->id)->exists()) return true;
            
            return false;
        });

        // 2. สิทธิ์ดูรายชื่อพนักงานได้ "ทั้งหมด" (เฉพาะ Admin, HR)
        Gate::define('view-all-employees', function (User $user) {
            if ($user->email === 'admin@rotisoft.com') return true;
            if ($user->employee && $user->employee->department && $user->employee->department->name === 'HR') return true;
            return false;
        });

        // 3. สิทธิ์ "แก้ไขข้อมูลพนักงาน" (เฉพาะ HR เท่านั้น!) Admin ก็แก้ไม่ได้
        Gate::define('edit-employees', function (User $user) {
            if ($user->employee && $user->employee->department && $user->employee->department->name === 'HR') return true;
            return false;
        });

        // สิทธิ์ใหม่: สำหรับ หัวหน้างาน (เช็คว่ามีลูกน้องหรือไม่)
        Gate::define('is-manager', function ($user) {
            // 1. หาข้อมูลพนักงานที่ผูกกับ User นี้แบบตรงๆ เพื่อความชัวร์
            $employee = \App\Models\Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return false;
            }

            // 2. เช็คว่าในตาราง Employee มีใครระบุว่า manager_id ตรงกับ ID ของคนๆ นี้บ้างไหม
            return \App\Models\Employee::where('manager_id', $employee->id)->exists();
        });
    }

}
