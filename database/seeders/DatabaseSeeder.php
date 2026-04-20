<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\OtSetting;
use App\Models\Holiday;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. 👨‍💼 สร้าง User Admin สำหรับล็อกอิน (ถ้ายังไม่มี)
        User::firstOrCreate(
            ['email' => 'admin@rotisoft.com'],
            [
                'name' => 'Admin RotiSoft',
                'password' => Hash::make('password'),
            ]
        );

        // 2. ⏱️ สร้างการตั้งค่า OT พื้นฐาน (สำหรับรายเดือน และ รายวัน)
        OtSetting::firstOrCreate(
            ['employee_type' => 'Monthly'],
            [
                'effective_date' => '2026-01-01',
                'workday_rate' => 1.5,
                'holiday_rate' => 3.0,
                'break_mins' => 60,
                'min_ot_mins' => 30,
                'is_active' => true
            ]
        );

        OtSetting::firstOrCreate(
            ['employee_type' => 'Daily'],
            [
                'effective_date' => '2026-01-01',
                'workday_rate' => 1.5,
                'holiday_rate' => 2.0,
                'break_mins' => 60,
                'min_ot_mins' => 30,
                'is_active' => true
            ]
        );

        // 3. 🏖️ สร้างวันหยุดบริษัทจำลอง (ปี 2026)
        $holidays = [
            ['date' => '2026-01-01', 'name' => 'วันขึ้นปีใหม่', 'description' => 'วันหยุดนักขัตฤกษ์'],
            ['date' => '2026-04-13', 'name' => 'วันสงกรานต์', 'description' => 'วันหยุดนักขัตฤกษ์'],
            ['date' => '2026-04-14', 'name' => 'วันสงกรานต์', 'description' => 'วันหยุดนักขัตฤกษ์'],
            ['date' => '2026-05-01', 'name' => 'วันแรงงานแห่งชาติ', 'description' => 'วันหยุดบริษัท'],
            ['date' => '2026-12-31', 'name' => 'วันสิ้นปี', 'description' => 'วันหยุดนักขัตฤกษ์'],
        ];

        foreach ($holidays as $holiday) {
            Holiday::firstOrCreate(['date' => $holiday['date']], $holiday);
        }

        // ==============================================================
        // 🌟 4. สร้างผู้จัดการ 3 แผนก (HR, IT, Sales)
        // ==============================================================
        $departments = \App\Models\Department::all();
        $managerMap = []; // ตัวแปรสำหรับจำว่าแผนกไหน ใครเป็น Manager (เก็บ Employee ID)

        $fakerTh = \Faker\Factory::create('th_TH');
        $fakerEn = \Faker\Factory::create('en_US');

        if ($departments->count() > 0) {
            foreach ($departments as $dept) {
                // หาตำแหน่งที่เป็น Manager ของแผนกนั้นๆ
                $managerPosition = \App\Models\Position::where('department_id', $dept->id)
                                    ->where('title', 'like', '%Manager%')
                                    ->first();

                $firstNameTh = $fakerTh->firstName;
                $lastNameTh = $fakerTh->lastName;
                
                // อีเมลผู้จัดการจะลงท้ายด้วย .mgr
                $firstNameEn = strtolower($fakerEn->firstName);
                $lastNameEn = strtolower($fakerEn->lastName);
                $email = $firstNameEn . '.mgr@rotisoft.com';

                $user = User::create([
                    'name' => $firstNameTh . ' ' . $lastNameTh,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]);

                $managerEmp = \App\Models\Employee::create([
                    'user_id' => $user->id,
                    'employee_code' => 'MGR-' . date('Y') . '-' . str_pad($dept->id, 3, '0', STR_PAD_LEFT),
                    'first_name' => $firstNameTh,
                    'last_name' => $lastNameTh,
                    'gender' => $fakerTh->randomElement(['Male', 'Female']),
                    'date_of_birth' => $fakerTh->dateTimeBetween('-50 years', '-35 years')->format('Y-m-d'), // ผู้จัดการอายุเยอะหน่อย
                    'national_id' => $fakerTh->numerify('1############'),
                    'phone_number' => $fakerTh->numerify('08########'),
                    'address' => $fakerTh->address,
                    
                    'department_id' => $dept->id,
                    'position_id' => $managerPosition ? $managerPosition->id : null,
                    
                    'hire_date' => $fakerTh->dateTimeBetween('-7 years', '-4 years')->format('Y-m-d'), // ทำงานมานานกว่า
                    'status' => 'Active',
                    'employee_type' => 'Monthly',
                    'employment_status' => 'Permanent',
                    'contract_type' => 'Full-time',
                    'marital_status' => $fakerTh->randomElement(['Single', 'Married']),
                    
                    'emergency_contact_name' => $fakerTh->name,
                    'emergency_contact_phone' => $fakerTh->numerify('08########'),
                ]);

                // จำ ID ของผู้จัดการคนนี้ไว้ เพื่อเอาไปผูกให้ลูกน้อง
                $managerMap[$dept->id] = $managerEmp->id;
            }
            $this->command->info('สร้างผู้จัดการ 3 แผนกสำเร็จแล้ว! 👨‍💼');
        }

        // ==============================================================
        // 🌟 5. สุ่มสร้างลูกน้อง 20 คน (พร้อมผูกหัวหน้างาน)
        // ==============================================================
        if ($departments->count() > 0) {
            for ($i = 1; $i <= 20; $i++) {
                $firstNameTh = $fakerTh->firstName;
                $lastNameTh = $fakerTh->lastName;

                $firstNameEn = strtolower($fakerEn->firstName);
                $lastNameEn = strtolower($fakerEn->lastName);
                $email = $firstNameEn . '.' . substr($lastNameEn, 0, 1) . '@rotisoft.com';

                while(User::where('email', $email)->exists()) {
                    $email = $firstNameEn . $fakerEn->numberBetween(1, 99) . '@rotisoft.com';
                }

                $user = User::create([
                    'name' => $firstNameTh . ' ' . $lastNameTh,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]);

                // สุ่มแผนก
                $randomDept = $departments->random();
                
                // สุ่มตำแหน่ง (แต่ต้องไม่เอาตำแหน่ง Manager เพราะสุ่มไปแล้วด้านบน)
                $deptPositions = \App\Models\Position::where('department_id', $randomDept->id)
                                    ->where('title', 'not like', '%Manager%')
                                    ->get();
                $randomPos = $deptPositions->count() > 0 ? $deptPositions->random() : null;

                \App\Models\Employee::create([
                    'user_id' => $user->id,
                    'employee_code' => 'EMP-' . date('Y') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'first_name' => $firstNameTh,
                    'last_name' => $lastNameTh,
                    'gender' => $fakerTh->randomElement(['Male', 'Female']),
                    'date_of_birth' => $fakerTh->dateTimeBetween('-35 years', '-22 years')->format('Y-m-d'),
                    'national_id' => $fakerTh->numerify('1############'),
                    'phone_number' => $fakerTh->numerify('08########'),
                    'address' => $fakerTh->address,
                    
                    'department_id' => $randomDept->id,
                    'position_id' => $randomPos ? $randomPos->id : null,
                    'manager_id' => $managerMap[$randomDept->id] ?? null, // 🌟 ผูกหัวหน้างานตามแผนก!
                    
                    'hire_date' => $fakerTh->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
                    'status' => 'Active',
                    'employee_type' => $fakerTh->randomElement(['Daily', 'Monthly']),
                    'employment_status' => $fakerTh->randomElement(['Probation', 'Permanent']),
                    'contract_type' => 'Full-time',
                    'marital_status' => $fakerTh->randomElement(['Single', 'Married']),
                    
                    'emergency_contact_name' => $fakerTh->name,
                    'emergency_contact_phone' => $fakerTh->numerify('08########'),
                ]);
            }
            $this->command->info('สร้างลูกน้อง 20 คนและผูกหัวหน้างานสำเร็จแล้ว! 👥');
        }
        
    }
}