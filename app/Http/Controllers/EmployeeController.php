<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    // ฟังก์ชันแสดงหน้ารายชื่อพนักงาน (และรองรับการค้นหา)
    public function index(Request $request)
    {
        // 1. รับค่าคำค้นหาจากหน้าเว็บ (ถ้ามี)
        $search = $request->input('search');

        // 2. ดึงข้อมูล พร้อมผูกตาราง แผนก, ตำแหน่ง และ หัวหน้างาน (manager)
        $query = Employee::with(['department', 'position', 'manager']);

        // 3. ถ้ามีการพิมพ์ค้นหา ให้กรองข้อมูลตาม ชื่อ, นามสกุล หรือ รหัสพนักงาน
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('employee_code', 'LIKE', "%{$search}%");
            });
        }

        // 4. แบ่งหน้าจอ (หน้าละ 10 คน) และพ่วงคำค้นหาไปกับปุ่มเปลี่ยนหน้าด้วย (withQueryString)
        $employees = $query->paginate(10)->withQueryString();
        
        return view('employees.index', compact('employees', 'search'));
    }

    // ส่วนฟังก์ชัน show($id) ที่เคยทำไว้ก่อนหน้านี้ ปล่อยไว้เหมือนเดิมได้เลยครับ
    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.show', compact('employee'));
    }

    // ฟังก์ชันเปิดหน้าฟอร์ม
    public function create()
    {
        // ถ้าไม่มีสิทธิ์ access-admin ให้เด้งออกไปพร้อม Error 403 (Forbidden)
        Gate::authorize('access-admin');

        $departments = Department::all();
        $positions = Position::all();
        $managers = Employee::where('status', 'Active')->get(); 

        return view('employees.create', compact('departments', 'positions', 'managers'));
    }

    // ฟังก์ชันบันทึกข้อมูล (สร้าง User และ Employee พร้อมกัน)
    public function store(Request $request)
    {
        Gate::authorize('access-admin');
        
        // 1. ตรวจสอบความถูกต้องของข้อมูล (Validation)
        $request->validate([
            'employee_code' => 'required|unique:employees,employee_code',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'department_id' => 'required',
            'position_id' => 'required',
            'hire_date' => 'required|date',
        ]);

        // 2. ใช้ DB Transaction เพื่อให้ชัวร์ว่า ข้อมูล User และ Employee ต้องถูกสร้างสำเร็จทั้งคู่
        DB::transaction(function () use ($request) {
            
            // สร้างบัญชีเข้าสู่ระบบ (ESS/MSS)
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // สร้างข้อมูลพนักงาน และผูกกับ User ด้านบน
            Employee::create([
                'user_id' => $user->id,
                'employee_code' => $request->employee_code,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'manager_id' => $request->manager_id, // ผูกหัวหน้างาน (อาจเป็น MD หรือ Manager)
                'hire_date' => $request->hire_date,
                'status' => 'Active',
            ]);
        });

        // บันทึกเสร็จให้เด้งกลับไปหน้ารายชื่อ พร้อมส่งข้อความแจ้งเตือน
        return redirect('/employees')->with('success', 'เพิ่มพนักงานใหม่ และสร้างบัญชี ESS สำเร็จแล้ว!');
    }
}