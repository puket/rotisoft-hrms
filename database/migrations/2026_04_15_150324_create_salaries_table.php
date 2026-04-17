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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('base_salary', 10, 2); // ฐานเงินเดือน (เช่น 25000.00)
            $table->string('bank_name')->nullable(); // ชื่อธนาคาร
            $table->string('account_number')->nullable(); // เลขที่บัญชี
            $table->string('tax_id')->nullable(); // เลขประจำตัวผู้เสียภาษี
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
