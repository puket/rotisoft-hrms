<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // เช็คว่าถ้ามีคอลัมน์เก่าแบบ String ให้ลบทิ้งก่อน
            if (Schema::hasColumn('leaves', 'leave_type')) {
                $table->dropColumn('leave_type');
            }
            
            // เพิ่มคอลัมน์ใหม่แบบ ID เชื่อมกับตาราง leave_types
            if (!Schema::hasColumn('leaves', 'leave_type_id')) {
                $table->foreignId('leave_type_id')->nullable()->after('employee_id')->constrained('leave_types')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['leave_type_id']);
            $table->dropColumn('leave_type_id');
            $table->string('leave_type')->nullable();
        });
    }
};
