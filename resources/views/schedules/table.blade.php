@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary"><i class="bi bi-table"></i> รายละเอียดตารางการทำงาน</h4>
        <a href="{{ url('/my-schedule') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
            <i class="bi bi-calendar3"></i> สลับไปดูแบบปฏิทิน
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body">
            <form method="GET" action="{{ route('schedules.table') }}" class="row g-3 align-items-end">
                
                @if(Gate::allows('edit-employees'))
                <div class="col-md-4">
                    <label class="form-label text-muted text-sm fw-bold">เลือกพนักงาน (เฉพาะ HR)</label>
                    <select name="employee_id" class="form-select form-select-sm">
                        @foreach($employeesList as $emp)
                            <option value="{{ $emp->id }}" {{ $viewEmployee->id == $emp->id ? 'selected' : '' }}>
                                {{ $emp->employee_code }} - {{ $emp->first_name }} {{ $emp->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="col-md-3">
                    <label class="form-label text-muted text-sm fw-bold">เดือน</label>
                    <select name="month" class="form-select form-select-sm">
                        @for($i=1; $i<=12; $i++)
                            @php $m = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $i, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label text-muted text-sm fw-bold">ปี</label>
                    <select name="year" class="form-select form-select-sm">
                        @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm">
                        <i class="bi bi-search"></i> ค้นหา
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold border-bottom">
            <i class="bi bi-person-lines-fill text-primary"></i> 
            ข้อมูลตารางงานของ: <span class="text-primary">{{ $viewEmployee->first_name }} {{ $viewEmployee->last_name }}</span> 
            ประจำเดือน {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0 text-center" style="font-size: 0.9rem;">
                    <thead class="table-primary text-secondary">
                        <tr>
                            <th>วันที่</th>
                            <th>ประเภท</th>
                            <th class="text-start">กะการทำงาน (Shift)</th>
                            <th>เวลาเข้างาน</th>
                            <th>เวลาออกงาน</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                            @php
                                $dateObj = \Carbon\Carbon::parse($schedule->work_date);
                                $isWeekend = $dateObj->isWeekend();
                            @endphp
                            <tr class="{{ $schedule->is_day_off ? 'table-secondary text-muted' : '' }}">
                                <td class="{{ $isWeekend ? 'text-danger fw-bold' : '' }}">
                                    {{ $dateObj->translatedFormat('d/m/Y (l)') }}
                                </td>
                                <td>
                                    @if($schedule->is_day_off)
                                        <span class="badge bg-secondary">วันหยุด (Day Off)</span>
                                    @else
                                        <span class="badge bg-success">วันทำงาน</span>
                                    @endif
                                </td>
                                <td class="text-start fw-bold">
                                    {{ $schedule->shift ? $schedule->shift->name : '-' }}
                                </td>
                                <td>{{ $schedule->expected_clock_in ? \Carbon\Carbon::parse($schedule->expected_clock_in)->format('H:i') : '-' }}</td>
                                <td>{{ $schedule->expected_clock_out ? \Carbon\Carbon::parse($schedule->expected_clock_out)->format('H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted py-5">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                    ไม่พบข้อมูลตารางการทำงานในเดือนนี้
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection