@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <h4 class="fw-bold text-primary">💰 ตั้งค่าฐานเงินเดือนพนักงาน (Salary Setup)</h4>
            <p class="text-muted">กำหนดฐานเงินเดือน ข้อมูลธนาคาร และเลขผู้เสียภาษี สำหรับใช้คำนวณ Payroll</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success fw-bold">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="text-start ps-4">พนักงาน</th>
                            <th>แผนก</th>
                            <th>ฐานเงินเดือน (บาท)</th>
                            <th>ข้อมูลธนาคาร</th>
                            <th>จัดการ</th>
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
                            <td class="text-center text-success fw-bold">
                                {{ $emp->salary ? number_format($emp->salary->base_salary, 2) : '-' }}
                            </td>
                            <td class="text-center">
                                @if($emp->salary && $emp->salary->bank_name)
                                    <span class="badge bg-info text-dark">{{ $emp->salary->bank_name }}</span><br>
                                    <small class="text-muted">{{ $emp->salary->account_number }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#salaryModal{{ $emp->id }}">
                                    ⚙️ ตั้งค่าเงินเดือน
                                </button>
                            </td>
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
    
    <div>
        {{ $employees->links('pagination::bootstrap-5') }}
    </div>
</div>

@foreach($employees as $emp)
<div class="modal fade" id="salaryModal{{ $emp->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">💰 ตั้งค่าเงินเดือน: {{ $emp->first_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/salaries" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">ฐานเงินเดือน (Base Salary) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">฿</span>
                            <input type="number" step="0.01" name="base_salary" class="form-control" value="{{ $emp->salary->base_salary ?? '' }}" required placeholder="เช่น 25000">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">ชื่อธนาคาร</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ $emp->salary->bank_name ?? '' }}" placeholder="เช่น KBank, SCB">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">เลขที่บัญชี</label>
                            <input type="text" name="account_number" class="form-control" value="{{ $emp->salary->account_number ?? '' }}" placeholder="เลขบัญชี 10 หลัก">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">เลขประจำตัวผู้เสียภาษี (Tax ID)</label>
                        <input type="text" name="tax_id" class="form-control" value="{{ $emp->salary->tax_id ?? '' }}" placeholder="เลขบัตร ปชช. หรือ Tax ID">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary fw-bold">💾 บันทึกข้อมูล</button>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endforeach

@endsection