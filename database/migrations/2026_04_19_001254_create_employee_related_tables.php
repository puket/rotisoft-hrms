<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ตารางประวัติการศึกษา (Employee Educations)
        Schema::create('employee_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('degree')->comment('ระดับการศึกษา เช่น ปริญญาตรี');
            $table->string('major')->comment('สาขาวิชา');
            $table->string('institution')->comment('สถาบันการศึกษา');
            $table->string('graduation_year', 4)->nullable()->comment('ปีที่จบการศึกษา');
            $table->decimal('gpa', 3, 2)->nullable()->comment('เกรดเฉลี่ย');
            $table->timestamps();
        });

        // 2. ตารางประวัติการทำงาน (Employee Experiences)
        Schema::create('employee_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('company_name')->comment('ชื่อบริษัทเก่า');
            $table->string('job_title')->comment('ตำแหน่งงาน');
            $table->date('start_date')->nullable()->comment('วันที่เริ่มงาน');
            $table->date('end_date')->nullable()->comment('วันที่สิ้นสุด (ถ้าว่างคือปัจจุบัน)');
            $table->text('job_description')->nullable()->comment('รายละเอียดงาน');
            $table->timestamps();
        });

        // 3. ตารางประวัติการฝึกอบรม (Employee Trainings)
        Schema::create('employee_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('course_name')->comment('ชื่อหลักสูตร');
            $table->string('organizer')->nullable()->comment('ผู้จัดอบรม / สถาบัน');
            $table->date('completion_date')->nullable()->comment('วันที่อบรมเสร็จสิ้น');
            $table->string('certificate_no')->nullable()->comment('เลขที่ใบประกาศนียบัตร');
            $table->timestamps();
        });

        // 4. ตารางแนบไฟล์เอกสารพนักงาน (Employee Documents)
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('document_name')->comment('ชื่อเอกสาร เช่น สำเนาบัตรประชาชน');
            $table->enum('document_type', ['ID_Card', 'House_Registration', 'Bookbank', 'Resume', 'Certificate', 'Contract', 'Other'])->default('Other');
            $table->string('file_path')->comment('ที่อยู่ไฟล์ในระบบ');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('employee_trainings');
        Schema::dropIfExists('employee_experiences');
        Schema::dropIfExists('employee_educations');
    }
};