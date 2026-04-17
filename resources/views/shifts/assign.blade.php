@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <h4 class="fw-bold text-primary">🧑‍💼 มอบหมายกะการทำงาน (Assign Shifts)</h4>
            <p class="text-muted">กำหนดกะการทำงานให้พนักงาน (ระบบจะสร้างตารางรายวันให้ถึงสิ้นปีอัตโนมัติ)</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success fw-bold">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="text-start ps-4">พนักงาน</th>
                            <th>แผนก</th>
                            <th>กะการทำงานปัจจุบัน</th>
                            <th>มอบหมายกะใหม่</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $emp)
                        <tr>
                            <td class="text-start ps-4">
                                <strong>{{ $emp->first_name }} {{ $emp->last_name }}</strong><br>
                                <small class="text-muted">{{ $emp->employee_code }}</small>
                            </td>
                            <td class="text-center">{{ $emp->department->name ?? '-' }}</td>
                            <td class="text-center">
                                @if($emp->shift)
                                    <span class="badge bg-success fs-6">{{ $emp->shift->name }}</span><br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($emp->shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($emp->shift->end_time)->format('H:i') }} น.</small>
                                @else
                                    <span class="badge bg-secondary">ยังไม่กำหนดกะ</span>
                                @endif
                            </td>
                            <td>
                                <form action="/shift-assignments" method="POST" class="d-flex justify-content-center align-items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                    
                                    <select name="shift_id" class="form-select form-select-sm w-auto" required>
                                        <option value="">-- เลือกกะ --</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                        @endforeach
                                    </select>

                                    <input type="date" name="effective_date" class="form-control form-control-sm w-auto" value="{{ \Carbon\Carbon::today()->toDateString() }}" required title="วันที่มีผล (Effective Date)">

                                    <button type="submit" class="btn btn-sm btn-primary fw-bold" onclick="return confirm('ยืนยันการมอบหมายกะ? ระบบจะสร้างตารางรายวันให้ถึงสิ้นปี')">บันทึก</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">ไม่มีข้อมูลพนักงาน</td>
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
@endsection