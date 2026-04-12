<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Position;
use App\Models\Employee;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. สร้างแผนก IT และตำแหน่ง
        $it = Department::create(['name' => 'IT', 'description' => 'Information Technology']);
        $itDev = Position::create(['department_id' => $it->id, 'title' => 'Software Engineer', 'base_salary' => 45000]);
        $itSupport = Position::create(['department_id' => $it->id, 'title' => 'IT Support', 'base_salary' => 25000]);

        // 2. สร้างแผนก HR และตำแหน่ง
        $hr = Department::create(['name' => 'HR', 'description' => 'Human Resources']);
        $hrManager = Position::create(['department_id' => $hr->id, 'title' => 'HR Manager', 'base_salary' => 55000]);
        $hrOfficer = Position::create(['department_id' => $hr->id, 'title' => 'HR Officer', 'base_salary' => 28000]);

        // 3. สร้างแผนก Sales และตำแหน่ง
        $sales = Department::create(['name' => 'Sales', 'description' => 'Sales Department']);
        $salesManager = Position::create(['department_id' => $sales->id, 'title' => 'Sales Manager', 'base_salary' => 60000]);
        $salesExec = Position::create(['department_id' => $sales->id, 'title' => 'Sales Executive', 'base_salary' => 35000]);

        // รวบรวมตำแหน่งทั้งหมดไว้สุ่ม
        $positions = [$itDev, $itSupport, $hrManager, $hrOfficer, $salesManager, $salesExec];

        // 4. สร้างพนักงาน 30 คน แล้วสุ่มจับยัดลงตำแหน่งที่ถูกต้อง (แผนกจะถูกผูกให้อัตโนมัติ)
        Employee::factory(30)->create()->each(function ($employee) use ($positions) {
            $randomPosition = $positions[array_rand($positions)];
            
            $employee->update([
                'department_id' => $randomPosition->department_id,
                'position_id' => $randomPosition->id,
            ]);
        });
    }
}