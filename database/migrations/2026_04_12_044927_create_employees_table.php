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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            // เชื่อมกับระบบ Login (เผื่อไว้กรณีเพิ่มพนักงานแต่ยังไม่สร้างรหัสผ่านให้)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('employee_code')->unique(); // รหัสพนักงาน
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('hire_date'); // วันที่เริ่มงาน
            
            // เชื่อม Foreign Key ไปที่ แผนก และ ตำแหน่ง
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('set null');
            
            // เชื่อม Foreign Key หาตัวเอง (หัวหน้างานก็คือพนักงานคนนึง)
            $table->foreignId('manager_id')->nullable()->constrained('employees')->onDelete('set null');
            
            $table->enum('status', ['Active', 'Resigned', 'Suspended'])->default('Active'); // สถานะพนักงาน
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
