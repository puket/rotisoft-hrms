<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
        {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('comp_code')->unique()->comment('รหัสบริษัท/องค์กร');
                $table->string('name')->comment('ชื่อบริษัท/องค์กร');
                $table->string('tax_id')->nullable()->comment('เลขประจำตัวผู้เสียภาษี 13 หลัก');
                $table->text('address')->nullable()->comment('ที่อยู่สำนักงานใหญ่');
                $table->string('phone_number')->nullable()->comment('เบอร์โทรศัพท์ติดต่อ');
                $table->string('logo_path')->nullable()->comment('พาธเก็บรูปลูกค้า/โลโก้');
                $table->enum('status', ['Active', 'Inactive'])->default('Active')->comment('สถานะการใช้งานระบบ');
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
