@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="page-title">ภาพรวมองค์กร</h1>
        <div class="page-subtitle">ยินดีต้อนรับกลับมา 👋 ข้อมูล ณ วันที่ {{ date('d M Y') }}</div>
    </div>
    <a href="{{ url('/employees') }}" class="btn btn-primary">
        <i class="bi bi-people-fill me-1"></i> จัดการพนักงาน
    </a>
</div>

{{-- ===================== Stat cards ===================== --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card--indigo">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">พนักงานทั้งหมด (Active)</div>
            <div class="stat-value">{{ $totalEmployees }} <span class="fs-6 fw-normal opacity-75">คน</span></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card--green">
            <div class="stat-icon"><i class="bi bi-diagram-2-fill"></i></div>
            <div class="stat-label">แผนกทั้งหมด</div>
            <div class="stat-value">{{ $totalDepartments }} <span class="fs-6 fw-normal opacity-75">แผนก</span></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card--purple">
            <div class="stat-icon"><i class="bi bi-cake2-fill"></i></div>
            <div class="stat-label">อายุเฉลี่ย</div>
            <div class="stat-value">-- <span class="fs-6 fw-normal opacity-75">ปี</span></div>
            <div class="stat-hint">*รอเพิ่มคอลัมน์วันเกิด</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card--blue">
            <div class="stat-icon"><i class="bi bi-gender-ambiguous"></i></div>
            <div class="stat-label">สัดส่วนเพศ</div>
            <div class="stat-value">-- <span class="fs-6 fw-normal opacity-75">%</span></div>
            <div class="stat-hint">*รอเพิ่มคอลัมน์เพศ</div>
        </div>
    </div>
</div>

{{-- ===================== Charts / panels ===================== --}}
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-fill text-primary me-2"></i>สัดส่วนพนักงานแยกตามแผนก</h5>
            </div>
            <div class="card-body px-4">
                @forelse($employeesByDept as $dept)
                    @php $percent = $totalEmployees > 0 ? ($dept->employees_count / $totalEmployees) * 100 : 0; @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">{{ $dept->name }}</span>
                            <span class="badge-soft badge-soft-primary">{{ $dept->employees_count }} คน</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar"
                                 style="width: {{ $percent }}%;"
                                 aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">ยังไม่มีข้อมูลแผนก</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-diagram-3-fill text-primary me-2"></i>แผนผังโครงสร้างองค์กร</h5>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center text-muted"
                 style="min-height: 220px;">
                <div class="display-4 mb-3">🏗️</div>
                <p class="mb-1 fw-bold">กำลังพัฒนาระบบแสดงแผนผัง</p>
                <small>ในอนาคตจะเชื่อมต่อไลบรารี JavaScript เพื่อแสดงลำดับขั้นหัวหน้า-ลูกน้อง</small>
            </div>
        </div>
    </div>
</div>
@endsection
