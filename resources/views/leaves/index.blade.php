@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-calendar2-x-fill text-primary me-2"></i>ประวัติการลาของฉัน</h1>
        <div class="page-subtitle">My Leaves</div>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#leaveModal">
        <i class="bi bi-pencil-square me-1"></i> เขียนใบลา
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ประเภท</th>
                        <th>เริ่มวันที่</th>
                        <th>ถึงวันที่</th>
                        <th>เหตุผล</th>
                        <th>สถานะ</th>
                        <th class="pe-4">วันที่ยื่น</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                    <tr>
                        <td class="ps-4 fw-semibold">{{ $leave->leaveType->name ?? '-' }}</td>
                        <td>{{ $leave->start_date }}</td>
                        <td>{{ $leave->end_date }}</td>
                        <td class="text-muted small">{{ Str::limit($leave->reason, 30) }}</td>
                        <td>
                            @if($leave->status == 'Pending')
                                <span class="badge-soft badge-soft-warning">รออนุมัติ</span>
                            @elseif($leave->status == 'Approved')
                                <span class="badge-soft badge-soft-success">อนุมัติแล้ว</span>
                            @else
                                <span class="badge-soft badge-soft-danger">ปฏิเสธ</span>
                            @endif
                        </td>
                        <td class="pe-4 text-muted">{{ $leave->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>ยังไม่มีประวัติการลา
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($leaves->hasPages())
    <div class="card-footer bg-transparent border-0 px-4 py-3">{{ $leaves->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

<div class="modal fade" id="leaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-calendar2-x-fill text-primary me-2"></i>เขียนใบลาใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/leaves" method="POST">
                @csrf
                <div class="modal-body p-4 pt-0">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>เกิดข้อผิดพลาด:</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">ประเภทการลา <span class="text-danger">*</span></label>
                        <select name="leave_type_id" class="form-select" required>
                            <option value="">-- เลือกประเภทการลา --</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">จากวันที่ <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">ถึงวันที่ <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">เหตุผลการลา</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="ระบุเหตุผล (ถ้ามี)"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-soft" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-send me-1"></i>ส่งใบลา</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
