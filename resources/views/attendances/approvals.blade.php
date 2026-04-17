@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <h4 class="fw-bold text-success mb-4">⏱️ อนุมัติคำร้องขอแก้ไขเวลา (Attendance Approvals)</h4>

            @if(session('success'))
                <div class="alert alert-success fw-bold">{{ session('success') }}</div>
            @endif

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>พนักงาน</th>
                                    <th>วันที่ขอแก้</th>
                                    <th>เวลาที่ขอเข้า</th>
                                    <th>เวลาที่ขอออก</th>
                                    <th>เหตุผล</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $req)
                                <tr>
                                    <td>
                                        <strong>{{ $req->employee->first_name }} {{ $req->employee->last_name }}</strong><br>
                                        <small class="text-muted">{{ $req->employee->position->title ?? '-' }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($req->work_date)->format('d/m/Y') }}</td>
                                    <td class="text-success fw-bold">{{ $req->requested_clock_in ? \Carbon\Carbon::parse($req->requested_clock_in)->format('H:i') : '-' }}</td>
                                    <td class="text-danger fw-bold">{{ $req->requested_clock_out ? \Carbon\Carbon::parse($req->requested_clock_out)->format('H:i') : '-' }}</td>
                                    <td><small class="text-muted">{{ $req->reason }}</small></td>
                                    <td class="text-center">
                                        <form action="/attendance-requests/{{ $req->id }}/status" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="Approved">
                                            <button type="submit" class="btn btn-sm btn-success fw-bold" onclick="return confirm('ยืนยันการอนุมัติ?')">อนุมัติ</button>
                                        </form>
                                        
                                        <form action="/attendance-requests/{{ $req->id }}/status" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="Rejected">
                                            <button type="submit" class="btn btn-sm btn-danger fw-bold" onclick="return confirm('ไม่อนุมัติคำร้องนี้?')">ปฏิเสธ</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">🎉 ไม่มีรายการคำร้องขอแก้ไขเวลาที่รออนุมัติ</td>
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
@endsection