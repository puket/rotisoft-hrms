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
                        <div class="alert alert-danger" id="error-box">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('/employees') }}" method="POST" novalidate>
                        @csrf

                        {{-- 📇 หมวดที่ 1: ข้อมูลส่วนตัว --}}
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-header bg-white fw-bold text-primary pb-0 border-0 pt-3">
                                <h5><i class="fas fa-user"></i> ข้อมูลส่วนตัว</h5><hr>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">รหัสพนักงาน <span class="text-danger">*</span></label>
                                        <input type="text" name="employee_code" class="form-control" value="{{ old('employee_code') }}" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">ชื่อ <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">นามสกุล <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">เพศ</label>
                                        <select name="gender" class="form-select">
                                            <option value="">-- เลือกเพศ --</option>
                                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>ชาย</option>
                                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>หญิง</option>
                                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>อื่นๆ</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">วันเกิด</label>
                                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">เลขบัตร ปชช.</label>
                                        <input type="text" name="national_id" class="form-control" value="{{ old('national_id') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">สถานภาพ</label>
                                        <select name="marital_status" class="form-select">
                                            <option value="">-- เลือก --</option>
                                            <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>โสด</option>
                                            <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>สมรส</option>
                                            <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>หย่าร้าง</option>
                                            <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>หม้าย</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">จำนวนบุตร (คน)</label>
                                        <input type="number" name="children_count" class="form-control" value="{{ old('children_count', 0) }}" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 📞 หมวดที่ 2: ข้อมูลการติดต่อ --}}
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-header bg-white fw-bold text-success pb-0 border-0 pt-3">
                                <h5><i class="fas fa-address-book"></i> ข้อมูลการติดต่อ</h5><hr>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">อีเมลติดต่อ (Contact Email)</label>
                                        <input type="email" name="email" id="contact_email" class="form-control" value="{{ old('email') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">เบอร์โทรศัพท์</label>
                                        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">ที่อยู่</label>
                                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">ชื่อผู้ติดต่อฉุกเฉิน</label>
                                        <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">เบอร์โทรผู้ติดต่อฉุกเฉิน</label>
                                        <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 💼 หมวดที่ 3: ข้อมูลการจ้างงาน --}}
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-header bg-white fw-bold text-warning pb-0 border-0 pt-3">
                                <h5><i class="fas fa-briefcase"></i> ข้อมูลการจ้างงาน</h5><hr>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">สังกัดบริษัท <span class="text-danger">*</span></label>
                                        <select name="company_id" id="company_id" class="form-select" required>
                                            <option value="">-- เลือกบริษัท --</option>
                                            @foreach($companies as $comp)
                                                <option value="{{ $comp->id }}" {{ old('company_id') == $comp->id ? 'selected' : '' }}>
                                                    {{ $comp->comp_code }} : {{ $comp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">สังกัดแผนก <span class="text-danger">*</span></label>
                                        <select name="department_id" id="department_id" class="form-select" required>
                                            <option value="">-- กรุณาเลือกบริษัทก่อน --</option>
                                            {{-- ลบ @foreach เก่าทิ้งไปเลยครับ --}}
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">ตำแหน่งงาน <span class="text-danger">*</span></label>
                                        <select name="position_id" id="position_id" class="form-select" required>
                                            <option value="">-- กรุณาเลือกแผนกก่อน --</option>
                                            {{-- ลบ @foreach เก่าทิ้งไปเลยครับ --}}
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">หัวหน้างาน</label>
                                        <select name="manager_id" id="manager_id" class="form-select">
                                            <option value="">-- ไม่มีหัวหน้า --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">ประเภทพนักงาน <span class="text-danger">*</span></label>
                                        <select name="employee_type" class="form-select" required>
                                            <option value="Monthly" {{ old('employee_type') == 'Monthly' ? 'selected' : '' }}>รายเดือน</option>
                                            <option value="Daily" {{ old('employee_type') == 'Daily' ? 'selected' : '' }}>รายวัน</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">สถานะการทำงาน <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select" required>
                                            <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active (ทำงานอยู่)</option>
                                            <option value="Suspended" {{ old('status') == 'Suspended' ? 'selected' : '' }}>Suspended (พักงาน)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">สถานะพนักงาน <span class="text-danger">*</span></label>
                                        <select name="employment_status" class="form-select" required>
                                            <option value="Probation" {{ old('employment_status', 'Probation') == 'Probation' ? 'selected' : '' }}>ทดลองงาน</option>
                                            <option value="Permanent" {{ old('employment_status') == 'Permanent' ? 'selected' : '' }}>พนักงานประจำ</option>
                                            <option value="Contract" {{ old('employment_status') == 'Contract' ? 'selected' : '' }}>สัญญาจ้าง</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">วันที่เริ่มงาน <span class="text-danger">*</span></label>
                                        <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date') }}" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">วันที่ผ่านโปร</label>
                                        <input type="date" name="probation_end_date" class="form-control" value="{{ old('probation_end_date') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 💰 หมวดที่ 4: ข้อมูลการเงินและสวัสดิการ --}}
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-header bg-white fw-bold text-info pb-0 border-0 pt-3">
                                <h5><i class="fas fa-money-check-alt"></i> ข้อมูลการเงินและสวัสดิการ</h5><hr>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">ชื่อธนาคาร</label>
                                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">เลขที่บัญชี</label>
                                        <input type="text" name="bank_account" class="form-control" value="{{ old('bank_account') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">เลขประจำตัวผู้เสียภาษี</label>
                                        <input type="text" name="tax_id" class="form-control" value="{{ old('tax_id') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">เลขประกันสังคม</label>
                                        <input type="text" name="social_security_number" class="form-control" value="{{ old('social_security_number') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 🔐 หมวดที่ 5: บัญชีเข้าระบบ (ESS / MSS Account) --}}
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-header bg-white fw-bold text-danger pb-0 border-0 pt-3">
                                <h5><i class="fas fa-key"></i> บัญชีเข้าระบบ (ESS / MSS Account)</h5><hr>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">อีเมลเข้าสู่ระบบ (Login Email)</label>
                                        {{-- เปลี่ยน name เป็น user_email และใส่ id="login_email" --}}
                                        <input type="email" name="user_email" id="login_email" class="form-control" value="{{ old('user_email') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">รหัสผ่านเริ่มต้น (Password) <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" placeholder="ตั้งรหัสผ่านอย่างน้อย 6 ตัว" autocomplete="new-password" required>
                                    </div>
                                </div>
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
<script>
    // 1. ดักจับการพิมพ์ที่ช่อง "อีเมลติดต่อ"
    document.getElementById('contact_email').addEventListener('input', function() {
        document.getElementById('login_email').value = this.value;
    });

    // 2. ถ้ามีกล่อง Error ให้เลื่อนหน้าจอขึ้นไปด้านบนสุดแบบนุ่มนวล
    window.onload = function() {
        let errorBox = document.getElementById('error-box');
        if (errorBox) {
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    };
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        const companySelect = document.getElementById('company_id');
        const departmentSelect = document.getElementById('department_id');
        const positionSelect = document.getElementById('position_id');
        const managerSelect = document.getElementById('manager_id');

        // เมื่อมีการเปลี่ยนบริษัท
        companySelect.addEventListener('change', function() {
            let companyId = this.value;
            
            // ล้างค่า Dropdown รอไว้เลย
            departmentSelect.innerHTML = '<option value="">-- กำลังโหลดข้อมูล... --</option>';
            positionSelect.innerHTML = '<option value="">-- เลือกตำแหน่ง --</option>';
            managerSelect.innerHTML = '<option value="">-- กำลังโหลดข้อมูล... --</option>'; // 🌟 2. ล้างค่าหัวหน้างาน

            if(companyId) {
                // เรียกข้อมูลแผนก
                fetch(`/get-departments/${companyId}`)
                    .then(response => response.json())
                    .then(data => {
                        departmentSelect.innerHTML = '<option value="">-- เลือกแผนก --</option>';
                        data.forEach(dept => {
                            departmentSelect.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
                        });
                    });

                // 🌟 3. เรียกข้อมูลหัวหน้างาน (พนักงานในบริษัทเดียวกัน)
                fetch(`/get-managers/${companyId}`)
                    .then(response => response.json())
                    .then(data => {
                        managerSelect.innerHTML = '<option value="">-- ไม่มีหัวหน้างาน --</option>'; // เผื่อกรณีเป็นระดับบนสุด
                        data.forEach(emp => {
                            // โชว์ชื่อ-นามสกุล ของพนักงาน
                            managerSelect.innerHTML += `<option value="${emp.id}">${emp.first_name} ${emp.last_name}</option>`;
                        });
                    });

            } else {
                departmentSelect.innerHTML = '<option value="">-- กรุณาเลือกบริษัทก่อน --</option>';
                managerSelect.innerHTML = '<option value="">-- กรุณาเลือกบริษัทก่อน --</option>'; // 🌟 4. คืนค่าเริ่มต้น
            }
        });

        // เมื่อมีการเปลี่ยนแผนก (ดึงตำแหน่งเหมือนเดิม)
        departmentSelect.addEventListener('change', function() {
            let departmentId = this.value;
            positionSelect.innerHTML = '<option value="">-- กำลังโหลดข้อมูล... --</option>';

            if(departmentId) {
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