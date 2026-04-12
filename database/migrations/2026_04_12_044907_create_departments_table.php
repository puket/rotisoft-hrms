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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // ชื่อแผนก (ห้ามซ้ำ)
            $table->text('description')->nullable(); // รายละเอียดแผนก (เว้นว่างได้)
            $table->timestamps();
            $table->softDeletes(); // 💡 ทริค HR: ใช้ Soft Delete เพื่อไม่ให้ข้อมูลหายจริงเวลาเผลอลบ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
