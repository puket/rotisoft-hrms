<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('positions', function (Blueprint $table) {
            // เช็คและเพิ่มคอลัมน์ job_level
            if (!Schema::hasColumn('positions', 'job_level')) {
                $table->enum('job_level', ['MD', 'Manager', 'Staff'])->default('Staff')->after('title')->comment('ระดับตำแหน่ง');
            }
            
            // เช็คและเพิ่มคอลัมน์ job_description
            if (!Schema::hasColumn('positions', 'job_description')) {
                $table->text('job_description')->nullable()->after('job_level')->comment('รายละเอียดงาน');
            }
        });
    }

    public function down()
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn(['job_level', 'job_description']);
        });
    }
};