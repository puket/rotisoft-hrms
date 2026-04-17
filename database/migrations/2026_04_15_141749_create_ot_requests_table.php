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
        Schema::create('ot_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade'); // คนขอ OT
            $table->date('work_date'); // วันที่ขอทำ OT
            $table->time('start_time'); // เวลาเริ่ม OT
            $table->time('end_time'); // เวลาจบ OT
            $table->text('reason'); // เหตุผลที่ต้องทำ OT
            $table->string('status')->default('Pending'); // สถานะ: Pending, Approved, Rejected
            
            // ใครเป็นคนอนุมัติ (หัวหน้างาน)
            $table->foreignId('manager_id')->nullable()->constrained('employees')->onDelete('set null'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ot_requests');
    }
};
