@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-people-fill text-primary me-2"></i>รายชื่อพนักงาน</h1>
        <div class="page-subtitle">ทำเนียบพนักงานทั้งหมด (Employee Directory)</div>
    </div>
    @can('edit-employees')
        <a href="/employees/create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> เพิ่มพนักงานใหม่
        </a>
    @endcan
</div>

@if (session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body pb-0">
        <form action="/employees" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="ค้นหาด้วยรหัส, ชื่อ หรือ นามสกุล..." value="{{ $search }}">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i>ค้นหา</button>
                        @if($search)
                            <a href="/employees" class="btn btn-outline-secondary">ล้างค่า</a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">รหัสพนักงาน</th>
                        <th>ข้อมูลพนักงาน</th>
                        <th>แผนก / ตำแหน่ง</th>
                        <th>หัวหน้างาน (Report To)</th>
                        <th>สถานะ</th>
                        <th class="text-end pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @if($employees->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>ไม่พบข้อมูลพนักงานที่ค้นหา
                            </td>
                        </tr>
                    @endif

                    @foreach($employees as $emp)
                    <tr>
                        <td class="ps-4 text-muted fw-semibold">{{ $emp->employee_code }}</td>
                        <td>
                            <div class="fw-semibold">{{ $emp->first_name }} {{ $emp->last_name }}</div>
                            <div class="text-muted small"><i class="bi bi-envelope me-1"></i>{{ $emp->email ?? '-' }}</div>
                            <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $emp->phone_number ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge-soft badge-soft-primary d-inline-block mb-1">
                                {{ $emp->department ? $emp->department->name : 'ไม่มีแผนก' }}
                            </span>
                            <div class="small text-muted">{{ $emp->position ? $emp->position->title : '-' }}</div>
                        </td>
                        <td>
                            @if($emp->manager)
                                <div class="fw-semibold text-primary">{{ $emp->manager->first_name }} {{ $emp->manager->last_name }}</div>
                                <div class="text-muted small">{{ $emp->manager->position ? $emp->manager->position->title : 'Manager' }}</div>
                            @else
                                <span class="text-muted">— ระดับสูงสุด —</span>
                            @endif
                        </td>
                        <td>
                            @if($emp->status == 'Active')
                                <span class="badge-soft badge-soft-success">ทำงานอยู่</span>
                            @elseif($emp->status == 'Resigned')
                                <span class="badge-soft badge-soft-danger">ลาออก</span>
                            @else
                                <span class="badge-soft badge-soft-warning">{{ $emp->status }}</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="/employees/{{ $emp->id }}" class="btn btn-sm btn-outline-secondary">ดูข้อมูล</a>
                            @can('edit-employees')
                                <a href="/employees/{{ $emp->id }}/edit" class="btn btn-sm btn-soft">แก้ไข</a>
                            @endcan
                            @can('is-hr')
                                <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('ยืนยันการลบพนักงานคนนี้?\n\nหากเคยมีประวัติลงเวลาทำงานแล้ว ระบบจะไม่ให้ลบ (ต้องเปลี่ยนสถานะเป็นลาออกแทน) หากยังไม่มีประวัติเลย จะถูกย้ายเข้าคลังเก็บถาวร');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="ลบ"><i class="bi bi-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer bg-transparent border-0 d-flex justify-content-end px-4 py-3">
        {{ $employees->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
