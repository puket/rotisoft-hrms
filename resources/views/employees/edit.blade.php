@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-warning text-dark pt-3 pb-2 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">✏️ แก้ไขข้อมูลพนักงาน (Edit Employee)</h5>
                    <span class="badge bg-light text-dark">รหัส: {{ $employee->employee_code }}</span>
                </div>

                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="/employees/{{ $employee->id }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">1. ข้อมูลส่วนตัวพนักงาน</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">รหัสพนักงาน (Employee Code) <span class="text-danger">*</span></label>
                                <input type="text" name="employee_code" class="form-control" value="{{ $employee->employee_code }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ชื่อจริง (First Name) <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ $employee->first_name }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">นามสกุล (Last Name) <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" value="{{ $employee->last_name }}" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">เบอร์โทรศัพท์ (Phone)</label>
                                <input type="text" name="phone" class="form-control" value="{{ $employee->phone }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">อีเมล (Email) - <small class="text-danger">แก้ไขไม่ได้</small></label>
                                <input type="email" class="form-control" value="{{ $employee->email }}" disabled>
                            </div>
                        </div>

                        <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">2. ข้อมูลองค์กร และ สถานะ</h6>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">แผนก (Department) <span class="text-danger">*</span></label>
                                <select name="department_id" class="form-select" required>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ตำแหน่ง (Position) <span class="text-danger">*</span></label>
                                <select name="position_id" class="form-select" required>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}" {{ $employee->position_id == $pos->id ? 'selected' : '' }}>
                                            {{ $pos->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">หัวหน้างาน (Report To)</label>
                                <select name="manager_id" class="form-select">
                                    <option value="">-- ไม่มี (เป็นระดับสูงสุด) --</option>
                                    @foreach($managers as $mgr)
                                        <option value="{{ $mgr->id }}" {{ $employee->manager_id == $mgr->id ? 'selected' : '' }}>
                                            {{ $mgr->first_name }} {{ $mgr->last_name }} 
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-danger fw-bold">สถานะ</label>
                                <select name="status" class="form-select border-danger">
                                    <option value="Active" {{ $employee->status == 'Active' ? 'selected' : '' }}>Active (ทำงานอยู่)</option>
                                    <option value="Resigned" {{ $employee->status == 'Resigned' ? 'selected' : '' }}>Resigned (ลาออก)</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end border-top pt-3">
                            <a href="/employees" class="btn btn-secondary me-2">ย้อนกลับ</a>
                            <button type="submit" class="btn btn-warning fw-bold text-dark">💾 บันทึกการแก้ไข</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection