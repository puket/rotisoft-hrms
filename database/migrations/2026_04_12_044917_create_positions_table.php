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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            // เชื่อม Foreign Key ไปที่ตาราง departments
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('title'); // ชื่อตำแหน่ง
            $table->decimal('base_salary', 10, 2)->nullable(); // ฐานเงินเดือนเริ่มต้น
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
