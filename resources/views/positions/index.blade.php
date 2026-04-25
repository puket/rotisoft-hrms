@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center pt-3 pb-2">
            <h5 class="mb-0 fw-bold"><i class="fas fa-id-badge"></i> จัดการข้อมูลตำแหน่งงาน (Positions)</h5>
            <a href="{{ route('positions.create') }}" class="btn btn-light btn-sm fw-bold"><i class="fas fa-plus"></i> เพิ่มตำแหน่ง</a>
        </div>
        <div class="card-body">
            @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>บริษัท</th>
                            <th>แผนก</th>
                            <th>ชื่อตำแหน่ง</th>
                            <th>ระดับ (Level)</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($positions as $pos)
                        <tr>
                            <td><span class="badge bg-dark">{{ $pos->company->comp_code ?? '-' }}</span></td>
                            <td>{{ $pos->department->name ?? '-' }}</td>
                            <td class="fw-bold text-primary">{{ $pos->title }}</td>
                            <td>
                                <span class="badge bg-{{ $pos->job_level == 'MD' ? 'danger' : ($pos->job_level == 'Manager' ? 'warning text-dark' : 'info text-dark') }}">
                                    {{ $pos->job_level }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('positions.edit', $pos->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('positions.destroy', $pos->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบตำแหน่งงานนี้?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $positions->links() }}
        </div>
    </div>
</div>
@endsection