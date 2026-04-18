<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique()->comment('วันที่หยุด');
            $table->string('name')->comment('ชื่อวันหยุด เช่น วันขึ้นปีใหม่');
            $table->text('description')->nullable()->comment('รายละเอียดเพิ่มเติม');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};