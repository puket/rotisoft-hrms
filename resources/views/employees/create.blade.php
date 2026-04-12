@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white pt-3 pb-2">
                    <h5 class="fw-bold">➕ เพิ่มพนักงานใหม่ (New Employee Registration)</h5>
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

                    <form action="/employees" method="POST">
                        @csrf
                        
                        <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">1. ข้อมูลส่วนตัวพนักงาน</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">รหัสพนักงาน (Employee Code) <span class="text-danger">*</span></label>
                                <input type="text" name="employee_code" class="form-control" placeholder="เช่น EMP-001" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ชื่อจริง (First Name) <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">นามสกุล (Last Name) <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">เบอร์โทรศัพท์ (Phone)</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">วันที่เริ่มงาน (Hire Date) <span class="text-danger">*</span></label>
                                <input type="date" name="hire_date" class="form-control" required>
                            </div>
                        </div>

                        <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">2. ข้อมูลองค์กร (Organization)</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">แผนก (Department) <span class="text-danger">*</span></label>
                                <select name="department_id" class="form-select" required>
                                    <option value="">-- เลือกแผนก --</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ตำแหน่ง (Position) <span class="text-danger">*</span></label>
                                <select name="position_id" class="form-select" required>
                                    <option value="">-- เลือกตำแหน่ง --</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}">{{ $pos->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">หัวหน้างาน (Report To)</label>
                                <select name="manager_id" class="form-select">
                                    <option value="">-- ไม่มี (เป็นระดับสูงสุด) --</option>
                                    @foreach($managers as $mgr)
                                        <option value="{{ $mgr->id }}">{{ $mgr->first_name }} {{ $mgr->last_name }} ({{ $mgr->position ? $mgr->position->title : 'ไม่มีตำแหน่ง' }})</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">พนักงานทั่วไปต้องเลือก Manager, ส่วน Manager ให้เลือก MD</small>
                            </div>
                        </div>

                        <h6 class="text-success fw-bold mb-3 border-bottom pb-2">3. บัญชีเข้าระบบ (ESS / MSS Account)</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">อีเมลเข้าสู่ระบบ (Email) <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="employee@rotisoft.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">รหัสผ่านเริ่มต้น (Password) <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="ตั้งรหัสผ่านอย่างน้อย 6 ตัว" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end border-top pt-3">
                            <a href="/employees" class="btn btn-secondary me-2">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary fw-bold">💾 บันทึกข้อมูลพนักงาน</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection