@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="page-title"><i class="bi bi-person-circle text-primary me-2"></i>ข้อมูลส่วนตัว</h1>
    <div class="page-subtitle">My Profile</div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <span class="avatar-circle mx-auto" style="width: 80px; height: 80px; font-size: 1.75rem;">
                        {{ mb_substr($employee->first_name, 0, 1) }}
                    </span>
                    <h4 class="mt-3 fw-bold mb-1">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                    <span class="badge-soft badge-soft-primary">{{ $employee->position->title ?? 'ไม่มีตำแหน่ง' }}</span>
                </div>

                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <th width="40%" class="text-muted fw-normal">รหัสพนักงาน:</th>
                            <td class="fw-semibold">{{ $employee->employee_code }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">แผนก:</th>
                            <td>{{ $employee->department->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">หัวหน้างาน:</th>
                            <td>
                                @if($employee->manager)
                                    {{ $employee->manager->first_name }} {{ $employee->manager->last_name }}
                                @else
                                    <span class="text-muted">- ระดับสูงสุด -</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">อีเมล:</th>
                            <td>{{ $employee->email }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">วันที่เริ่มงาน:</th>
                            <td>{{ \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock text-danger me-2"></i>เปลี่ยนรหัสผ่าน</h5>

                @if(session('success'))
                    <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/profile/password" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">รหัสผ่านปัจจุบัน</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <hr style="border-color: var(--rs-border);">
                    <div class="mb-3">
                        <label class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control" placeholder="อย่างน้อย 6 ตัวอักษร" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <input type="password" name="new_password_confirmation" class="form-control" placeholder="พิมพ์ให้ตรงกับรหัสผ่านใหม่" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>บันทึกรหัสผ่านใหม่</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
