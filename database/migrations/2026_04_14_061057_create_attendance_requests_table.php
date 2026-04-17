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
            Schema::create('attendance_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->onDelete('cascade');
                $table->date('work_date'); // วันที่ต้องการแก้ไขหรือลืมลงเวลา
                $table->time('requested_clock_in')->nullable(); // เวลาเข้าที่ขอแก้
                $table->time('requested_clock_out')->nullable(); // เวลาออกที่ขอแก้
                $table->text('reason'); // เหตุผลที่ขอแก้
                $table->string('status')->default('Pending'); // สถานะ Pending, Approved, Rejected
                
                // ใครเป็นคนอนุมัติ (เชื่อมไปตาราง employees)
                $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null'); 
                
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_requests');
    }
};
