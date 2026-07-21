@extends('layouts.app')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-diagram-2-fill text-primary me-2"></i>จัดการข้อมูลแผนก</h1>
        <div class="page-subtitle">โครงสร้างแผนกทั้งหมดในองค์กร</div>
    </div>
    <a href="{{ route('departments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> เพิ่มแผนก
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">บริษัท</th>
                        <th>ชื่อแผนก</th>
                        <th>ภายใต้แผนก (หลัก)</th>
                        <th class="text-end pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $dept)
                    <tr>
                        <td class="ps-4"><span class="badge-soft badge-soft-muted">{{ $dept->company->name ?? '-' }}</span></td>
                        <td class="fw-semibold">{{ $dept->name }}</td>
                        <td class="text-muted">{{ $dept->parent->name ?? '—' }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('departments.edit', $dept->id) }}" class="btn btn-sm btn-soft" title="แก้ไข">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('departments.destroy', $dept->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('ยืนยันการลบแผนกนี้?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="ลบ"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>ยังไม่มีข้อมูลแผนก
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($departments->hasPages())
    <div class="card-footer bg-transparent border-0 px-4 py-3">{{ $departments->links() }}</div>
    @endif
</div>
@endsection
