@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0 col-md-8 mx-auto">
        <div class="card-header bg-warning text-dark"><h5 class="mb-0 fw-bold">แก้ไขแผนก: {{ $department->name }}</h5></div>
        <div class="card-body p-4">
            <form action="{{ route('departments.update', $department->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold">สังกัดบริษัท <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-select" required>
                        @foreach($companies as $comp)
                            <option value="{{ $comp->id }}" {{ $department->company_id == $comp->id ? 'selected' : '' }}>
                                {{ $comp->comp_code }} : {{ $comp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">ชื่อแผนก <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ $department->name }}" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">ภายใต้แผนกหลัก</label>
                    <select name="parent_id" class="form-select">
                        <option value="">-- เป็นแผนกหลัก --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $department->parent_id == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-warning w-100 fw-bold text-dark">อัปเดตข้อมูล</button>
            </form>
        </div>
    </div>
</div>
@endsection