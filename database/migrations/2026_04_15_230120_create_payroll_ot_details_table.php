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
    Schema::create('payroll_ot_details', function (Blueprint $table) {
        $table->id();
        $table->foreignId('payroll_id')->constrained()->onDelete('cascade');
        $table->date('work_date');
        $table->integer('pre_shift_mins')->default(0);  // นาที OT ก่อนงาน
        $table->integer('post_shift_mins')->default(0); // นาที OT หลังงาน
        $table->decimal('total_hours', 5, 2);           // ชั่วโมงรวม (นาที/60)
        $table->decimal('multiplier', 3, 2);            // 1.5, 3.0
        $table->decimal('amount', 10, 2);               // ยอดเงินที่ได้ในวันนั้น
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_ot_details');
    }
};
