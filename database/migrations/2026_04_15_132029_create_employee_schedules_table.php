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
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('work_date'); // วันที่
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->onDelete('set null'); // กะของวันนี้
            $table->boolean('is_day_off')->default(false); // เป็นวันหยุดประจำสัปดาห์ไหม?
            $table->boolean('is_holiday')->default(false); // เป็นวันหยุดนักขัตฤกษ์ไหม?
            
            $table->time('expected_clock_in')->nullable(); // เวลาที่ควรจะเข้า (ดึงมาจากกะ เผื่อคำนวณสาย)
            $table->time('expected_clock_out')->nullable(); // เวลาที่ควรจะออก
            
            $table->timestamps();

            // ป้องกันระบบ Gen ตารางซ้ำในวันเดียวกันของพนักงานคนเดิม
            $table->unique(['employee_id', 'work_date']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_schedules');
    }
};
