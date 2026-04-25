<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::with(['company', 'department'])->latest()->paginate(10);
        return view('positions.index', compact('positions'));
    }

    public function create()
    {
        $companies = Company::where('status', 'Active')->get();
        $departments = Department::all();
        return view('positions.create', compact('companies', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'job_level' => 'required|in:MD,Manager,Staff',
            'job_description' => 'nullable|string',
        ]);

        Position::create($request->except(['_token', '_method']));
        return redirect()->route('positions.index')->with('success', 'เพิ่มตำแหน่งงานเรียบร้อยแล้ว 🎯');
    }

    public function edit(Position $position)
    {
        $companies = Company::where('status', 'Active')->get();
        $departments = Department::all();
        return view('positions.edit', compact('position', 'companies', 'departments'));
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'job_level' => 'required|in:MD,Manager,Staff',
            'job_description' => 'nullable|string',
        ]);

        $position->update($request->except(['_token', '_method']));
        return redirect()->route('positions.index')->with('success', 'อัปเดตข้อมูลตำแหน่งงานเรียบร้อยแล้ว 💾');
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return redirect()->route('positions.index')->with('success', 'ลบข้อมูลตำแหน่งงานเรียบร้อยแล้ว 🗑️');
    }
}