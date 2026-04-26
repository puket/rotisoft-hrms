<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Models\Company;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

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
        // ดึงข้อมูลพนักงาน พร้อมกับข้อมูลแผนก, ตำแหน่ง, หัวหน้างาน และประวัติต่างๆ
        $employee = Employee::with([
            'department', 
            'position', 
            'manager', 
            'educations', 
            'experiences', 
            'trainings', 
            'documents'
        ])->findOrFail($id);

        return view('employees.show', compact('employee'));
    }

    public function edit($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $companies = \App\Models\Company::where('status', 'Active')->get();
        
        // 🌟 ดึงข้อมูลเฉพาะของบริษัทและแผนกที่พนักงานคนนี้สังกัดอยู่ปัจจุบัน
        $departments = \App\Models\Department::where('company_id', $employee->company_id)->get();
        $positions = \App\Models\Position::where('department_id', $employee->department_id)->get();
        $managers = \App\Models\Employee::where('company_id', $employee->company_id)->get();

        return view('employees.edit', compact('employee', 'companies', 'departments', 'positions', 'managers'));
    }

    public function update(Request $request, $id)
    {
        // 🔒 เฉพาะ HR เท่านั้น
        Gate::authorize('edit-employees');

        // 🌟 1. ดึงข้อมูลพนักงานมาก่อน เพื่อเอา user_id ไปบอกให้ Laravel ละเว้น
        $employee = Employee::findOrFail($id);

        // 2. ตรวจสอบข้อมูลทั้งหมด
        $validated = $request->validate([
            'employee_code' => 'required|unique:employees,employee_code,'.$id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'national_id' => 'nullable|string|max:20',
            'marital_status' => 'nullable|in:Single,Married,Divorced,Widowed',
            'children_count' => 'nullable|integer|min:0',
            
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',

            'company_id' => 'required|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'manager_id' => 'nullable|exists:employees,id',
            
            'hire_date' => 'required|date',
            'status' => 'required|in:Active,Resigned,Suspended',
            'employee_type' => 'required|in:Daily,Monthly',
            'employment_status' => 'required|in:Probation,Permanent,Resigned,Terminated',
            'probation_end_date' => 'nullable|date',

            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:30',
            'tax_id' => 'nullable|string|max:20',
            'social_security_number' => 'nullable|string|max:20',
            
            // อีเมลเข้าระบบ (เก็บลงตาราง users)
            'user_email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($employee->user_id)
            ],
            'password' => 'nullable|string|min:6',
        ]);

        $employee = Employee::findOrFail($id);

        // 3. บันทึกข้อมูลแบบ Transaction ป้องกันระบบล่มกลางทาง
        DB::transaction(function () use ($validated, $employee) {
            
            // อัปเดตข้อมูลตารางพนักงาน
            // ตัด email และ password ออกก่อนอัปเดตลงตาราง employees (ถ้าตารางนี้ไม่มีฟิลด์นี้แล้ว)
            $employeeData = $validated;
            unset($employeeData['password']);
            unset($employeeData['user_email']);
            $employee->update($employeeData);

            // อัปเดตชื่อ (และอีเมล) ในตาราง Users ด้วย ให้ตรงกัน
            if ($employee->user) {
                    $userDataToUpdate = [
                        'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                        'email' => $validated['user_email']
                    ];
                
                // ถ้ามีการแก้อีเมล ให้แก้ในตาราง Users ด้วย
                if (!empty($validated['password'])) {
                    $userDataToUpdate['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
                }

                $employee->user->update($userDataToUpdate);
            }
        });

        // กลับไปหน้ารายละเอียดโปรไฟล์ (หรือจะเปลี่ยนเป็น '/employees' ก็ได้ครับ)
        return redirect('/employees/' . $id)->with('success', 'อัปเดตข้อมูลพนักงานเรียบร้อยแล้ว ✅');
    }
    
    // ฟังก์ชันเปิดหน้าฟอร์ม
    public function create()
    {
        // ถ้าไม่มีสิทธิ์ access-admin ให้เด้งออกไปพร้อม Error 403 (Forbidden)
        Gate::authorize('access-admin');

        $companies = Company::where('status', 'Active')->get();
        //$departments = Department::all();
        //$positions = Position::all();
        //$managers = Employee::where('status', 'Active')->get(); 

        return view('employees.create', compact('companies'));
    }

    public function store(Request $request)
    {
        // 🌟 วางคำสั่งนี้เพื่อดักจับ ถ้าหน้าจอเปลี่ยนเป็นตัวหนังสือ HELLO แสดงว่าฟอร์มทำงาน!
        //dd('HELLO! ปุ่มทำงานแล้ว!', $request->all());
        // 🔒 เฉพาะ HR เท่านั้น
        Gate::authorize('edit-employees');

        // 1. ตรวจสอบข้อมูลทั้งหมด
        $validated = $request->validate([
            // สำหรับตาราง Users
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',

            // สำหรับตาราง Employees
            'employee_code' => 'required|unique:employees,employee_code',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'national_id' => 'nullable|string|max:20',
            'marital_status' => 'nullable|in:Single,Married,Divorced,Widowed',
            'children_count' => 'nullable|integer|min:0',
            
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',

            'company_id' => 'required|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'manager_id' => 'nullable|exists:employees,id',
            
            'hire_date' => 'required|date',
            'status' => 'required|in:Active,Resigned,Suspended',
            'employee_type' => 'required|in:Daily,Monthly',
            'employment_status' => 'required|in:Probation,Permanent,Contract,Resigned,Terminated',
            'probation_end_date' => 'nullable|date',

            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:30',
            'tax_id' => 'nullable|string|max:20',
            'social_security_number' => 'nullable|string|max:20',

             // อีเมลเข้าระบบ (เก็บลงตาราง users)
            'user_email' => 'nullable|email|max:255|unique:users,email,' . ($employee->user_id ?? 'NULL'),
            'password' => 'nullable|string|min:6',
        ]);

        // 2. บันทึกข้อมูลแบบ Transaction
        DB::transaction(function () use ($validated) {
            
            // 2.1 สร้างบัญชีผู้ใช้งาน (User) ก่อน
            $user = \App\Models\User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['user_email'],
                'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            ]);

            // 2.2 เตรียมข้อมูลสำหรับตาราง Employee
            $employeeData = $validated;
            $employeeData['user_id'] = $user->id; // เอา ID จากข้อ 2.1 มาผูก
            unset($employeeData['password']); // ตัด password ออกเพราะตาราง Employee ไม่มีฟิลด์นี้
            unset($employeeData['user_email']);

            // 2.3 สร้างข้อมูลพนักงาน (Employee)
            Employee::create($employeeData);
        });

        return redirect('/employees')->with('success', 'เพิ่มพนักงานใหม่และสร้างบัญชีเข้าระบบเรียบร้อยแล้ว ✅');
    }

    // ฟังก์ชันบันทึกข้อมูล (สร้าง User และ Employee พร้อมกัน)
    public function storexx(Request $request)
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

    // ==========================================
    // 🌟 ระบบผังองค์กร (Organization Chart)
    // ==========================================
    public function orgChart()
    {
        // 🔒 ทุกคนที่เข้าระบบได้ ควรเห็นผังบริษัทตัวเอง (เพื่อให้รู้ว่าใครเป็นหัวหน้าใคร)
        $employees = \App\Models\Employee::with(['department', 'position'])
                        ->where('status', 'Active')
                        ->get();

        $chartData = [];

        foreach ($employees as $emp) {
            // ดึง ID ของหัวหน้า (ถ้าไม่มีให้เป็นค่าว่างแปลว่าเป็นเบอร์ 1 ของบริษัท)
            $managerId = $emp->manager_id ? (string)$emp->manager_id : '';
            
            // เตรียมข้อมูลสำหรับโชว์ในการ์ดแต่ละใบ (ใช้ HTML ได้เลย)
            $name = $emp->first_name . ' ' . $emp->last_name;
            $position = $emp->position ? $emp->position->title : 'ไม่มีตำแหน่ง';
            $department = $emp->department ? $emp->department->name : '-';
            
            // ตกแต่งหน้าตาของการ์ด (Node)
            $formattedLabel = "
                <div class='text-center px-2 py-1'>
                    <strong class='text-primary'>{$name}</strong><br>
                    <small class='text-secondary'>{$position}</small><br>
                    <span class='badge bg-light text-dark border mt-1'>🏢 {$department}</span>
                </div>
            ";

            // ใส่ข้อมูลลง Array ตามรูปแบบที่ Google Charts ต้องการ
            // [ [ ID, HTML Layout ], Manager_ID, Tooltip ]
            $chartData[] = [
                ['v' => (string)$emp->id, 'f' => $formattedLabel],
                $managerId,
                $position
            ];
        }

        return view('employees.org-chart', compact('chartData'));
    }
    
    public function storeEducation(Request $request, $id)
    {
        Gate::authorize('edit-employees'); // 🔒 เช็คสิทธิ์

        $request->validate([
            'degree' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'graduation_year' => 'nullable|digits:4',
            'gpa' => 'nullable|numeric|min:0|max:4.00',
        ]);

        $employee = Employee::findOrFail($id);
        
        // บันทึกข้อมูลลงตาราง employee_educations
        $employee->educations()->create($request->all());

        // บันทึกเสร็จ ให้เด้งกลับมาหน้าเดิม พร้อมข้อความแจ้งเตือน
        return back()->with('success', 'เพิ่มประวัติการศึกษาเรียบร้อยแล้ว 🎓');
    }

    // ==========================================
    // บันทึกประวัติการทำงาน (Experiences)
    // ==========================================
    public function storeExperience(Request $request, $id)
    {
        // 🔒 เช็คสิทธิ์เฉพาะ HR
        Gate::authorize('edit-employees'); 

        $request->validate([
            'company_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            // ตรวจสอบว่าถ้ามีวันสิ้นสุด ต้องไม่ใช่วันที่ก่อนวันเริ่มงาน
            'end_date' => 'nullable|date|after_or_equal:start_date', 
            'job_description' => 'nullable|string',
        ]);

        $employee = Employee::findOrFail($id);
        
        // บันทึกลงตาราง employee_experiences
        $employee->experiences()->create($request->all());

        return back()->with('success', 'เพิ่มประวัติการทำงานเรียบร้อยแล้ว 💼');
    }

    // ==========================================
    // บันทึกประวัติการฝึกอบรม (Trainings)
    // ==========================================
    public function storeTraining(Request $request, $id)
    {
        // 🔒 เช็คสิทธิ์เฉพาะ HR
        Gate::authorize('edit-employees');

        $request->validate([
            'course_name' => 'required|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'completion_date' => 'nullable|date',
            'certificate_no' => 'nullable|string|max:255',
        ]);

        $employee = Employee::findOrFail($id);
        
        // บันทึกลงตาราง employee_trainings
        $employee->trainings()->create($request->all());

        return back()->with('success', 'เพิ่มประวัติการฝึกอบรมเรียบร้อยแล้ว 📜');
    }

    // ==========================================
    // บันทึกและอัปโหลดเอกสารแนบ (Documents)
    // ==========================================
    public function storeDocument(Request $request, $id)
    {
        // 🔒 เช็คสิทธิ์เฉพาะ HR
        Gate::authorize('edit-employees');

        $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|in:ID_Card,House_Registration,Bookbank,Resume,Certificate,Contract,Other',
            // ตรวจสอบไฟล์: ต้องเป็น pdf, jpg, jpeg, png และขนาดไม่เกิน 5MB (5120 KB)
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', 
        ]);

        $employee = Employee::findOrFail($id);

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            
            // ตั้งชื่อไฟล์ใหม่ ป้องกันชื่อซ้ำ (รหัสพนักงาน_เวลา_ชื่อไฟล์เดิม)
            $filename = $employee->id . '_' . time() . '_' . $file->getClientOriginalName();
            
            // อัปโหลดไฟล์ไปไว้ที่โฟลเดอร์ storage/app/public/employee_documents
            $path = $file->storeAs('employee_documents', $filename, 'public');

            // บันทึกข้อมูลลงตาราง employee_documents
            $employee->documents()->create([
                'document_name' => $request->document_name,
                'document_type' => $request->document_type,
                'file_path' => $path, // เก็บพาร์ทไฟล์ไว้ดึงมาแสดง
            ]);

            return back()->with('success', 'อัปโหลดเอกสารแนบเรียบร้อยแล้ว 📁');
        }

        return back()->withErrors(['error' => 'ไม่พบไฟล์ที่อัปโหลด กรุณาลองใหม่อีกครั้ง']);
    }
    
    // ดึงข้อมูลแผนกตาม ID บริษัทที่เลือก (ส่งกลับเป็น JSON)
    public function getDepartments($company_id)
    {
        $departments = \App\Models\Department::where('company_id', $company_id)->get();
        return response()->json($departments);
    }

    // ดึงข้อมูลตำแหน่งตาม ID แผนกที่เลือก (ส่งกลับเป็น JSON)
    public function getPositions($department_id)
    {
        $positions = \App\Models\Position::where('department_id', $department_id)->get();
        return response()->json($positions);
    }
    
    // ดึงข้อมูลพนักงานในบริษัทเดียวกันมาเป็นตัวเลือกหัวหน้างาน
    public function getManagers($company_id)
    {
        // ดึงพนักงานทุกคนในบริษัทนี้ (คุณอาจจะกรองเพิ่มเฉพาะคนที่เป็น Manager ก็ได้โดยเพิ่ม ->whereHas('position', ...))
        $managers = \App\Models\Employee::where('company_id', $company_id)->get();
        return response()->json($managers);
    }

}