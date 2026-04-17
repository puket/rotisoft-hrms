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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('period'); // รอบเดือนที่จ่าย เช่น "2026-04"
            
            // รายรับ
            $table->decimal('base_salary', 10, 2)->default(0); // ฐานเงินเดือน
            $table->decimal('ot_amount', 10, 2)->default(0); // ค่าล่วงเวลา (OT)
            $table->decimal('allowance', 10, 2)->default(0); // สวัสดิการอื่นๆ
            
            // รายจ่าย/หัก
            $table->decimal('late_deduction', 10, 2)->default(0); // หักมาสาย/ขาดงาน
            $table->decimal('tax_amount', 10, 2)->default(0); // หักภาษี (ภาษีหัก ณ ที่จ่าย)
            $table->decimal('social_security', 10, 2)->default(0); // หักประกันสังคม
            
            // สุทธิ
            $table->decimal('net_salary', 10, 2)->default(0); // เงินเดือนสุทธิที่ได้รับ
            
            $table->string('status')->default('Draft'); // สถานะ: Draft (กำลังคำนวณ), Approved, Paid
            $table->date('payment_date')->nullable(); // วันที่โอนเงินจริง
            
            $table->timestamps();
            
            // ป้องกันการรันเงินเดือนซ้ำเดือนเดียวกันให้พนักงานคนเดิม
            $table->unique(['employee_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
