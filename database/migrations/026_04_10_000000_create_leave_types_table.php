<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('ชื่อประเภทการลา เช่น ลากิจ ลาป่วย');
            $table->text('description')->nullable()->comment('รายละเอียด');
            $table->integer('default_days')->default(0)->comment('จำนวนวันลาเริ่มต้นต่อปี');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};