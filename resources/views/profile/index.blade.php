@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-primary text-white pt-3 pb-2">
                    <h5 class="fw-bold mb-0">👤 ข้อมูลส่วนตัว (My Profile)</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <span class="fs-1">🧑‍💼</span>
                        </div>
                        <h4 class="mt-3 fw-bold">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                        <span class="badge bg-info text-dark">{{ $employee->position->title ?? 'ไม่มีตำแหน่ง' }}</span>
                    </div>

                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="40%" class="text-muted">รหัสพนักงาน:</th>
                                <td class="fw-bold">{{ $employee->employee_code }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">แผนก:</th>
                                <td>{{ $employee->department->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">หัวหน้างาน:</th>
                                <td>
                                    @if($employee->manager)
                                        {{ $employee->manager->first_name }} {{ $employee->manager->last_name }}
                                    @else
                                        <span class="text-muted">- ระดับสูงสุด -</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">อีเมล:</th>
                                <td>{{ $employee->email }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">วันที่เริ่มงาน:</th>
                                <td>{{ \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-secondary text-white pt-3 pb-2">
                    <h5 class="fw-bold mb-0">🔒 เปลี่ยนรหัสผ่าน (Change Password)</h5>
                </div>
                <div class="card-body p-4">
                    
                    @if(session('success'))
                        <div class="alert alert-success fw-bold">{{ session('success') }}</div>
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
                            <label class="form-label fw-bold">รหัสผ่านปัจจุบัน</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label fw-bold">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control" placeholder="อย่างน้อย 6 ตัวอักษร" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <input type="password" name="new_password_confirmation" class="form-control" placeholder="พิมพ์ให้ตรงกับรหัสผ่านใหม่" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark fw-bold">💾 บันทึกรหัสผ่านใหม่</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection