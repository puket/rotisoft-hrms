<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // 1. ข้อมูลการติดต่อฉุกเฉิน (ไม่ซ้ำกับของเดิม)
            $table->string('emergency_contact_name')->nullable()->comment('ชื่อผู้ติดต่อฉุกเฉิน');
            $table->string('emergency_contact_phone', 20)->nullable()->comment('เบอร์โทรผู้ติดต่อฉุกเฉิน');

            // 2. ข้อมูลการจ้างงาน
            $table->enum('employee_type', ['Daily', 'Monthly'])->default('Monthly')->after('status')->comment('ประเภทพนักงาน (รายวัน/รายเดือน)');
            $table->enum('employment_status', ['Probation', 'Permanent', 'Resigned', 'Terminated'])->default('Probation')->comment('สถานะการจ้างงาน');
            $table->enum('contract_type', ['Full-time', 'Part-time', 'Contract'])->default('Full-time')->comment('ประเภทสัญญาจ้าง');
            $table->date('probation_end_date')->nullable()->comment('วันที่ผ่านโปร');
            $table->date('resignation_date')->nullable()->comment('วันที่พ้นสภาพพนักงาน');

            // 3. ข้อมูลการเงินและสวัสดิการ
            $table->string('bank_name')->nullable()->comment('ชื่อธนาคาร');
            $table->string('bank_account', 30)->nullable()->comment('เลขที่บัญชีธนาคาร');
            $table->string('tax_id', 20)->nullable()->comment('เลขประจำตัวผู้เสียภาษี');
            $table->string('social_security_number', 20)->nullable()->comment('เลขประกันสังคม');
            $table->string('uniform_size', 10)->nullable()->comment('ไซส์เสื้อยูนิฟอร์ม');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_contact_name', 'emergency_contact_phone',
                'employee_type', 'employment_status', 'contract_type', 
                'probation_end_date', 'resignation_date',
                'bank_name', 'bank_account', 'tax_id', 
                'social_security_number', 'uniform_size'
            ]);
        });
    }
};