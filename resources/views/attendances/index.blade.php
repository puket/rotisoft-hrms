@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h4 class="fw-bold text-primary mb-4">⏱️ บันทึกเวลาเข้า-ออกงาน (Time & Attendance)</h4>

            @if(session('success'))
                <div class="alert alert-success fw-bold">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger fw-bold">{{ session('error') }}</div>
            @endif

            <div class="card shadow-sm border-0 rounded-4 mb-4 bg-light">
                <div class="card-body text-center p-5">
                    <h5 class="text-muted mb-2">เวลาปัจจุบัน</h5>
                    <h1 class="display-3 fw-bold text-dark mb-4" id="realtime-clock">00:00:00</h1>
                    <p class="mb-4">{{ \Carbon\Carbon::now()->translatedFormat('l d F Y') }}</p>

                    @if(!$todayAttendance)
                        <form action="/attendance/clock-in" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-lg btn-success fw-bold px-5 rounded-pill shadow">☀️ บันทึกเวลาเข้างาน (Clock In)</button>
                        </form>
                    @elseif($todayAttendance && is_null($todayAttendance->clock_out))
                        <div class="mb-3">
                            <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">เวลาเข้างาน: {{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('H:i') }} น.</span>
                        </div>
                        <form action="/attendance/clock-out" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-lg btn-danger fw-bold px-5 rounded-pill shadow">🌙 บันทึกเวลาออกงาน (Clock Out)</button>
                        </form>
                    @else
                        <div class="alert alert-info d-inline-block px-5 rounded-pill border-0 shadow-sm">
                            <h5 class="mb-0 fw-bold">🎉 วันนี้คุณลงเวลาครบแล้ว</h5>
                            <hr class="my-2">
                            <span class="me-3">☀️ เข้า: {{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('H:i') }} น.</span>
                            <span>🌙 ออก: {{ \Carbon\Carbon::parse($todayAttendance->clock_out)->format('H:i') }} น.</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white pt-3 pb-2 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">📅 ประวัติการลงเวลาของคุณ</h6>
                    <a href="/attendance/request" class="btn btn-outline-warning btn-sm fw-bold">📝 ขอแก้ไขเวลา / ลืมลงเวลา</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle text-center">
                            <thead class="table-light">
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
                                    <td class="text-success fw-bold">{{ $record->clock_in ? \Carbon\Carbon::parse($record->clock_in)->format('H:i') : '-' }}</td>
                                    <td class="text-danger fw-bold">{{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('H:i') : '-' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $record->status }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-muted py-4">ยังไม่มีประวัติการลงเวลา</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                {{ $history->links('pagination::bootstrap-5') }}
            </div>
            
            <div class="card shadow-sm border-0 rounded-4 mt-4">
                <div class="card-header bg-white pt-3 pb-2">
                    <h6 class="fw-bold mb-0 text-secondary">📨 สถานะคำร้องขอแก้ไขเวลาของคุณ</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle text-center">
                            <thead class="table-light">
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
                                    <td colspan="5" class="text-muted py-4">ไม่มีประวัติการส่งคำร้อง</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                {{ $requests->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
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