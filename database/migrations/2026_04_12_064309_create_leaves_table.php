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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained(); // ใครลา
            $table->string('leave_type'); // ประเภทการลา (Sick, Casual, Vacation)
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable(); // เหตุผลการลา
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending'); // สถานะ
            $table->foreignId('approved_by')->nullable()->references('id')->on('employees'); // ใครอนุมัติ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
