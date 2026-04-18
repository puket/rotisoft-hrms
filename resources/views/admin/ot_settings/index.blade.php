@extends('layouts.app')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0"><i class="bi bi-clock-history"></i> ตั้งค่าล่วงเวลา (OT Settings)</h4>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-plus-circle"></i> เพิ่มการตั้งค่าใหม่
                </div>
                <div class="card-body">
                    <form action="{{ route('ot-settings.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label text-sm fw-bold">ประเภทพนักงาน <span class="text-danger">*</span></label>
                            <select name="employee_type" class="form-select" required>
                                <option value="Monthly" {{ old('employee_type') == 'Monthly' ? 'selected' : '' }}>พนักงานรายเดือน (Monthly)</option>
                                <option value="Daily" {{ old('employee_type') == 'Daily' ? 'selected' : '' }}>พนักงานรายวัน (Daily)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm fw-bold">วันที่มีผลบังคับใช้ <span class="text-danger">*</span></label>
                            <input type="date" name="effective_date" class="form-control" value="{{ old('effective_date') ?? date('Y-m-d') }}" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label text-sm fw-bold">เรทวันปกติ (เท่า) <span class="text-danger">*</span></label>
                                <input type="number" step="0.5" name="workday_rate" class="form-control" value="{{ old('workday_rate', 1.5) }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-sm fw-bold">เรทวันหยุด (เท่า) <span class="text-danger">*</span></label>
                                <input type="number" step="0.5" name="holiday_rate" class="form-control" value="{{ old('holiday_rate', 3.0) }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label text-sm fw-bold">หักเวลาพัก (นาที) <span class="text-danger">*</span></label>
                                <input type="number" name="break_mins" class="form-control" value="{{ old('break_mins', 30) }}" title="จำนวนนาทีที่ต้องหักเป็นเวลาพักก่อนเริ่มคิด OT" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-sm fw-bold">ทำขั้นต่ำ (นาที) <span class="text-danger">*</span></label>
                                <input type="number" name="min_ot_mins" class="form-control" value="{{ old('min_ot_mins', 30) }}" title="จำนวนนาทีขั้นต่ำที่ทำ OT ถึงจะได้เงิน" required>
                            </div>
                        </div>

                        <div class="mb-4 form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                            <label class="form-check-label fw-bold" for="isActive">เปิดใช้งานสถานะ Active</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                            <i class="bi bi-save"></i> บันทึกการตั้งค่า
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold border-bottom">
                    <i class="bi bi-list-ul text-primary"></i> ประวัติการตั้งค่า OT 
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>สถานะ</th>
                                    <th>ประเภท</th>
                                    <th>วันที่มีผล</th>
                                    <th>เรท (ปกติ/หยุด)</th>
                                    <th>หักพัก/ขั้นต่ำ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $setting)
                                <tr>
                                    <td>
                                        @if($setting->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($setting->employee_type == 'Monthly')
                                            <span class="badge bg-primary">รายเดือน</span>
                                        @else
                                            <span class="badge bg-info text-dark">รายวัน</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">{{ \Carbon\Carbon::parse($setting->effective_date)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="text-primary fw-bold">{{ $setting->workday_rate }}x</span> / 
                                        <span class="text-danger fw-bold">{{ $setting->holiday_rate }}x</span>
                                    </td>
                                    <td class="text-muted text-sm">
                                        หักพัก {{ $setting->break_mins }} นาที<br>
                                        ขั้นต่ำ {{ $setting->min_ot_mins }} นาที
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editOtModal{{ $setting->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('ot-settings.destroy', $setting->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบการตั้งค่า OT นี้?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-muted py-4">ยังไม่มีประวัติการตั้งค่า OT</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($settings as $setting)
<div class="modal fade" id="editOtModal{{ $setting->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning border-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square"></i> แก้ไขการตั้งค่า OT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('ot-settings.update', $setting->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">ประเภทพนักงาน <span class="text-danger">*</span></label>
                        <select name="employee_type" class="form-select" required>
                            <option value="Monthly" {{ $setting->employee_type == 'Monthly' ? 'selected' : '' }}>พนักงานรายเดือน (Monthly)</option>
                            <option value="Daily" {{ $setting->employee_type == 'Daily' ? 'selected' : '' }}>พนักงานรายวัน (Daily)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">วันที่มีผลบังคับใช้ <span class="text-danger">*</span></label>
                        <input type="date" name="effective_date" class="form-control" value="{{ $setting->effective_date }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">เรทวันปกติ (เท่า) <span class="text-danger">*</span></label>
                            <input type="number" step="0.5" name="workday_rate" class="form-control" value="{{ $setting->workday_rate }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">เรทวันหยุด (เท่า) <span class="text-danger">*</span></label>
                            <input type="number" step="0.5" name="holiday_rate" class="form-control" value="{{ $setting->holiday_rate }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">หักเวลาพัก (นาที) <span class="text-danger">*</span></label>
                            <input type="number" name="break_mins" class="form-control" value="{{ $setting->break_mins }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">ทำขั้นต่ำ (นาที) <span class="text-danger">*</span></label>
                            <input type="number" name="min_ot_mins" class="form-control" value="{{ $setting->min_ot_mins }}" required>
                        </div>
                    </div>

                    <div class="mb-2 form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActiveEdit{{ $setting->id }}" {{ $setting->is_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="isActiveEdit{{ $setting->id }}">เปิดใช้งานสถานะ Active</label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark"><i class="bi bi-check-circle"></i> บันทึกการเปลี่ยนแปลง</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection