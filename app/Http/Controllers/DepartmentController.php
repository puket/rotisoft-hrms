<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Company;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        // ดึงข้อมูลแผนก พร้อมชื่อบริษัทและชื่อแผนกแม่
        $departments = Department::with(['company', 'parent'])->latest()->paginate(10);
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        // ดึงเฉพาะบริษัทที่ยัง Active อยู่มาให้เลือก
        $companies = Company::where('status', 'Active')->get();
        $departments = Department::all(); 
        return view('departments.create', compact('companies', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'parent_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:255',
        ]);

        Department::create($request->except(['_token', '_method']));
        return redirect()->route('departments.index')->with('success', 'เพิ่มข้อมูลแผนกเรียบร้อยแล้ว 📂');
    }

    public function edit(Department $department)
    {
        $companies = Company::where('status', 'Active')->get();
        // ดึงแผนกทั้งหมดมา ยกเว้นตัวเอง (กันการเลือกตัวเองเป็นแผนกแม่)
        $departments = Department::where('id', '!=', $department->id)->get();
        return view('departments.edit', compact('department', 'companies', 'departments'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'parent_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:255',
        ]);

        $department->update($request->except(['_token', '_method']));
        return redirect()->route('departments.index')->with('success', 'อัปเดตข้อมูลแผนกเรียบร้อยแล้ว 💾');
    }

    public function destroy(Department $department)
    {
        // Safe Deletion Rule: ห้ามลบแผนกที่ยังมีแผนกย่อย/ตำแหน่งงาน/พนักงานผูกอยู่
        // ต้องย้ายข้อมูลเหล่านี้ออกไปที่อื่นก่อน ป้องกันข้อมูลกำพร้า
        if ($department->children()->exists()) {
            return redirect()->route('departments.index')->with(
                'error',
                'ไม่สามารถลบแผนกนี้ได้ เนื่องจากยังมีแผนกย่อยสังกัดอยู่ กรุณาย้ายหรือลบแผนกย่อยออกก่อน'
            );
        }

        if ($department->positions()->exists()) {
            return redirect()->route('departments.index')->with(
                'error',
                'ไม่สามารถลบแผนกนี้ได้ เนื่องจากยังมีตำแหน่งงานสังกัดอยู่ กรุณาย้ายหรือลบตำแหน่งงานออกก่อน'
            );
        }

        if ($department->employees()->exists()) {
            return redirect()->route('departments.index')->with(
                'error',
                'ไม่สามารถลบแผนกนี้ได้ เนื่องจากยังมีพนักงานสังกัดอยู่ กรุณาย้ายพนักงานออกจากแผนกนี้ก่อน'
            );
        }

        $department->delete();
        return redirect()->route('departments.index')->with('success', 'ลบข้อมูลแผนกเรียบร้อยแล้ว 🗑️');
    }
}
