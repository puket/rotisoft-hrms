@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold text-primary mb-0">🧾 สลิปเงินเดือนของฉัน (My Payslips)</h4>
            <p class="text-muted">ตรวจสอบรายละเอียดรายรับ-รายจ่าย และยอดสุทธิในแต่ละรอบเดือน</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3">รอบบิล (Period)</th>
                            <th>ฐานเงินเดือน</th>
                            <th class="text-success">รวมรายรับ</th>
                            <th class="text-danger">รวมรายการหัก</th>
                            <th class="bg-success text-white">รับสุทธิ (Net)</th>
                            <th>สถานะ</th>
                            <th>การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $pr)
                            @php
                                $totalPlus = $pr->ot_amount + $pr->allowance;
                                $totalMinus = $pr->late_deduction + $pr->tax_amount + $pr->social_security;
                            @endphp
                        <tr>
                            <td class="fw-bold py-3">{{ Carbon\Carbon::parse($pr->period)->translatedFormat('F Y') }}</td>
                            <td>{{ number_format($pr->base_salary, 2) }}</td>
                            <td class="text-success">+{{ number_format($totalPlus, 2) }}</td>
                            <td class="text-danger">-{{ number_format($totalMinus, 2) }}</td>
                            <td class="fw-bold text-success fs-5">{{ number_format($pr->net_salary, 2) }} ฿</td>
                            <td>
                                <span class="badge {{ $pr->status == 'Draft' ? 'bg-warning text-dark' : 'bg-success' }}">
                                    {{ $pr->status }}
                                </span>
                            </td>
                            <td>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#payslipModal{{ $pr->id }}">
                                        ดูสลิป
                                    </button>
                                    
                                    <a href="{{ route('payrolls.download-pdf', $pr->id) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-muted py-5">
                                <h5>ยังไม่มีข้อมูลสลิปเงินเดือนของคุณ</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        {{ $payrolls->links() }}
    </div>
</div>

@foreach($payrolls as $pr)
<div class="modal fade" id="payslipModal{{ $pr->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-primary text-white border-0 pb-4">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="modal-title fw-bold m-0">PAYSLIP</h4>
                        <h5 class="m-0 border border-white rounded px-3 py-1">เดือน: {{ Carbon\Carbon::parse($pr->period)->translatedFormat('F Y') }}</h5>
                    </div>
                    <div class="row mt-3 text-light">
                        <div class="col-6">
                            <strong>รหัสพนักงาน:</strong> {{ $pr->employee->employee_code }}<br>
                            <strong>ชื่อ-สกุล:</strong> {{ $pr->employee->first_name }} {{ $pr->employee->last_name }}
                        </div>
                        <div class="col-6 text-end">
                            <strong>แผนก:</strong> {{ $pr->employee->department->name ?? '-' }}<br>
                            <strong>ตำแหน่ง:</strong> {{ is_object($pr->employee->position) ? $pr->employee->position->title : $pr->employee->position }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-body p-4 bg-light">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold text-success border-bottom-0 pt-3">
                                <i class="bi bi-arrow-down-circle"></i> รายรับ (Earnings)
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>เงินเดือน (Base Salary)</span>
                                    <span>{{ number_format($pr->base_salary, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>ค่าล่วงเวลา (Overtime)</span>
                                    <span>{{ number_format($pr->ot_amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>สวัสดิการ (Allowance)</span>
                                    <span>{{ number_format($pr->allowance, 2) }}</span>
                                </div>
                            </div>
                            <div class="card-footer bg-success text-white d-flex justify-content-between fw-bold">
                                <span>รวมรายรับ</span>
                                <span>{{ number_format($pr->base_salary + $pr->ot_amount + $pr->allowance, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold text-danger border-bottom-0 pt-3">
                                <i class="bi bi-arrow-up-circle"></i> รายการหัก (Deductions)
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>มาสาย/ขาด/ลา (Absence/Late)</span>
                                    <span>{{ number_format($pr->late_deduction, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>ประกันสังคม (SSO)</span>
                                    <span>{{ number_format($pr->social_security, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>ภาษีหัก ณ ที่จ่าย (Tax)</span>
                                    <span>{{ number_format($pr->tax_amount, 2) }}</span>
                                </div>
                            </div>
                            <div class="card-footer bg-danger text-white d-flex justify-content-between fw-bold">
                                <span>รวมรายการหัก</span>
                                <span>{{ number_format($pr->late_deduction + $pr->social_security + $pr->tax_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 card border-primary shadow-sm">
                    <div class="card-body text-center bg-primary bg-opacity-10 py-4">
                        <p class="text-muted fw-bold mb-1">รายรับสุทธิ (Net Pay)</p>
                        <h2 class="text-primary fw-bold mb-0">THB {{ number_format($pr->net_salary, 2) }}</h2>
                    </div>
                </div>

                @if($pr->otDetails->count() > 0)
                <div class="mt-4 border-top pt-3">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-clock-history"></i> รายละเอียดการทำงานล่วงเวลา (OT Details)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover border" style="font-size: 0.85rem;">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>วันที่</th>
                                    <th>ก่อนงาน (นาที)</th>
                                    <th>หลังงาน (นาที)</th>
                                    <th>รวมชม.</th>
                                    <th>เรท</th>
                                    <th class="text-end">เป็นเงิน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pr->otDetails as $ot)
                                <tr class="text-center">
                                    <td>{{ \Carbon\Carbon::parse($ot->work_date)->format('d/m/Y') }}</td>
                                    <td class="{{ $ot->pre_shift_mins > 0 ? 'text-primary fw-bold' : 'text-muted' }}">
                                        {{ $ot->pre_shift_mins }}
                                    </td>
                                    <td class="{{ $ot->post_shift_mins > 0 ? 'text-primary fw-bold' : 'text-muted' }}">
                                        {{ $ot->post_shift_mins }}
                                    </td>
                                    <td>{{ number_format($ot->total_hours, 2) }}</td>
                                    <td>{{ $ot->multiplier }}x</td>
                                    <td class="text-end fw-bold">{{ number_format($ot->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                @if($pr->items->count() > 0)
                <div class="mt-3">
                    <h6 class="fw-bold text-secondary mb-3">
                        <i class="bi bi-list-check"></i> รายการเงินได้และเงินหักเพิ่มเติม
                    </h6>
                    <ul class="list-group list-group-flush border rounded">
                        @foreach($pr->items as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge {{ $item->item_type == 'Addition' ? 'bg-success' : 'bg-danger' }} me-2">
                                    {{ $item->item_type == 'Addition' ? '+' : '-' }}
                                </span>
                                <strong>{{ $item->item_name }}</strong>
                                <div class="text-muted small ps-4">{{ $item->description }}</div>
                            </div>
                            <span class="fw-bold {{ $item->item_type == 'Addition' ? 'text-success' : 'text-danger' }}">
                                {{ number_format($item->amount, 2) }}
                            </span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

            </div>
            
            <div class="modal-footer border-0 bg-light d-flex justify-content-between">
                <small class="text-muted">เอกสารนี้สร้างโดยระบบอัตโนมัติ ไม่จำเป็นต้องมีลายเซ็น</small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection