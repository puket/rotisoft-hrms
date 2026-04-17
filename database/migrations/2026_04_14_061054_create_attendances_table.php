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
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->onDelete('cascade');
                $table->date('work_date'); // วันที่ทำงาน
                $table->time('clock_in')->nullable(); // เวลาเข้า
                $table->time('clock_out')->nullable(); // เวลาออก
                $table->string('status')->default('Present'); // สถานะ เช่น Present, Late
                $table->timestamps();
                
                // ป้องกันพนักงานลงเวลาซ้ำในวันเดียวกัน 2 record
                $table->unique(['employee_id', 'work_date']); 
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
