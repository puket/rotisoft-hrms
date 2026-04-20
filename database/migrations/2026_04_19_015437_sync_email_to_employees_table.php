<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ทำการเชื่อมตาราง employees กับ users ด้วย user_id
        // และอัปเดตช่อง email ของ employees ให้เท่ากับ email ของ users
        DB::table('employees')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->update([
                'employees.email' => DB::raw('users.email')
            ]);
    }

    public function down(): void
    {
        // กรณี Rollback ให้เคลียร์ค่า email ในตาราง employees เป็น null
        DB::table('employees')->update(['email' => null]);
    }
};