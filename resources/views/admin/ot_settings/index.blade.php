@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-plus-circle"></i> ตั้งค่านโยบาย OT ใหม่
                </div>
                <div class="card-body">
                    <form action="{{ route('ot-settings.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">วันที่มีผลบังคับใช้</label>
                            <input type="date" name="effective_date" class="form-control" required>
                            <small class="text-muted text-xs">ระบบจะเลือกใช้ค่าล่าสุดที่น้อยกว่าหรือเท่ากับวันที่คำนวณ</small>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">เรทวันปกติ (เท่า)</label>
                                <input type="number" step="0.1" name="workday_rate" class="form-control" value="1.5" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">เรทวันหยุด (เท่า)</label>
                                <input type="number" step="0.1" name="holiday_rate" class="form-control" value="3.0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ต้องทำอย่างน้อย (นาที)</label>
                            <input type="number" name="min_ot_mins" class="form-control" value="30" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">บังคับพักเบรค (นาที)</label>
                            <input type="number" name="break_mins" class="form-control" value="30" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">บันทึกนโยบาย</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-clock-history"></i> ประวัตินโยบายการจ่าย OT
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th>วันที่มีผล</th>
                                <th>เรทปกติ</th>
                                <th>เรทวันหยุด</th>
                                <th>ขั้นต่ำ (นาที)</th>
                                <th>พักเบรค (นาที)</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach($settings as $setting)
                            <tr>
                                <td class="fw-bold">{{ $setting->effective_date }}</td>
                                <td>{{ number_format($setting->workday_rate, 1) }}x</td>
                                <td>{{ number_format($setting->holiday_rate, 1) }}x</td>
                                <td>{{ $setting->min_ot_mins }}</td>
                                <td>{{ $setting->break_mins }}</td>
                                <td>
                                    @if($setting->effective_date <= date('Y-m-d'))
                                        <span class="badge bg-success text-xs">ใช้งานอยู่</span>
                                    @else
                                        <span class="badge bg-info text-xs">รอดำเนินการ</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection