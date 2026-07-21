@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="page-title"><i class="bi bi-hourglass-split text-primary me-2"></i>ระบบขอทำล่วงเวลา</h1>
    <div class="page-subtitle">ส่งคำร้องขอทำ OT ล่วงหน้าเพื่อให้หัวหน้างานพิจารณาอนุมัติ</div>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square text-primary me-2"></i>ฟอร์มขอทำ OT</h6>
                <form action="/ot-requests" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">วันที่ต้องการทำ OT <span class="text-danger">*</span></label>
                        <input type="date" name="work_date" class="form-control" min="{{ \Carbon\Carbon::today()->toDateString() }}" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">เวลาเริ่ม <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">เวลาสิ้นสุด <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">เหตุผล/งานที่ต้องทำ <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="ระบุรายละเอียดงานที่ต้องทำให้ชัดเจน" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send me-1"></i>ส่งคำร้องขอ OT</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-list-ul text-secondary me-2"></i>ประวัติการขอ OT ของฉัน</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0">
                        <thead>
                            <tr>
                                <th>วันที่ทำ OT</th>
                                <th>เวลา</th>
                                <th>เหตุผล</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($otRequests as $req)
                            <tr>
                                <td class="fw-semibold">{{ \Carbon\Carbon::parse($req->work_date)->format('d/m/Y') }}</td>
                                <td class="text-primary">{{ \Carbon\Carbon::parse($req->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($req->end_time)->format('H:i') }} น.</td>
                                <td><small class="text-muted d-block text-start text-truncate" style="max-width: 200px;" title="{{ $req->reason }}">{{ $req->reason }}</small></td>
                                <td>
                                    @if($req->status == 'Pending')
                                        <span class="badge-soft badge-soft-warning">รออนุมัติ</span>
                                    @elseif($req->status == 'Approved')
                                        <span class="badge-soft badge-soft-success">อนุมัติ</span>
                                    @else
                                        <span class="badge-soft badge-soft-danger">ไม่อนุมัติ</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>ยังไม่มีประวัติการขอ OT
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($otRequests->hasPages())
            <div class="card-footer bg-transparent border-0 px-4 py-3">{{ $otRequests->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
