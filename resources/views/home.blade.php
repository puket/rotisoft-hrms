@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">📊 ภาพรวมองค์กร (Dashboard)</h3>
        <span class="text-muted">ข้อมูล ณ วันที่ {{ date('d M Y') }}</span>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="card-title text-uppercase opacity-75">พนักงานทั้งหมด (Active)</h6>
                    <h1 class="fw-bold mb-0">{{ $totalEmployees }} <small class="fs-6 fw-normal">คน</small></h1>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="card-title text-uppercase opacity-75">แผนกทั้งหมด</h6>
                    <h1 class="fw-bold mb-0">{{ $totalDepartments }} <small class="fs-6 fw-normal">แผนก</small></h1>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="card-title text-uppercase opacity-75">อายุเฉลี่ย (Average Age)</h6>
                    <h2 class="fw-bold mb-0">-- <small class="fs-6 fw-normal">ปี</small></h2>
                    <small class="opacity-75">*รอเพิ่มคอลัมน์วันเกิด</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-dark shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="card-title text-uppercase opacity-75">สัดส่วนเพศ (Gender)</h6>
                    <h3 class="fw-bold mb-0 text-center mt-2">ชาย --% | หญิง --%</h3>
                    <small class="opacity-75 d-block text-center">*รอเพิ่มคอลัมน์เพศ</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold">🏢 สัดส่วนพนักงานแยกตามแผนก</h5>
                </div>
                <div class="card-body">
                    @foreach($employeesByDept as $dept)
                        @php
                            // คำนวณเปอร์เซ็นต์เพื่อทำความยาวของกราฟแท่ง
                            $percent = $totalEmployees > 0 ? ($dept->employees_count / $totalEmployees) * 100 : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $dept->name }}</span>
                                <span class="fw-bold">{{ $dept->employees_count }} คน</span>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold">🌳 แผนผังโครงสร้างองค์กร (Org Chart)</h5>
                </div>
                <div class="card-body text-center text-muted d-flex flex-column align-items-center justify-content-center bg-light rounded-bottom-4" style="min-height: 200px;">
                    <h1 class="display-4 mb-3">🏗️</h1>
                    <p class="mb-0 fw-bold">กำลังพัฒนาระบบแสดงแผนผัง</p>
                    <small>ในอนาคตจะเชื่อมต่อไลบรารี Javascript เพื่อแสดงลำดับขั้นหัวหน้า-ลูกน้อง</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection