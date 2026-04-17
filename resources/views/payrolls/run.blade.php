@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h4 class="fw-bold text-primary mb-0">⚙️ ระบบประมวลผลเงินเดือน (Payroll Run)</h4>
            <p class="text-muted mb-0">คำนวณและสรุปยอดเงินเดือนพนักงานในแต่ละรอบเดือน</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success fw-bold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger fw-bold">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 mb-4 bg-light">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            
            <form action="/payrolls" method="GET" class="d-flex align-items-center gap-2">
                <label class="fw-bold">เลือกรอบเดือน (Period):</label>
                <input type="month" name="period" class="form-control w-auto" value="{{ $period }}" onchange="this.form.submit()">
            </form>

            <form action="/payrolls/calculate" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="period" value="{{ $period }}">
                <button type="submit" class="btn btn-lg btn-warning fw-bold shadow-sm" onclick="return confirm('ยืนยันการรันประมวลผลเงินเดือนรอบ {{ $period }} ใช่หรือไม่? (ระบบจะคำนวณใหม่และทับข้อมูลเดิม)')">
                    ⚡ รันเงินเดือนรอบนี้
                </button>
            </form>

        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white pt-3 pb-2">
            <h6 class="fw-bold mb-0 text-secondary">📋 สรุปผลเงินเดือนรอบ: {{ $period }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start ps-4">พนักงาน</th>
                            <th>ฐานเงินเดือน</th>
                            <th class="text-success">รวมรายรับอื่นๆ</th>
                            <th class="text-danger">รวมรายการหัก</th>
                            <th class="bg-success text-white">ยอดสุทธิ (Net)</th>
                            <th>ตรวจสอบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $pr)
                            @php
                                $totalPlus = $pr->ot_amount + $pr->allowance;
                                $totalMinus = $pr->late_deduction + $pr->tax_amount + $pr->social_security;
                            @endphp
                        <tr>
                            <td class="text-start ps-4">
                                <strong>{{ $pr->employee->first_name }} {{ $pr->employee->last_name }}</strong><br>
                                <small class="text-muted">{{ $pr->employee->department->name ?? '-' }}</small>
                            </td>
                            <td>{{ number_format($pr->base_salary, 2) }}</td>
                            <td class="text-success">+ {{ number_format($totalPlus, 2) }}</td>
                            <td class="text-danger">- {{ number_format($totalMinus, 2) }}</td>
                            <td class="fw-bold fs-5 text-success">{{ number_format($pr->net_salary, 2) }} ฿</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info fw-bold" data-bs-toggle="modal" data-bs-target="#detailModal{{ $pr->id }}">
                                    👁️ รายละเอียด
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-muted py-5">
                                <h5>ยังไม่มีข้อมูลเงินเดือนในรอบนี้</h5>
                                <p>กรุณากดปุ่ม "รันเงินเดือนรอบนี้" ด้านบนเพื่อเริ่มคำนวณ</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach($payrolls as $pr)
<div class="modal fade" id="detailModal{{ $pr->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">🔎 รายละเอียดเงินเดือนรอบ {{ $pr->period }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $pr->employee->first_name }} {{ $pr->employee->last_name }}</h6>
                        <small class="text-muted">รหัสพนักงาน: {{ $pr->employee->employee_code }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge {{ $pr->status == 'Draft' ? 'bg-warning text-dark' : 'bg-success' }}">
                            สถานะ: {{ $pr->status }}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-success mb-3">📈 รายรับ (Income)</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>เงินเดือนพื้นฐาน (Base Salary)</span>
                            <strong>{{ number_format($pr->base_salary, 2) }} ฿</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>ค่าล่วงเวลา (OT)</span>
                            <span class="text-success">+ {{ number_format($pr->ot_amount, 2) }} ฿</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>สวัสดิการ/อื่นๆ (Allowance)</span>
                            <span class="text-success">+ {{ number_format($pr->allowance, 2) }} ฿</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold text-success">
                            <span>รวมรายรับ</span>
                            <span>{{ number_format($pr->base_salary + $pr->ot_amount + $pr->allowance, 2) }} ฿</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold text-danger mb-3">📉 รายการหัก (Deductions)</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>หักมาสาย / ขาดงาน / ลา</span>
                            <span class="text-danger">- {{ number_format($pr->late_deduction, 2) }} ฿</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>ประกันสังคม (SSO)</span>
                            <span class="text-danger">- {{ number_format($pr->social_security, 2) }} ฿</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>ภาษีหัก ณ ที่จ่าย (Tax)</span>
                            <span class="text-danger">- {{ number_format($pr->tax_amount, 2) }} ฿</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold text-danger">
                            <span>รวมรายการหัก</span>
                            <span>{{ number_format($pr->late_deduction + $pr->social_security + $pr->tax_amount, 2) }} ฿</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded text-center border">
                    <span class="fw-bold text-muted d-block mb-1">เงินเดือนสุทธิที่ได้รับ (Net Salary)</span>
                    <h3 class="fw-bold text-primary mb-0">{{ number_format($pr->net_salary, 2) }} บาท</h3>
                </div>

            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection