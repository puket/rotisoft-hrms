<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ot_settings', function (Blueprint $table) {
            // เพิ่มฟิลด์ประเภทพนักงาน (รายวัน / รายเดือน)
            $table->enum('employee_type', ['Daily', 'Monthly'])->default('Monthly')->after('id')->comment('ประเภทพนักงาน');
        });
    }

    public function down(): void
    {
        Schema::table('ot_settings', function (Blueprint $table) {
            $table->dropColumn('employee_type');
        });
    }
};