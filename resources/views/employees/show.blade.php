@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="/employees" class="btn btn-secondary btn-sm fw-bold">⬅️ ย้อนกลับไปหน้ารายชื่อ</a>
                
                @can('edit-employees')
                    <a href="/employees/{{ $employee->id }}/edit" class="btn btn-warning btn-sm fw-bold text-dark">✏️ แก้ไขข้อมูล</a>
                @endcan
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white pt-4 pb-3 text-center border-0">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow" style="width: 90px; height: 90px;">
                        <span class="fs-1">🧑‍💼</span>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                    <span class="badge bg-light text-primary fs-6">{{ $employee->position->title ?? 'ไม่มีตำแหน่ง' }}</span>
                </div>

                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4 mb-md-0 border-end">
                            <h6 class="text-muted fw-bold mb-3 border-bottom pb-2">📋 ข้อมูลส่วนบุคคล</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted" width="40%">รหัสพนักงาน:</td>
                                    <td class="fw-bold text-dark">{{ $employee->employee_code }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">เบอร์โทรศัพท์:</td>
                                    <td class="text-dark">{{ $employee->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">อีเมล (บัญชีระบบ):</td>
                                    <td class="text-dark">{{ $employee->email }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted fw-bold mb-3 border-bottom pb-2">🏢 โครงสร้างองค์กร</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted" width="40%">แผนก:</td>
                                    <td class="text-dark fw-bold">{{ $employee->department->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">หัวหน้างาน:</td>
                                    <td class="text-dark">
                                        @if($employee->manager)
                                            <a href="/employees/{{ $employee->manager->id }}" class="text-decoration-none">
                                                {{ $employee->manager->first_name }} {{ $employee->manager->last_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">- ไม่มี (ระดับสูงสุด) -</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">วันที่เริ่มงาน:</td>
                                    <td class="text-dark">{{ \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">สถานะ:</td>
                                    <td>
                                        @if($employee->status == 'Active')
                                            <span class="badge bg-success">ทำงานอยู่</span>
                                        @else
                                            <span class="badge bg-danger">ลาออก</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection