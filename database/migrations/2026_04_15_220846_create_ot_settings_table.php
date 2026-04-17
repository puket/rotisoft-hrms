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
    Schema::create('ot_settings', function (Blueprint $table) {
        $table->id();
        $table->date('effective_date'); // วันที่มีผลบังคับใช้
        $table->decimal('workday_rate', 3, 2)->default(1.5);
        $table->decimal('holiday_rate', 3, 2)->default(3.0);
        $table->integer('min_ot_mins')->default(30); // ขั้นต่ำที่เริ่มนับ OT
        $table->integer('break_mins')->default(30);  // บังคับพักก่อนเริ่ม OT
        $table->text('note')->nullable();            // หมายเหตุบันทึกช่วยจำ
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ot_settings');
    }
};
