@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0 col-md-8 mx-auto">
        <div class="card-header bg-warning text-dark pt-3 pb-2"><h5 class="mb-0 fw-bold">แก้ไขตำแหน่ง: {{ $position->title }}</h5></div>
        <div class="card-body p-4">
            <form action="{{ route('positions.update', $position->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">สังกัดบริษัท <span class="text-danger">*</span></label>
                        <select name="company_id" class="form-select" required>
                            @foreach($companies as $comp)
                                <option value="{{ $comp->id }}" {{ $position->company_id == $comp->id ? 'selected' : '' }}>
                                    {{ $comp->comp_code }} : {{ $comp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">สังกัดแผนก <span class="text-danger">*</span></label>
                        <select name="department_id" class="form-select" required>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $position->department_id == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->company->comp_code ?? 'ไม่มีบริษัท' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">ชื่อตำแหน่ง <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ $position->title }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">ระดับตำแหน่ง <span class="text-danger">*</span></label>
                        <select name="job_level" class="form-select" required>
                            <option value="Staff" {{ $position->job_level == 'Staff' ? 'selected' : '' }}>พนักงานทั่วไป (Staff)</option>
                            <option value="Manager" {{ $position->job_level == 'Manager' ? 'selected' : '' }}>ผู้จัดการ (Manager)</option>
                            <option value="MD" {{ $position->job_level == 'MD' ? 'selected' : '' }}>ผู้บริหาร (MD / Director)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">รายละเอียดงาน (Job Description)</label>
                    <textarea name="job_description" class="form-control" rows="3">{{ $position->job_description }}</textarea>
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-bold text-dark">💾 อัปเดตข้อมูล</button>
            </form>
        </div>
    </div>
</div>
@endsection