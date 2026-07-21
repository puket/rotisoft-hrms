@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-pencil-square text-warning me-2"></i>แก้ไขข้อมูลพนักงาน</h1>
        <div class="page-subtitle">Edit Employee — {{ $employee->first_name }} {{ $employee->last_name }}</div>
    </div>
    <a href="{{ url('/employees/' . $employee->id) }}" class="btn btn-soft">
        <i class="bi bi-arrow-left me-1"></i> กลับไปหน้าโปรไฟล์
    </a>
</div>

<div class="card">
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

        <form action="{{ url('/employees/' . $employee->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- 📇 หมวดที่ 1: ข้อมูลส่วนตัว --}}
        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon section-icon-primary"><i class="bi bi-person"></i></span>
                ข้อมูลส่วนตัว
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">รหัสพนักงาน <span class="text-danger">*</span></label>
                    <input type="text" name="employee_code" class="form-control" value="{{ old('employee_code', $employee->employee_code) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $employee->first_name) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $employee->last_name) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">เพศ</label>
                    <select name="gender" class="form-select">
                        <option value="">-- เลือกเพศ --</option>
                        <option value="Male" {{ old('gender', $employee->gender) == 'Male' ? 'selected' : '' }}>ชาย</option>
                        <option value="Female" {{ old('gender', $employee->gender) == 'Female' ? 'selected' : '' }}>หญิง</option>
                        <option value="Other" {{ old('gender', $employee->gender) == 'Other' ? 'selected' : '' }}>อื่นๆ</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">วันเกิด</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $employee->date_of_birth) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">เลขบัตร ปชช.</label>
                    <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $employee->national_id) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">สถานภาพ</label>
                    <select name="marital_status" class="form-select">
                        <option value="">-- เลือก --</option>
                        <option value="Single" {{ old('marital_status', $employee->marital_status) == 'Single' ? 'selected' : '' }}>โสด</option>
                        <option value="Married" {{ old('marital_status', $employee->marital_status) == 'Married' ? 'selected' : '' }}>สมรส</option>
                        <option value="Divorced" {{ old('marital_status', $employee->marital_status) == 'Divorced' ? 'selected' : '' }}>หย่าร้าง</option>
                        <option value="Widowed" {{ old('marital_status', $employee->marital_status) == 'Widowed' ? 'selected' : '' }}>หม้าย</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">จำนวนบุตร (คน)</label>
                    <input type="number" name="children_count" class="form-control" value="{{ old('children_count', $employee->children_count) }}" min="0">
                </div>
            </div>
        </div>

        {{-- 📞 หมวดที่ 2: ข้อมูลการติดต่อ --}}
        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon section-icon-success"><i class="bi bi-telephone"></i></span>
                ข้อมูลการติดต่อ
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">อีเมลติดต่อ (Contact Email)</label>
                    <input type="email" name="email" id="contact_email" class="form-control" value="{{ old('email', $employee->email ?? '') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">เบอร์โทรศัพท์</label>
                    <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $employee->phone_number) }}">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">ที่อยู่</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $employee->address) }}</textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อผู้ติดต่อฉุกเฉิน</label>
                    <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">เบอร์โทรผู้ติดต่อฉุกเฉิน</label>
                    <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}">
                </div>
            </div>
        </div>

        {{-- 💼 หมวดที่ 3: ข้อมูลการจ้างงาน --}}
        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon section-icon-warning"><i class="bi bi-briefcase"></i></span>
                ข้อมูลการจ้างงาน
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">สังกัดบริษัท <span class="text-danger">*</span></label>
                    <select name="company_id" id="company_id" class="form-select" required>
                        <option value="">-- เลือกบริษัท --</option>
                        @foreach($companies as $comp)
                            <option value="{{ $comp->id }}" {{ old('company_id', $employee->company_id) == $comp->id ? 'selected' : '' }}>
                                {{ $comp->comp_code }} : {{ $comp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">แผนก</label>
                    <select name="department_id" id="department_id" class="form-select">
                        <option value="">-- เลือกแผนก --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">ตำแหน่ง</label>
                    <select name="position_id" id="position_id" class="form-select">
                        <option value="">-- เลือกตำแหน่ง --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>{{ $pos->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">หัวหน้างาน</label>
                    <select name="manager_id" id="manager_id" class="form-select">
                        <option value="">-- ไม่มีหัวหน้า --</option>
                        @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}" {{ old('manager_id', $employee->manager_id) == $mgr->id ? 'selected' : '' }}>{{ $mgr->first_name }} {{ $mgr->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">ประเภทพนักงาน <span class="text-danger">*</span></label>
                    <select name="employee_type" class="form-select" required>
                        <option value="Monthly" {{ old('employee_type', $employee->employee_type) == 'Monthly' ? 'selected' : '' }}>รายเดือน</option>
                        <option value="Daily" {{ old('employee_type', $employee->employee_type) == 'Daily' ? 'selected' : '' }}>รายวัน</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">สถานะการทำงาน <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="Active" {{ old('status', $employee->status) == 'Active' ? 'selected' : '' }}>Active (ทำงานอยู่)</option>
                        <option value="Suspended" {{ old('status', $employee->status) == 'Suspended' ? 'selected' : '' }}>Suspended (พักงาน)</option>
                        <option value="Resigned" {{ old('status', $employee->status) == 'Resigned' ? 'selected' : '' }}>Resigned (ลาออก)</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">สถานะพนักงาน <span class="text-danger">*</span></label>
                    <select name="employment_status" class="form-select" required>
                        <option value="Probation" {{ old('employment_status', $employee->employment_status) == 'Probation' ? 'selected' : '' }}>ทดลองงาน</option>
                        <option value="Permanent" {{ old('employment_status', $employee->employment_status) == 'Permanent' ? 'selected' : '' }}>พนักงานประจำ</option>
                        <option value="Terminated" {{ old('employment_status', $employee->employment_status) == 'Terminated' ? 'selected' : '' }}>เลิกจ้าง</option>
                        <option value="Resigned" {{ old('employment_status', $employee->employment_status) == 'Resigned' ? 'selected' : '' }}>ลาออก</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">วันที่เริ่มงาน <span class="text-danger">*</span></label>
                    <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date', $employee->hire_date) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">วันที่ผ่านโปร</label>
                    <input type="date" name="probation_end_date" class="form-control" value="{{ old('probation_end_date', $employee->probation_end_date) }}">
                </div>
            </div>
        </div>

        {{-- 💰 หมวดที่ 4: ข้อมูลการเงินและสวัสดิการ --}}
        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon section-icon-info"><i class="bi bi-cash-coin"></i></span>
                ข้อมูลการเงินและสวัสดิการ
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">ชื่อธนาคาร</label>
                    <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $employee->bank_name) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">เลขที่บัญชี</label>
                    <input type="text" name="bank_account" class="form-control" value="{{ old('bank_account', $employee->bank_account) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">เลขประจำตัวผู้เสียภาษี</label>
                    <input type="text" name="tax_id" class="form-control" value="{{ old('tax_id', $employee->tax_id) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">เลขประกันสังคม</label>
                    <input type="text" name="social_security_number" class="form-control" value="{{ old('social_security_number', $employee->social_security_number) }}">
                </div>
            </div>
        </div>

        {{-- 🔐 หมวดที่ 5: บัญชีเข้าระบบ (ESS / MSS Account) --}}
        <div class="form-section">
            <div class="form-section-title">
                <span class="section-icon section-icon-danger"><i class="bi bi-key"></i></span>
                บัญชีเข้าระบบ (ESS / MSS Account)
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">อีเมลเข้าสู่ระบบ (Login Email)</label>
                    {{-- เปลี่ยน name เป็น user_email และใส่ id="login_email" --}}
                    <input type="email" name="user_email" id="login_email" class="form-control" value="{{ old('user_email', $employee->user->email ?? '') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">เปลี่ยนรหัสผ่านใหม่ (Password)</label>
                    {{-- 🌟 จุดสำคัญ: ไม่ใส่ required และบอกว่าปล่อยว่างได้ --}}
                    <input type="password" name="password" class="form-control" placeholder="** ปล่อยว่างไว้ หากไม่ต้องการเปลี่ยนรหัสผ่าน **" autocomplete="new-password">
                    <small class="text-muted">หากต้องการรีเซ็ตรหัสผ่านให้พนักงาน ให้พิมพ์รหัสใหม่ที่นี่ (อย่างน้อย 6 ตัว)</small>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end border-top pt-3 mt-2" style="border-color: var(--rs-border) !important;">
            <a href="{{ url('/employees/' . $employee->id) }}" class="btn btn-soft me-2">ยกเลิก</a>
            <button type="submit" class="btn btn-warning fw-bold text-dark"><i class="bi bi-save me-1"></i>บันทึกการแก้ไข</button>
        </div>
        </form>
    </div>
</div>
<script>
    // ดักจับการพิมพ์ที่ช่อง "อีเมลติดต่อ"
    document.getElementById('contact_email').addEventListener('input', function() {
        // ให้ช่อง "อีเมลเข้าสู่ระบบ" เปลี่ยนตามทันที
        document.getElementById('login_email').value = this.value;
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const companySelect = document.getElementById('company_id');
        const departmentSelect = document.getElementById('department_id');
        const positionSelect = document.getElementById('position_id');

        // เมื่อมีการเปลี่ยนบริษัท
        companySelect.addEventListener('change', function() {
            let companyId = this.value;

            // ล้างค่า Dropdown แผนกและตำแหน่งรอไว้เลย
            departmentSelect.innerHTML = '<option value="">-- กำลังโหลดข้อมูล... --</option>';
            positionSelect.innerHTML = '<option value="">-- เลือกตำแหน่ง --</option>';

            if(companyId) {
                // เรียกข้อมูลแผนกผ่าน API ที่เราสร้างไว้
                fetch(`/get-departments/${companyId}`)
                    .then(response => response.json())
                    .then(data => {
                        departmentSelect.innerHTML = '<option value="">-- เลือกแผนก --</option>';
                        data.forEach(dept => {
                            departmentSelect.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
                        });
                    });
            } else {
                departmentSelect.innerHTML = '<option value="">-- กรุณาเลือกบริษัทก่อน --</option>';
            }
        });

        // เมื่อมีการเปลี่ยนแผนก
        departmentSelect.addEventListener('change', function() {
            let departmentId = this.value;

            // ล้างค่า Dropdown ตำแหน่งรอไว้เลย
            positionSelect.innerHTML = '<option value="">-- กำลังโหลดข้อมูล... --</option>';

            if(departmentId) {
                // เรียกข้อมูลตำแหน่งผ่าน API
                fetch(`/get-positions/${departmentId}`)
                    .then(response => response.json())
                    .then(data => {
                        positionSelect.innerHTML = '<option value="">-- เลือกตำแหน่ง --</option>';
                        data.forEach(pos => {
                            positionSelect.innerHTML += `<option value="${pos.id}">${pos.title}</option>`;
                        });
                    });
            } else {
                positionSelect.innerHTML = '<option value="">-- กรุณาเลือกแผนกก่อน --</option>';
            }
        });

    });
</script>
@endsection
