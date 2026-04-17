@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h4 class="fw-bold text-primary mb-4">📊 รายงานสรุปการลงเวลา (Attendance Report)</h4>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 bg-primary text-white rounded-4 h-100">
                        <div class="card-body text-center">
                            <h6 class="mb-2">พนักงานทั้งหมด (Active)</h6>
                            <h2 class="fw-bold mb-0">{{ $totalEmployees }} <small class="fs-6">คน</small></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 bg-success text-white rounded-4 h-100">
                        <div class="card-body text-center">
                            <h6 class="mb-2">มาทำงาน (ลงเวลาแล้ว)</h6>
                            <h2 class="fw-bold mb-0">{{ $presentCount }} <small class="fs-6">คน</small></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 bg-danger text-white rounded-4 h-100">
                        <div class="card-body text-center">
                            <h6 class="mb-2">ขาดงาน / ยังไม่ลงเวลา</h6>
                            <h2 class="fw-bold mb-0">{{ $absentCount }} <small class="fs-6">คน</small></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <form action="/attendance-report" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small mb-1">เลือกวันที่</label>
                            <input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ \Carbon\Carbon::today()->toDateString() }}">
                        </div>
                        <div class="col-md-7">
                            <label class="fw-bold text-muted small mb-1">ค้นหาชื่อ / รหัสพนักงาน</label>
                            <input type="text" name="search" class="form-control" placeholder="พิมพ์เพื่อค้นหา..." value="{{ $search }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark w-100 fw-bold">ดึงรายงาน</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start ps-4">พนักงาน</th>
                                    <th>แผนก</th>
                                    <th>เวลาเข้างาน</th>
                                    <th>เวลาออกงาน</th>
                                    <th>สถานะในวันนี้</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $emp)
                                    @php
                                        // เช็คว่าพนักงานคนนี้มีประวัติลงเวลาของวันนี้ไหม
                                        $record = $attendances->get($emp->id);
                                    @endphp
                                <tr>
                                    <td class="text-start ps-4">
                                        <strong>{{ $emp->first_name }} {{ $emp->last_name }}</strong><br>
                                        <small class="text-muted">{{ $emp->employee_code }}</small>
                                    </td>
                                    <td>{{ $emp->department->name ?? '-' }}</td>
                                    
                                    @if($record)
                                        <td class="text-success fw-bold">{{ $record->clock_in ? \Carbon\Carbon::parse($record->clock_in)->format('H:i') : '-' }}</td>
                                        <td class="text-danger fw-bold">{{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('H:i') : '-' }}</td>
                                        <td><span class="badge bg-success">มาทำงาน</span></td>
                                    @else
                                        <td class="text-muted">-</td>
                                        <td class="text-muted">-</td>
                                        <td><span class="badge bg-danger">ขาดงาน</span></td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">ไม่มีข้อมูลพนักงาน</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                {{ $employees->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>
@endsection