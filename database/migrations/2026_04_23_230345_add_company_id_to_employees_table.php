<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // เช็คก่อนว่ามีคอลัมน์นี้หรือยัง ถ้ายังไม่มีให้สร้างและเชื่อมไปยังตาราง companies
            if (!Schema::hasColumn('employees', 'company_id')) {
                $table->foreignId('company_id')
                      ->nullable() // ยอมให้เป็นค่าว่างได้ก่อน เผื่อมีพนักงานเก่าค้างในระบบ
                      ->after('id')
                      ->constrained('companies')
                      ->cascadeOnDelete(); // ถ้าลบบริษัท พนักงานในบริษัทนี้จะถูกลบทิ้งไปด้วย
            }
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'company_id')) {
                $table->dropForeign(['company_id']); // ต้องตัดความสัมพันธ์ Foreign Key ก่อน
                $table->dropColumn('company_id');    // แล้วค่อยลบคอลัมน์ทิ้ง
            }
        });
    }
};