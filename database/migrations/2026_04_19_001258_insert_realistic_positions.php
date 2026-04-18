<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ค้นหา ID ของแผนก (ถ้ายังไม่มีชื่อแผนกนี้ในระบบ จะทำการสร้างให้ก่อนอัตโนมัติ)
        $cId = $this->getOrCreateDepartment('Management', 'Management');
        $hrId = $this->getOrCreateDepartment('HR', 'Human Resources Department');
        $itId = $this->getOrCreateDepartment('IT', 'Information Technology Department');
        $salesId = $this->getOrCreateDepartment('Sales', 'Sales and Marketing Department');

        // 2. ข้อมูลตำแหน่งงานที่สมจริง ตามแผนก
        $positions = [
            ['department_id' => $cId, 'title' => 'Managing Director', 'base_salary' => 100000],
            ['department_id' => $cId, 'title' => 'HR Director', 'base_salary' => 80000],
            ['department_id' => $cId, 'title' => 'IT Director', 'base_salary' => 80000],
            ['department_id' => $cId, 'title' => 'Sales Director', 'base_salary' => 80000],

            // 👥 ตำแหน่งในแผนก HR
            ['department_id' => $hrId, 'title' => 'HR Manager', 'base_salary' => 45000],
            ['department_id' => $hrId, 'title' => 'HR Specialist', 'base_salary' => 25000],
            ['department_id' => $hrId, 'title' => 'Recruitment Officer', 'base_salary' => 20000],

            // 💻 ตำแหน่งในแผนก IT
            ['department_id' => $itId, 'title' => 'IT Manager', 'base_salary' => 60000],
            ['department_id' => $itId, 'title' => 'System Analyst', 'base_salary' => 40000],
            ['department_id' => $itId, 'title' => 'Software Developer', 'base_salary' => 35000],
            ['department_id' => $itId, 'title' => 'IT Support', 'base_salary' => 18000],

            // 📈 ตำแหน่งในแผนก Sales
            ['department_id' => $salesId, 'title' => 'Sales Manager', 'base_salary' => 50000],
            ['department_id' => $salesId, 'title' => 'Sales Executive', 'base_salary' => 25000],
            ['department_id' => $salesId, 'title' => 'Account Manager', 'base_salary' => 30000],
        ];

        // 3. ทำการเพิ่มข้อมูลลงตาราง positions
        foreach ($positions as $position) {
            // เช็คก่อนว่ามีตำแหน่งนี้ในแผนกนี้หรือยัง (ป้องกันการ insert ซ้ำถ้ารัน migrate หลายรอบ)
            $exists = DB::table('positions')
                ->where('department_id', $position['department_id'])
                ->where('title', $position['title'])
                ->exists();

            if (!$exists) {
                DB::table('positions')->insert([
                    'department_id' => $position['department_id'],
                    'title'         => $position['title'],
                    'base_salary'   => $position['base_salary'],
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // กรณีต้องการย้อนกลับ (Rollback) ให้ลบเฉพาะตำแหน่งเหล่านี้
        $titles = [
            'Managing Director', 'HR Specialist', 'Recruitment Officer',
            'HR Manager', 'HR Specialist', 'Recruitment Officer',
            'IT Manager', 'System Analyst', 'Software Developer', 'IT Support',
            'Sales Manager', 'Sales Executive', 'Account Manager'
        ];
        
        DB::table('positions')->whereIn('title', $titles)->delete();
    }

    /**
     * ฟังก์ชันตัวช่วยสำหรับหา ID แผนก หรือสร้างใหม่ถ้าไม่มี
     */
    private function getOrCreateDepartment($name, $description)
    {
        $department = DB::table('departments')->where('name', $name)->first();

        if ($department) {
            return $department->id;
        }

        return DB::table('departments')->insertGetId([
            'name'        => $name,
            'description' => $description,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);
    }
};