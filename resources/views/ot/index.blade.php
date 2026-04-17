@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <h4 class="fw-bold text-primary">🕒 ระบบขอทำล่วงเวลา (OT Plan)</h4>
            <p class="text-muted">ส่งคำร้องขอทำ OT ล่วงหน้าเพื่อให้หัวหน้างานพิจารณาอนุมัติ</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success fw-bold">{{ session('success') }}</div>
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

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4 bg-light">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">📝 ฟอร์มขอทำ OT</h6>
                    <form action="/ot-requests" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">วันที่ต้องการทำ OT <span class="text-danger">*</span></label>
                            <input type="date" name="work_date" class="form-control" min="{{ \Carbon\Carbon::today()->toDateString() }}" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">เวลาเริ่ม <span class="text-danger">*</span></label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">เวลาสิ้นสุด <span class="text-danger">*</span></label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small">เหตุผล/งานที่ต้องทำ <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="ระบุรายละเอียดงานที่ต้องทำให้ชัดเจน" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">🚀 ส่งคำร้องขอ OT</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white pt-3 pb-2">
                    <h6 class="fw-bold mb-0 text-secondary">📋 ประวัติการขอ OT ของฉัน</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle text-center">
                            <thead class="table-light">
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
                                    <td class="fw-bold">{{ \Carbon\Carbon::parse($req->work_date)->format('d/m/Y') }}</td>
                                    <td class="text-primary">{{ \Carbon\Carbon::parse($req->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($req->end_time)->format('H:i') }} น.</td>
                                    <td><small class="text-muted d-block text-start text-truncate" style="max-width: 200px;" title="{{ $req->reason }}">{{ $req->reason }}</small></td>
                                    <td>
                                        @if($req->status == 'Pending')
                                            <span class="badge bg-warning text-dark">⏳ รออนุมัติ</span>
                                        @elseif($req->status == 'Approved')
                                            <span class="badge bg-success">✅ อนุมัติ</span>
                                        @else
                                            <span class="badge bg-danger">❌ ไม่อนุมัติ</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-muted py-4">ยังไม่มีประวัติการขอ OT</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                {{ $otRequests->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection