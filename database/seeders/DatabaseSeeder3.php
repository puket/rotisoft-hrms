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

        // (ถ้าคุณมี Seeder อื่นๆ เช่น DepartmentSeeder สามารถเรียกใช้ตรงนี้ได้)
        // $this->call([
        //     DepartmentSeeder::class,
        // ]);
    }
}