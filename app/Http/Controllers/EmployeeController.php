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
    public function index(Request $request)
    {
        Gate::authorize('is-manager');

        $search = $request->input('search');
        $user = auth()->user();

        $query = Employee::with(['department', 'position', 'manager']);

        if (!Gate::allows('is-hr')) {
            $query->where('manager_id', $user->employee->id ?? null);
        }

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
        $employee = Employee::with([
            'department', 'position', 'manager', 'educations', 
            'experiences', 'trainings', 'documents'
        ])->findOrFail($id);
        
        return view('employees.show', compact('employee'));
    }

    public function edit($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $companies = \App\Models\Company::where('status', 'Active')->get();
        
        $departments = \App\Models\Department::where('company_id', $employee->company_id)->get();
        $positions = \App\Models\Position::where('department_id', $employee->department_id)->get();
        $managers = \App\Models\Employee::where('company_id', $employee->company_id)->get();

        return view('employees.edit', compact('employee', 'companies', 'departments', 'positions', 'managers'));
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('is-hr');

        $employee = Employee::findOrFail($id);

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
            'user_email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($employee->user_id)
            ],
            'password' => 'nullable|string|min:6',
        ]);

        DB::transaction(function () use ($validated, $employee) {
            $employeeData = $validated;
            unset($employeeData['password']);
            unset($employeeData['user_email']);
            $employee->update($employeeData);

            if ($employee->user) {
                $userDataToUpdate = [
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['user_email'],
                    'company_id' => $validated['company_id'] // 🌟 ยัด company_id เผื่อมีการย้ายบริษัท
                ];
                
                if (!empty($validated['password'])) {
                    $userDataToUpdate['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
                }

                $employee->user->update($userDataToUpdate);
            }
        });

        return redirect('/employees/' . $id)->with('success', 'อัปเดตข้อมูลพนักงานเรียบร้อยแล้ว ✅');
    }
    
    public function create()
    {
        Gate::authorize('is-hr');
        $companies = Company::where('status', 'Active')->get();
        return view('employees.create', compact('companies'));
    }

    public function store(Request $request)
    {
        Gate::authorize('is-hr');

        $validated = $request->validate([
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
            'user_email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'nullable|string|min:6',
        ]);

        DB::transaction(function () use ($validated) {
            // 🌟 สำคัญมาก! ตาราง User ไม่ได้ใช้ Trait ต้องยัด company_id ให้ตรงๆ
            $user = \App\Models\User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['user_email'],
                'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                'company_id' => $validated['company_id'], 
                'role' => 'staff' // กำหนด Default ให้เลย
            ]);

            $employeeData = $validated;
            $employeeData['user_id'] = $user->id;
            unset($employeeData['password']);
            unset($employeeData['user_email']);

            Employee::create($employeeData);
        });

        return redirect('/employees')->with('success', 'เพิ่มพนักงานใหม่และสร้างบัญชีเข้าระบบเรียบร้อยแล้ว ✅');
    }

    public function orgChart()
    {
        $employees = \App\Models\Employee::with(['department', 'position'])
                        ->where('status', 'Active')
                        ->get();

        $chartData = [];

        foreach ($employees as $emp) {
            $managerId = $emp->manager_id ? (string)$emp->manager_id : '';
            $name = $emp->first_name . ' ' . $emp->last_name;
            $position = $emp->position ? $emp->position->title : 'ไม่มีตำแหน่ง';
            $department = $emp->department ? $emp->department->name : '-';
            
            $formattedLabel = "
                <div class='text-center px-2 py-1'>
                    <strong class='text-primary'>{$name}</strong><br>
                    <small class='text-secondary'>{$position}</small><br>
                    <span class='badge bg-light text-dark border mt-1'>🏢 {$department}</span>
                </div>
            ";
            
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
        Gate::authorize('is-hr');
        $employee = Employee::findOrFail($id);

        $request->validate([
            'degree' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'graduation_year' => 'nullable|digits:4',
            'gpa' => 'nullable|numeric|min:0|max:4.00',
        ]);

        $data = $request->all();
        $data['company_id'] = $employee->company_id; // 🌟 ยัด company_id
        $employee->educations()->create($data);

        return back()->with('success', 'เพิ่มประวัติการศึกษาเรียบร้อยแล้ว 🎓');
    }

    public function storeExperience(Request $request, $id)
    {
        Gate::authorize('is-hr');
        $employee = Employee::findOrFail($id);

        $request->validate([
            'company_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date', 
            'job_description' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['company_id'] = $employee->company_id; // 🌟 ยัด company_id
        $employee->experiences()->create($data);

        return back()->with('success', 'เพิ่มประวัติการทำงานเรียบร้อยแล้ว 💼');
    }

    public function storeTraining(Request $request, $id)
    {
        Gate::authorize('is-hr');
        $employee = Employee::findOrFail($id);

        $request->validate([
            'course_name' => 'required|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'completion_date' => 'nullable|date',
            'certificate_no' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['company_id'] = $employee->company_id; // 🌟 ยัด company_id
        $employee->trainings()->create($data);

        return back()->with('success', 'เพิ่มประวัติการฝึกอบรมเรียบร้อยแล้ว 📜');
    }

    public function storeDocument(Request $request, $id)
    {
        Gate::authorize('is-hr');
        $employee = Employee::findOrFail($id);

        $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|in:ID_Card,House_Registration,Bookbank,Resume,Certificate,Contract,Other',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', 
        ]);

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $filename = $employee->id . '_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('employee_documents', $filename, 'public');
            
            $employee->documents()->create([
                'company_id' => $employee->company_id, // 🌟 ยัด company_id
                'document_name' => $request->document_name,
                'document_type' => $request->document_type,
                'file_path' => $path,
            ]);

            return back()->with('success', 'อัปโหลดเอกสารแนบเรียบร้อยแล้ว 📁');
        }

        return back()->withErrors(['error' => 'ไม่พบไฟล์ที่อัปโหลด กรุณาลองใหม่อีกครั้ง']);
    }
    
    public function getDepartments($company_id)
    {
        $departments = \App\Models\Department::where('company_id', $company_id)->get();
        return response()->json($departments);
    }

    public function getPositions($department_id)
    {
        $positions = \App\Models\Position::where('department_id', $department_id)->get();
        return response()->json($positions);
    }
    
    public function getManagers($company_id)
    {
        $managers = \App\Models\Employee::where('company_id', $company_id)->get();
        return response()->json($managers);
    }
}