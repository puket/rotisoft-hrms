@extends('layouts.app')

@section('content')
<div class="container py-4">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-plus-circle"></i> เพิ่มกะการทำงานใหม่
                </div>
                <div class="card-body">
                    <form action="{{ route('shifts.store') }}" method="POST">
                        @csrf
                        
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">1. ข้อมูลพื้นฐาน</h6>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label text-sm">รหัสกะ</label>
                                <input type="text" name="shift_code" class="form-control form-control-sm" placeholder="Ex. S1" value="{{ old('shift_code') }}">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label text-sm">ชื่อกะการทำงาน <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="Ex. กะเช้า 08:30-17:30" value="{{ old('name') }}" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label text-sm">เวลาเข้า <span class="text-danger">*</span></label>
                                <input type="time" name="start_time" class="form-control form-control-sm" value="{{ old('start_time') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-sm">เวลาออก <span class="text-danger">*</span></label>
                                <input type="time" name="end_time" class="form-control form-control-sm" value="{{ old('end_time') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-sm">ชม.ทำงาน</label>
                                <input type="number" step="0.5" name="normal_work_hours" class="form-control form-control-sm" value="{{ old('normal_work_hours', '8.0') }}" required>
                            </div>
                        </div>

                        <h6 class="fw-bold text-warning border-bottom pb-2 mb-3 mt-4">2. ช่วงเวลาพัก (Break)</h6>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label text-sm">เริ่มพัก</label>
                                <input type="time" name="break_start_time" class="form-control form-control-sm" value="{{ old('break_start_time') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-sm">สิ้นสุดพัก</label>
                                <input type="time" name="break_end_time" class="form-control form-control-sm" value="{{ old('break_end_time') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-sm">ชม.พัก</label>
                                <input type="number" step="0.5" name="break_hours" class="form-control form-control-sm" value="{{ old('break_hours', '1.0') }}">
                            </div>
                        </div>

                        <h6 class="fw-bold text-success border-bottom pb-2 mb-3 mt-4">3. อนุญาตให้ทำ OT (ตั้งแต่-ถึง)</h6>
                        
                        <label class="form-label text-sm text-muted mb-1">OT ก่อนเข้างาน (Pre-Shift)</label>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <input type="time" name="ot_before_start_time" class="form-control form-control-sm" value="{{ old('ot_before_start_time') }}" title="เริ่ม OT ก่อนเข้างาน">
                            </div>
                            <div class="col-6">
                                <input type="time" name="ot_before_end_time" class="form-control form-control-sm" value="{{ old('ot_before_end_time') }}" title="สิ้นสุด OT ก่อนเข้างาน">
                            </div>
                        </div>

                        <label class="form-label text-sm text-muted mb-1">OT หลังออกงาน (Post-Shift)</label>
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <input type="time" name="ot_after_start_time" class="form-control form-control-sm" value="{{ old('ot_after_start_time') }}" title="เริ่ม OT หลังออกงาน">
                            </div>
                            <div class="col-6">
                                <input type="time" name="ot_after_end_time" class="form-control form-control-sm" value="{{ old('ot_after_end_time') }}" title="สิ้นสุด OT หลังออกงาน">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-save"></i> บันทึกกะการทำงาน</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-ul"></i> รายการกะการทำงานทั้งหมด</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัส</th>
                                    <th class="text-start">ชื่อกะ</th>
                                    <th>เวลาทำงาน</th>
                                    <th>ชม.ปกติ</th>
                                    <th>เวลาพัก</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shifts as $shift)
                                    @php
                                        // ปรับฟอร์แมตเวลาให้ดูง่าย (ตัดวินาทีออก)
                                        $sTime = substr($shift->start_time, 0, 5);
                                        $eTime = substr($shift->end_time, 0, 5);
                                        $bStart = $shift->break_start_time ? substr($shift->break_start_time, 0, 5) : '-';
                                        $bEnd = $shift->break_end_time ? substr($shift->break_end_time, 0, 5) : '-';
                                    @endphp
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $shift->shift_code ?? '-' }}</span></td>
                                    <td class="text-start fw-bold text-primary">{{ $shift->name }}</td>
                                    <td>{{ $sTime }} - {{ $eTime }}</td>
                                    <td>{{ $shift->normal_work_hours * 1 }} ชม.</td>
                                    <td>{{ $bStart }} - {{ $bEnd }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editShiftModal{{ $shift->id }}">
                                            <i class="bi bi-pencil-square"></i> ดู/แก้ไข
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-muted py-4">ยังไม่มีข้อมูลกะการทำงานในระบบ</td>
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

@foreach($shifts as $shift)
<div class="modal fade" id="editShiftModal{{ $shift->id }}" tabindex="-1" aria-labelledby="editShiftModalLabel{{ $shift->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold" id="editShiftModalLabel{{ $shift->id }}">
                    <i class="bi bi-pencil-square"></i> แก้ไขข้อมูลกะการทำงาน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('shifts.update', $shift->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="fw-bold text-primary border-bottom pb-2">1. ข้อมูลพื้นฐาน</h6>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-sm">รหัสกะ</label>
                            <input type="text" name="shift_code" class="form-control" value="{{ $shift->shift_code }}">
                        </div>
                        <div class="col-md-9 mb-3">
                            <label class="form-label text-sm">ชื่อกะการทำงาน <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $shift->name }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-sm">เวลาเข้า <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control" value="{{ substr($shift->start_time, 0, 5) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-sm">เวลาออก <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control" value="{{ substr($shift->end_time, 0, 5) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-sm">ชม.ทำงาน</label>
                            <input type="number" step="0.5" name="normal_work_hours" class="form-control" value="{{ $shift->normal_work_hours }}" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="fw-bold text-warning border-bottom pb-2">2. ช่วงเวลาพัก (Break)</h6>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-sm">เริ่มพัก</label>
                            <input type="time" name="break_start_time" class="form-control" value="{{ $shift->break_start_time ? substr($shift->break_start_time, 0, 5) : '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-sm">สิ้นสุดพัก</label>
                            <input type="time" name="break_end_time" class="form-control" value="{{ $shift->break_end_time ? substr($shift->break_end_time, 0, 5) : '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-sm">ชม.พัก</label>
                            <input type="number" step="0.5" name="break_hours" class="form-control" value="{{ $shift->break_hours }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="fw-bold text-success border-bottom pb-2">3. อนุญาตให้ทำ OT (ตั้งแต่-ถึง)</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-sm text-muted">OT ก่อนเข้างาน (Pre-Shift)</label>
                            <div class="input-group">
                                <input type="time" name="ot_before_start_time" class="form-control" value="{{ $shift->ot_before_start_time ? substr($shift->ot_before_start_time, 0, 5) : '' }}">
                                <span class="input-group-text">ถึง</span>
                                <input type="time" name="ot_before_end_time" class="form-control" value="{{ $shift->ot_before_end_time ? substr($shift->ot_before_end_time, 0, 5) : '' }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-sm text-muted">OT หลังออกงาน (Post-Shift)</label>
                            <div class="input-group">
                                <input type="time" name="ot_after_start_time" class="form-control" value="{{ $shift->ot_after_start_time ? substr($shift->ot_after_start_time, 0, 5) : '' }}">
                                <span class="input-group-text">ถึง</span>
                                <input type="time" name="ot_after_end_time" class="form-control" value="{{ $shift->ot_after_end_time ? substr($shift->ot_after_end_time, 0, 5) : '' }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success fw-bold"><i class="bi bi-check-circle"></i> บันทึกการเปลี่ยนแปลง</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection