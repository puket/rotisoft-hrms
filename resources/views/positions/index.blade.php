@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-person-badge-fill text-primary me-2"></i>จัดการข้อมูลตำแหน่งงาน</h1>
        <div class="page-subtitle">ตำแหน่งและระดับงาน (Positions &amp; Job Levels)</div>
    </div>
    <a href="{{ route('positions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> เพิ่มตำแหน่ง
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
                        <th>แผนก</th>
                        <th>ชื่อตำแหน่ง</th>
                        <th>ระดับ (Level)</th>
                        <th class="text-end pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($positions as $pos)
                    <tr>
                        <td class="ps-4"><span class="badge-soft badge-soft-muted">{{ $pos->company->comp_code ?? '-' }}</span></td>
                        <td class="text-muted">{{ $pos->department->name ?? '—' }}</td>
                        <td class="fw-semibold">{{ $pos->title }}</td>
                        <td>
                            <span class="badge-soft badge-soft-{{ $pos->job_level == 'MD' ? 'danger' : ($pos->job_level == 'Manager' ? 'warning' : 'primary') }}">
                                {{ $pos->job_level }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('positions.edit', $pos->id) }}" class="btn btn-sm btn-soft" title="แก้ไข">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('positions.destroy', $pos->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('ยืนยันการลบตำแหน่งงานนี้?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="ลบ"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>ยังไม่มีข้อมูลตำแหน่งงาน
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($positions->hasPages())
    <div class="card-footer bg-transparent border-0 px-4 py-3">{{ $positions->links() }}</div>
    @endif
</div>
@endsection
