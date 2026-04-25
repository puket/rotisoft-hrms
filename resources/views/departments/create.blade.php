@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0 col-md-8 mx-auto">
        <div class="card-header bg-primary text-white"><h5 class="mb-0 fw-bold">เพิ่มแผนกใหม่</h5></div>
        <div class="card-body p-4">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">สังกัดบริษัท <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-select" required>
                        <option value="">-- เลือกบริษัท --</option>
                        @foreach($companies as $comp)
                            <option value="{{ $comp->id }}">{{ $comp->comp_code }} : {{ $comp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">ชื่อแผนก <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">ภายใต้แผนกหลัก (ถ้าเป็นแผนกย่อย)</label>
                    <select name="parent_id" class="form-select">
                        <option value="">-- เป็นแผนกหลัก --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->company->name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold">บันทึกข้อมูล</button>
            </form>
        </div>
    </div>
</div>
@endsection