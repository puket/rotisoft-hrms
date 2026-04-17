@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-warning text-dark pt-3 pb-2 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">📝 แบบฟอร์มขอแก้ไขเวลา / ลืมลงเวลา</h5>
                </div>

                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger fw-bold">{{ session('error') }}</div>
                    @endif

                    <form action="/attendance/request" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">วันที่ต้องการแก้ไข <span class="text-danger">*</span></label>
                            <input type="date" name="work_date" class="form-control" max="{{ \Carbon\Carbon::today()->toDateString() }}" required>
                            <small class="text-muted">เลือกได้เฉพาะวันปัจจุบันหรือวันย้อนหลังเท่านั้น</small>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">เวลาเข้างานที่ถูกต้อง (Clock In)</label>
                                <input type="time" name="requested_clock_in" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">เวลาออกงานที่ถูกต้อง (Clock Out)</label>
                                <input type="time" name="requested_clock_out" class="form-control">
                            </div>
                        </div>
                        <small class="text-danger mb-3 d-block">* กรอกเฉพาะเวลาที่ต้องการแก้ไขหรือลืมลงเวลา (ปล่อยว่างได้ถ้าไม่ต้องการแก้)</small>

                        <div class="mb-4">
                            <label class="form-label fw-bold">เหตุผลการขอแก้ไข <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="เช่น ลืมสแกนนิ้ว, เครื่องสแกนขัดข้อง, ออกไปพบลูกค้า..." required></textarea>
                        </div>

                        <div class="d-flex justify-content-end border-top pt-3">
                            <a href="/attendance" class="btn btn-secondary me-2">ยกเลิก</a>
                            <button type="submit" class="btn btn-warning fw-bold text-dark">🚀 ส่งคำร้องให้หัวหน้างาน</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection