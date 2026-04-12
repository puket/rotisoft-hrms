@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold text-primary mb-0">📅 ประวัติการลาของฉัน</h4>
                <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#leaveModal">
                    ✍️ เขียนใบลา
                </button>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ประเภท</th>
                                    <th>เริ่มวันที่</th>
                                    <th>ถึงวันที่</th>
                                    <th>เหตุผล</th>
                                    <th>สถานะ</th>
                                    <th>วันที่ยื่น</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaves as $leave)
                                <tr>
                                    <td><strong>{{ $leave->leave_type }}</strong></td>
                                    <td>{{ $leave->start_date }}</td>
                                    <td>{{ $leave->end_date }}</td>
                                    <td><small class="text-muted">{{ Str::limit($leave->reason, 30) }}</small></td>
                                    <td>
                                        @if($leave->status == 'Pending')
                                            <span class="badge bg-warning text-dark">รออนุมัติ</span>
                                        @elseif($leave->status == 'Approved')
                                            <span class="badge bg-success">อนุมัติแล้ว</span>
                                        @else
                                            <span class="badge bg-danger">ปฏิเสธ</span>
                                        @endif
                                    </td>
                                    <td>{{ $leave->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">ยังไม่มีประวัติการลา</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                {{ $leaves->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="leaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">เขียนใบลาใหม่</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/leaves" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">ประเภทการลา <span class="text-danger">*</span></label>
                        <select name="leave_type" class="form-select" required>
                            <option value="ลาป่วย">ลาป่วย (Sick Leave)</option>
                            <option value="ลากิจ">ลากิจ (Casual Leave)</option>
                            <option value="ลาพักร้อน">ลาพักร้อน (Vacation Leave)</option>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">จากวันที่ <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">ถึงวันที่ <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">เหตุผลการลา</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="ระบุเหตุผล (ถ้ามี)"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">ส่งใบลา 🚀</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection