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
        // กำหนดสิทธิ์ 'access-admin'
        // ในที่นี้เรากำหนดให้ คนที่มีอีเมล admin@rotisoft.com หรือ คนที่มีคำว่า Manager อยู่ในชื่อตำแหน่ง เป็นผู้มีสิทธิ์
        Gate::define('access-admin', function ($user) {
            // เช็คจากอีเมลแอดมินหลัก
            if ($user->email === 'admin@rotisoft.com') return true;

            // เช็คว่าพนักงานคนนี้มีตำแหน่งเป็น Manager หรือ MD หรือไม่
            return $user->employee && (
                str_contains($user->employee->position->title, 'Manager') || 
                str_contains($user->employee->position->title, 'Director')
            );
        });
    }
}
