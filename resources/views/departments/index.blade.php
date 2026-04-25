@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-sitemap"></i> จัดการข้อมูลแผนก</h5>
            <a href="{{ route('departments.create') }}" class="btn btn-light btn-sm fw-bold"><i class="fas fa-plus"></i> เพิ่มแผนก</a>
        </div>
        <div class="card-body">
            @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>บริษัท</th>
                        <th>ชื่อแผนก</th>
                        <th>ภายใต้แผนก (หลัก)</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $dept)
                    <tr>
                        <td><span class="badge bg-dark">{{ $dept->company->name ?? '-' }}</span></td>
                        <td class="fw-bold text-primary">{{ $dept->name }}</td>
                        <td>{{ $dept->parent->name ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('departments.edit', $dept->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('departments.destroy', $dept->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบแผนกนี้?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $departments->links() }}
        </div>
    </div>
</div>
@endsection