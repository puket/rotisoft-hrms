<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    // 1. หน้าแสดงรายการบริษัททั้งหมด
    public function index()
    {
        $companies = Company::latest()->paginate(10);
        return view('companies.index', compact('companies'));
    }

    // 2. หน้าฟอร์มเพิ่มบริษัท
    public function create()
    {
        return view('companies.create');
    }

    // 3. บันทึกข้อมูลบริษัทใหม่
    public function store(Request $request)
    {
        $request->validate([
            'comp_code' => 'required|string|max:50|unique:companies,comp_code',
            'name' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:13',
            'status' => 'required|in:Active,Inactive',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // โลโก้ไม่เกิน 2MB
        ]);

        $data = $request->except(['_token', '_method', 'logo']);

        // ถ้ามีการอัปโหลดโลโก้
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('company_logos', 'public');
        }

        Company::create($data);

        return redirect()->route('companies.index')->with('success', 'เพิ่มข้อมูลบริษัทเรียบร้อยแล้ว 🏢');
    }

    // 4. หน้าฟอร์มแก้ไขบริษัท
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    // 5. บันทึกการแก้ไขข้อมูล
    public function update(Request $request, Company $company)
    {
        $request->validate([
            // ยกเว้นการเช็ค comp_code ซ้ำกับของตัวเอง
            'comp_code' => 'required|string|max:50|unique:companies,comp_code,' . $company->id,
            'name' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:13',
            'status' => 'required|in:Active,Inactive',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('logo');

        // ถ้ามีการอัปโหลดโลโก้ใหม่ ให้ลบของเก่าทิ้งก่อน
        if ($request->hasFile('logo')) {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('company_logos', 'public');
        }

        $company->update($data);

        return redirect()->route('companies.index')->with('success', 'อัปเดตข้อมูลบริษัทเรียบร้อยแล้ว 💾');
    }

    // 6. ลบข้อมูลบริษัท
    public function destroy(Company $company)
    {
        // ลบไฟล์โลโก้ออกจากเซิร์ฟเวอร์ด้วย (ถ้ามี)
        if ($company->logo_path) {
            Storage::disk('public')->delete($company->logo_path);
        }
        
        $company->delete();

        return redirect()->route('companies.index')->with('success', 'ลบข้อมูลบริษัทเรียบร้อยแล้ว 🗑️');
    }
}