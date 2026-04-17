@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h4 class="fw-bold text-primary mb-4">✅ จัดการใบลาของทีม (Team Leaves)</h4>

            @if(session('success'))
                <div class="alert alert-success fw-bold">{{ session('success') }}</div>
            @endif

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <form action="/leave-approvals" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" placeholder="🔍 ค้นหาชื่อลูกน้อง..." value="{{ $search }}">
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="Pending" {{ $statusFilter == 'Pending' ? 'selected' : '' }}>⏳ รออนุมัติ (Pending)</option>
                                <option value="Approved" {{ $statusFilter == 'Approved' ? 'selected' : '' }}>✅ อนุมัติแล้ว (Approved)</option>
                                <option value="Rejected" {{ $statusFilter == 'Rejected' ? 'selected' : '' }}>❌ ไม่อนุมัติ (Rejected)</option>
                                <option value="All" {{ $statusFilter == 'All' ? 'selected' : '' }}>📂 ทั้งหมด (All)</option>
                            </select>
                        </div>
                        <div class="col-md-3 text-end">
                            <button type="submit" class="btn btn-dark w-100 fw-bold">ค้นหา</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>พนักงานที่ขอลา</th>
                                    <th>ประเภท</th>
                                    <th>วันที่ลา</th>
                                    <th>เหตุผล</th>
                                    <th class="text-center">สถานะ/จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaves as $leave)
                                <tr>
                                    <td>
                                        <strong>{{ $leave->employee->first_name }} {{ $leave->employee->last_name }}</strong><br>
                                        <small class="text-muted">{{ $leave->employee->position->title ?? 'ไม่มีตำแหน่ง' }}</small>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $leave->leaveType->name ?? '-' }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</td>
                                    <td><small class="text-muted">{{ $leave->reason ?? '-' }}</small></td>
                                    <td class="text-center">
                                        
                                        @if($leave->status == 'Pending')
                                            <form action="/leaves/{{ $leave->id }}/status" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="Approved">
                                                <button type="submit" class="btn btn-sm btn-success fw-bold" onclick="return confirm('ยืนยันการอนุมัติใบลา?')">อนุมัติ</button>
                                            </form>
                                            
                                            <form action="/leaves/{{ $leave->id }}/status" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="Rejected">
                                                <button type="submit" class="btn btn-sm btn-danger fw-bold" onclick="return confirm('ไม่อนุมัติใบลา?')">ปฏิเสธ</button>
                                            </form>
                                        @elseif($leave->status == 'Approved')
                                            <span class="badge bg-success">✅ อนุมัติแล้ว</span>
                                        @elseif($leave->status == 'Rejected')
                                            <span class="badge bg-danger">❌ ปฏิเสธแล้ว</span>
                                        @endif

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">🎉 ไม่มีรายการใบลาตรงตามเงื่อนไข</td>
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
@endsection