@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center pt-3 pb-2">
            <h5 class="mb-0 fw-bold"><i class="fas fa-building"></i> จัดการข้อมูลบริษัท (Companies)</h5>
            <a href="{{ route('companies.create') }}" class="btn btn-light btn-sm fw-bold">
                <i class="fas fa-plus"></i> เพิ่มบริษัทใหม่
            </a>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>สำเร็จ!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>โลโก้</th>
                            <th>รหัสบริษัท</th>
                            <th>ชื่อบริษัท</th>
                            <th>เลขผู้เสียภาษี</th>
                            <th>สถานะ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($companies as $comp)
                            <tr>
                                <td>
                                    @if($comp->logo_path)
                                        <img src="{{ asset('storage/' . $comp->logo_path) }}" alt="Logo" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $comp->comp_code }}</td>
                                <td>{{ $comp->name }}</td>
                                <td>{{ $comp->tax_id ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $comp->status == 'Active' ? 'success' : 'danger' }}">
                                        {{ $comp->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('companies.edit', $comp->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('companies.destroy', $comp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบบริษัทนี้? ข้อมูลแผนกและพนักงานที่ผูกไว้จะได้รับผลกระทบด้วย');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">ยังไม่มีข้อมูลบริษัทในระบบ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                {{ $companies->links() }}
            </div>
        </div>
    </div>
</div>
@endsection