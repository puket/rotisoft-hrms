<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class EmployeeController extends Controller
{
    // ฟังก์ชันแสดงหน้ารายชื่อพนักงาน (และรองรับการค้นหา)
public function index(Request $request)
    {
        // 🔒 ล็อคสิทธิ์: ต้องเป็น HR, Admin หรือ Manager เท่านั้น ถึงจะเข้าหน้านี้ได้
        Gate::authorize('view-employees-menu');

        $search = $request->input('search');
        $user = auth()->user();

        $query = Employee::with(['department', 'position', 'manager']);

        // 🔒 การกรองการมองเห็น: ถ้าไม่ใช่ Admin หรือ HR (แปลว่าเป็นแค่ Manager ธรรมดา) ให้เห็นแค่ลูกน้อง
        if (!Gate::allows('view-all-employees')) {
            $query->where('manager_id', $user->employee->id);
        }

        // 🔍 ระบบค้นหา
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('employee_code', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->paginate(10)->withQueryString();
        return view('employees.index', compact('employees', 'search'));
    }

    public function show($id)
    {
        Gate::authorize('view-employees-menu');
        
        $employee = Employee::findOrFail($id);
        $user = auth()->user();

        // 🔒 เช็คสิทธิ์ดูข้อมูล: ถ้าไม่ใช่ HR/Admin และไม่ใช่หัวหน้างานของคนๆ นี้ จะดูไม่ได้
        if (!Gate::allows('view-all-employees') && $employee->manager_id !== $user->employee->id) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าดูข้อมูลพนักงานท่านนี้');
        }

        return view('employees.show', compact('employee'));
    }

    public function edit($id)
    {
        // 🔒 เฉพาะ HR เท่านั้น
        Gate::authorize('edit-employees');

        $employee = Employee::findOrFail($id);
        $departments = Department::all();
        $positions = Position::all();
        $managers = Employee::where('status', 'Active')->where('id', '!=', $id)->get(); 

        return view('employees.edit', compact('employee', 'departments', 'positions', 'managers'));
    }

    // ฟังก์ชันสำหรับรับข้อมูลที่แก้แล้วมาบันทึกลง Database
    public function update(Request $request, $id)
    {
        // 🔒 เฉพาะ HR เท่านั้น
        Gate::authorize('edit-employees');

        // 1. ตรวจสอบข้อมูล
        $request->validate([
            'employee_code' => 'required|unique:employees,employee_code,'.$id, // ห้ามรหัสซ้ำคนอื่น แต่ซ้ำตัวเองได้
            'first_name' => 'required',
            'last_name' => 'required',
            'department_id' => 'required',
            'position_id' => 'required',
            'status' => 'required'
        ]);

        $employee = Employee::findOrFail($id);

        // 2. บันทึกข้อมูลแบบ Transaction (เซฟลงทั้งตาราง Employee และ User)
        DB::transaction(function () use ($request, $employee) {
            
            // อัปเดตข้อมูลพนักงาน
            $employee->update([
                'employee_code' => $request->employee_code,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'manager_id' => $request->manager_id,
                'status' => $request->status,
            ]);

            // อัปเดตชื่อในตาราง Users ด้วย ให้ตรงกัน
            if ($employee->user) {
                $employee->user->update([
                    'name' => $request->first_name . ' ' . $request->last_name
                ]);
            }
        });

        return redirect('/employees')->with('success', 'อัปเดตข้อมูลพนักงานเรียบร้อยแล้ว ✅');
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