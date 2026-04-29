<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. จัดการตาราง users (เพิ่ม company_id และ role)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('staff')->after('company_id')->comment('super_admin, tenant_admin, staff');
            }
        });

        // 2. ลิสต์ตารางทั้งหมดที่ต้องการเพิ่ม company_id
        $saasTables = [
            'holidays', 'leave_types', 'ot_settings', 'shifts', 
            'attendances', 'attendance_requests', 'leaves', 'ot_requests', 
            'payrolls', 'salaries', 'shift_assignments',
            'employee_documents', 'employee_educations', 'employee_experiences', 
            'employee_schedules', 'employee_trainings', 'payroll_items', 'payroll_ot_details'
        ];
        
        foreach ($saasTables as $tableName) {
            // เช็คก่อนว่ามีตารางนี้อยู่ในระบบหรือยัง (กัน Error)
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'company_id')) {
                        // เพิ่ม company_id และตั้งให้ลบข้อมูลทิ้งอัตโนมัติ (cascade) ถ้าบริษัทถูกลบ
                        $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->cascadeOnDelete();
                    }
                });
            }
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });

        $saasTables = [
            'holidays', 'leave_types', 'ot_settings', 'shifts', 
            'attendances', 'attendance_requests', 'leaves', 'ot_requests', 
            'payrolls', 'salaries', 'shift_assignments',
            'employee_documents', 'employee_educations', 'employee_experiences', 
            'employee_schedules', 'employee_trainings', 'payroll_items', 'payroll_ot_details'
        ];

        foreach ($saasTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                    $table->dropColumn('company_id');
                });
            }
        }
    }
};