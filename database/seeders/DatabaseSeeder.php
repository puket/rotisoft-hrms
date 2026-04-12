<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Employee;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 👑 1. สร้างบัญชี Admin ระบบ
        User::create(['name' => 'Admin RotiSoft', 'email' => 'admin@rotisoft.com', 'password' => Hash::make('RTB@2026!')]);

        // 🏢 2. สร้างแผนกต่างๆ
        $it = Department::create(['name' => 'IT']);
        $hr = Department::create(['name' => 'HR']);
        $sales = Department::create(['name' => 'Sales']);

        // 🌟 3. สร้างตำแหน่ง (Positions)
        $posMD = Position::create(['department_id' => $hr->id, 'title' => 'Managing Director']);
        $posITManager = Position::create(['department_id' => $it->id, 'title' => 'IT Manager']);
        $posHRManager = Position::create(['department_id' => $hr->id, 'title' => 'HR Manager']);
        $posSalesManager = Position::create(['department_id' => $sales->id, 'title' => 'Sales Manager']);
        $posStaff = Position::create(['department_id' => $it->id, 'title' => 'Staff']); // ตำแหน่งพนักงานทั่วไป

        // 👑 4. สร้าง Managing Director (MD) - ไม่มี Manager
        $mdUser = User::create(['name' => 'Somchai MD', 'email' => 'md@rotisoft.com', 'password' => Hash::make('password')]);
        $mdEmp = Employee::create([
            'user_id' => $mdUser->id, 'employee_code' => 'MD-001', 'first_name' => 'Somchai', 'last_name' => 'Managing',
            'email' => 'md@rotisoft.com',
            'department_id' => $hr->id, 'position_id' => $posMD->id, 'manager_id' => null, 'hire_date' => now(),
        ]);

        // 👔 5. สร้าง Managers ของแต่ละแผนก (โดยกำหนดให้ manager_id คือ MD)
        $managers = [];
        $managerData = [
            ['email' => 'it.mgr@rotisoft.com', 'fname' => 'Wichai', 'lname' => 'IT', 'dept' => $it->id, 'pos' => $posITManager->id],
            ['email' => 'hr.mgr@rotisoft.com', 'fname' => 'Manee', 'lname' => 'HR', 'dept' => $hr->id, 'pos' => $posHRManager->id],
            ['email' => 'sales.mgr@rotisoft.com', 'fname' => 'Piti', 'lname' => 'Sales', 'dept' => $sales->id, 'pos' => $posSalesManager->id],
        ];

        foreach ($managerData as $idx => $data) {
            $user = User::create(['name' => $data['fname'].' '.$data['lname'], 'email' => $data['email'], 'password' => Hash::make('password')]);
            $managers[] = Employee::create([
                'user_id' => $user->id, 'employee_code' => 'MGR-00'.($idx+1), 'first_name' => $data['fname'], 'last_name' => $data['lname'],
                'email' => $data['email'],
                'department_id' => $data['dept'], 'position_id' => $data['pos'], 'manager_id' => $mdEmp->id, 'hire_date' => now(), // ผูกกับ MD
            ]);
        }

        // 👥 6. สร้างพนักงานทั่วไป 30 คน พร้อมบัญชี ESS และผูกเข้ากับ Manager แต่ละแผนก
        Employee::factory(30)->create()->each(function ($employee) use ($managers, $posStaff) {
            // สุ่ม Manager 1 คนมาเป็นหัวหน้า
            $randomManager = $managers[array_rand($managers)];
            
            // สร้างบัญชี ESS ให้พนักงานแต่ละคน (ใช้อีเมลจากชื่อจริง)
            $email = strtolower($employee->first_name) . '@rotisoft.com';
            $user = User::create([
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'email' => $email,
                'password' => Hash::make('password'),
            ]);

            // อัปเดตข้อมูลพนักงานให้สมบูรณ์
            $employee->update([
                'user_id' => $user->id,
                'email' => $email,
                'department_id' => $randomManager->department_id,
                'position_id' => $posStaff->id,
                'manager_id' => $randomManager->id, // ผูกกับหัวหน้างานแผนกนั้นๆ
            ]);
        });
    }
}