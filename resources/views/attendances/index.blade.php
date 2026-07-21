@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="page-title"><i class="bi bi-clock-fill text-primary me-2"></i>บันทึกเวลาเข้า-ออกงาน</h1>
    <div class="page-subtitle">Time &amp; Attendance</div>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}</div>
@endif

<div class="card mb-4">
    <div class="card-body text-center py-5">
        <div class="text-muted small text-uppercase fw-semibold mb-2">เวลาปัจจุบัน</div>
        <h1 class="display-3 fw-bold mb-3" id="realtime-clock" style="letter-spacing: -0.02em;">00:00:00</h1>
        <p class="text-muted mb-4">{{ \Carbon\Carbon::now()->translatedFormat('l d F Y') }}</p>

        @if(!$todayAttendance)
            <form action="/attendance/clock-in" method="POST">
                @csrf
                <button type="submit" class="btn btn-success btn-lg rounded-pill px-5">
                    <i class="bi bi-sun-fill me-1"></i> บันทึกเวลาเข้างาน (Clock In)
                </button>
            </form>
        @elseif($todayAttendance && is_null($todayAttendance->clock_out))
            <div class="mb-3">
                <span class="badge-soft badge-soft-success" style="font-size: .85rem; padding: .5rem 1rem;">
                    เวลาเข้างาน: {{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('H:i') }} น.
                </span>
            </div>
            <form action="/attendance/clock-out" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger btn-lg rounded-pill px-5">
                    <i class="bi bi-moon-stars-fill me-1"></i> บันทึกเวลาออกงาน (Clock Out)
                </button>
            </form>
        @else
            <div class="alert alert-info d-inline-block px-4 py-3 mb-0">
                <div class="fw-bold mb-2"><i class="bi bi-check-circle-fill me-1"></i>วันนี้คุณลงเวลาครบแล้ว</div>
                <span class="me-3"><i class="bi bi-sun-fill me-1"></i>เข้า: {{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('H:i') }} น.</span>
                <span><i class="bi bi-moon-stars-fill me-1"></i>ออก: {{ \Carbon\Carbon::parse($todayAttendance->clock_out)->format('H:i') }} น.</span>
            </div>
        @endif
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><i class="bi bi-calendar3 text-primary me-2"></i>ประวัติการลงเวลาของคุณ</h5>
        <a href="/attendance/request" class="btn btn-sm btn-soft">
            <i class="bi bi-pencil-square me-1"></i>ขอแก้ไขเวลา / ลืมลงเวลา
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead>
                    <tr>
                        <th>วันที่</th>
                        <th>เวลาเข้างาน</th>
                        <th>เวลาออกงาน</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $record)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($record->work_date)->format('d/m/Y') }}</td>
                        <td class="text-success fw-semibold">{{ $record->clock_in ? \Carbon\Carbon::parse($record->clock_in)->format('H:i') : '-' }}</td>
                        <td class="text-danger fw-semibold">{{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('H:i') : '-' }}</td>
                        <td><span class="badge-soft badge-soft-muted">{{ $record->status }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>ยังไม่มีประวัติการลงเวลา
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($history->hasPages())
    <div class="card-footer bg-transparent border-0 px-4 py-3">{{ $history->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

<div class="card">
    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
        <h5 class="fw-bold mb-0"><i class="bi bi-envelope-paper text-secondary me-2"></i>สถานะคำร้องขอแก้ไขเวลาของคุณ</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead>
                    <tr>
                        <th>วันที่ขอแก้</th>
                        <th>เวลาที่ขอเข้า</th>
                        <th>เวลาที่ขอออก</th>
                        <th>เหตุผล</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($req->work_date)->format('d/m/Y') }}</td>
                        <td class="text-success">{{ $req->requested_clock_in ? \Carbon\Carbon::parse($req->requested_clock_in)->format('H:i') : '-' }}</td>
                        <td class="text-danger">{{ $req->requested_clock_out ? \Carbon\Carbon::parse($req->requested_clock_out)->format('H:i') : '-' }}</td>
                        <td><small class="text-muted text-start d-block" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $req->reason }}">{{ $req->reason }}</small></td>
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
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>ไม่มีประวัติการส่งคำร้อง
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($requests->hasPages())
    <div class="card-footer bg-transparent border-0 px-4 py-3">{{ $requests->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

<script>
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('th-TH', { hour12: false });
        document.getElementById('realtime-clock').textContent = timeString;
    }
    setInterval(updateClock, 1000);
    updateClock(); // เรียกครั้งแรกทันที
</script>
@endsection
