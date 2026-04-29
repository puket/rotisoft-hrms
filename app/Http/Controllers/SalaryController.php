<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Support\Facades\Gate;

class SalaryController extends Controller
{
    // แสดงหน้ารายชื่อพนักงานและข้อมูลเงินเดือน
    public function index()
    {
        Gate::authorize('is-hr'); 

        // ดึงข้อมูลพนักงาน พร้อมข้อมูลเงินเดือน (ถ้ามี)
        $employees = Employee::with(['department', 'salary'])
                             ->where('status', 'Active')
                             ->paginate(15);

        return view('salaries.index', compact('employees'));
    }

    // บันทึกหรืออัปเดตข้อมูลเงินเดือน
    public function store(Request $request)
    {
        Gate::authorize('is-hr');

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'base_salary' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
        ]);

        // 🌟 ดึงข้อมูลพนักงานเพื่อเอา company_id มาใช้แบบชัวร์ๆ
        $employee = Employee::findOrFail($request->employee_id);

        // อัปเดตข้อมูลถ้ามีอยู่แล้ว หรือสร้างใหม่ถ้ายังไม่มี (updateOrCreate)
        Salary::updateOrCreate(
            ['employee_id' => $request->employee_id],
            [
                'company_id' => $employee->company_id, // 🌟 ยัด company_id ตรงๆ ให้ชัดเจน
                'base_salary' => $request->base_salary,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'tax_id' => $request->tax_id,
            ]
        );

        return redirect()->back()->with('success', 'บันทึกข้อมูลฐานเงินเดือนและบัญชีธนาคารเรียบร้อยแล้ว ✅');
    }
}