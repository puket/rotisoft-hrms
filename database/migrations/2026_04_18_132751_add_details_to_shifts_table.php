<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->string('shift_code')->nullable()->after('id')->comment('รหัสกะการทำงาน');
            $table->decimal('normal_work_hours', 4, 2)->default(8.00)->after('end_time')->comment('จำนวนชั่วโมงทำงานปกติ');
            
            // ช่วงเวลาพักเบรค
            $table->time('break_start_time')->nullable()->after('normal_work_hours')->comment('เวลาเริ่มพักเบรค');
            $table->time('break_end_time')->nullable()->after('break_start_time')->comment('เวลาสิ้นสุดพักเบรค');
            $table->decimal('break_hours', 4, 2)->default(1.00)->after('break_end_time')->comment('จำนวนชั่วโมงพัก');

            // ช่วงเวลาอนุญาตให้ทำ OT
            $table->time('ot_before_start_time')->nullable()->after('break_hours')->comment('เวลาเริ่ม OT ก่อนเข้างาน');
            $table->time('ot_before_end_time')->nullable()->after('ot_before_start_time')->comment('เวลาสิ้นสุด OT ก่อนเข้างาน');
            $table->time('ot_after_start_time')->nullable()->after('ot_before_end_time')->comment('เวลาเริ่ม OT หลังออกงาน');
            $table->time('ot_after_end_time')->nullable()->after('ot_after_start_time')->comment('เวลาสิ้นสุด OT หลังออกงาน');
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn([
                'shift_code', 'normal_work_hours', 
                'break_start_time', 'break_end_time', 'break_hours',
                'ot_before_start_time', 'ot_before_end_time', 
                'ot_after_start_time', 'ot_after_end_time'
            ]);
        });
    }
};
