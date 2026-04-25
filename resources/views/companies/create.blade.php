@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white pt-3 pb-2">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-plus-circle"></i> เพิ่มข้อมูลบริษัทใหม่</h5>
                </div>
                
                <div class="card-body p-4">
                    {{-- แจ้งเตือน Error --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">รหัสบริษัท <span class="text-danger">*</span></label>
                                <input type="text" name="comp_code" class="form-control" value="{{ old('comp_code') }}" required placeholder="เช่น Roti01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">สถานะ <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Active">เปิดใช้งาน (Active)</option>
                                    <option value="Inactive">ปิดใช้งาน (Inactive)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อบริษัท / องค์กร <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">เลขผู้เสียภาษี 13 หลัก</label>
                                <input type="text" name="tax_id" class="form-control" value="{{ old('tax_id') }}" maxlength="13">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">เบอร์โทรศัพท์</label>
                                <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ที่อยู่บริษัท</label>
                            <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">โลโก้บริษัท</label>
                            <input type="file" name="logo" class="form-control" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">รองรับไฟล์ JPG, PNG ขนาดไม่เกิน 2MB</small>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-3">
                            <a href="{{ route('companies.index') }}" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">💾 บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection