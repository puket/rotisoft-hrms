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
    Schema::create('payroll_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('payroll_id')->constrained()->onDelete('cascade');
        $table->string('item_name'); // เช่น "ค่าเบี้ยเลี้ยง", "หักมาสาย"
        $table->enum('item_type', ['Addition', 'Deduction']); 
        $table->decimal('amount', 10, 2);
        $table->text('description')->nullable(); // เช่น "มาสาย 3 ครั้ง รวม 45 นาที"
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
