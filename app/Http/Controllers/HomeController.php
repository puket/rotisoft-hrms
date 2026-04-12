<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;

class HomeController extends Controller
{
    public function index()
    {
        // 1. นับจำนวนพนักงานทั้งหมด (ที่ยังทำงานอยู่)
        $totalEmployees = Employee::where('status', 'Active')->count();

        // 2. นับจำนวนแผนก
        $totalDepartments = Department::count();

        // 3. ดึงจำนวนพนักงานแยกตามแผนก (ใช้วิธี withCount เพื่อความรวดเร็ว)
        $employeesByDept = Department::withCount(['employees' => function($query) {
            $query->where('status', 'Active');
        }])->get();

        // ส่งตัวแปรทั้งหมดไปที่หน้า View
        return view('home', compact('totalEmployees', 'totalDepartments', 'employeesByDept'));
    }
}