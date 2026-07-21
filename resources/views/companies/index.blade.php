@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-buildings-fill text-primary me-2"></i>จัดการข้อมูลบริษัท</h1>
        <div class="page-subtitle">บริษัทลูกค้าทั้งหมดในระบบ (Companies)</div>
    </div>
    <a href="{{ route('companies.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> เพิ่มบริษัทใหม่
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">โลโก้</th>
                        <th>รหัสบริษัท</th>
                        <th>ชื่อบริษัท</th>
                        <th>เลขผู้เสียภาษี</th>
                        <th>สถานะ</th>
                        <th class="text-end pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($companies as $comp)
                        <tr>
                            <td class="ps-4">
                                @if($comp->logo_path)
                                    <img src="{{ asset('storage/' . $comp->logo_path) }}" alt="Logo" class="rounded" style="width: 44px; height: 44px; object-fit: cover;">
                                @else
                                    <div class="rounded d-flex align-items-center justify-content-center"
                                         style="width: 44px; height: 44px; background: var(--rs-primary-soft); color: var(--rs-primary);">
                                        <i class="bi bi-building"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $comp->comp_code }}</td>
                            <td>{{ $comp->name }}</td>
                            <td class="text-muted">{{ $comp->tax_id ?? '—' }}</td>
                            <td>
                                <span class="badge-soft badge-soft-{{ $comp->status == 'Active' ? 'success' : 'danger' }}">
                                    {{ $comp->status }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('companies.edit', $comp->id) }}" class="btn btn-sm btn-soft" title="แก้ไข">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('companies.destroy', $comp->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('ยืนยันการลบบริษัทนี้? ข้อมูลแผนกและพนักงานที่ผูกไว้จะได้รับผลกระทบด้วย');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="ลบ"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>ยังไม่มีข้อมูลบริษัทในระบบ
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($companies->hasPages())
    <div class="card-footer bg-transparent border-0 px-4 py-3">{{ $companies->links() }}</div>
    @endif
</div>
@endsection
