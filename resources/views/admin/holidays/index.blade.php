@extends('layouts.app')

@section('content')
<div class="container py-4">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-end mb-4">
        <h4 class="fw-bold text-primary mb-0"><i class="bi bi-calendar-check"></i> จัดการวันหยุดบริษัท (Company Holidays)</h4>
        
        <form action="{{ route('holidays.index') }}" method="GET" class="d-flex align-items-center">
            <label class="me-2 fw-bold text-muted">ปีปฏิทิน:</label>
            <select name="year" class="form-select form-select-sm shadow-sm" onchange="this.form.submit()" style="width: 120px;">
                @for($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-plus-circle"></i> เพิ่มวันหยุดใหม่ (ปี {{ $year }})
                </div>
                <div class="card-body">
                    <form action="{{ route('holidays.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label text-sm fw-bold">วันที่หยุด <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm fw-bold">ชื่อวันหยุด <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Ex. วันขึ้นปีใหม่, วันแรงงาน" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-sm fw-bold">รายละเอียดเพิ่มเติม (ระบุหรือไม่ก็ได้)</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="เช่น หยุดชดเชย...">{{ old('description') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                            <i class="bi bi-save"></i> บันทึกวันหยุด
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center border-bottom">
                    <span><i class="bi bi-list-ul text-primary"></i> รายการวันหยุดประจำปี {{ $year }}</span>
                    <span class="badge bg-primary rounded-pill">ทั้งหมด {{ $holidays->count() }} วัน</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 15%;">วันที่</th>
                                    <th style="width: 35%;">ชื่อวันหยุด</th>
                                    <th style="width: 30%;">รายละเอียด</th>
                                    <th class="text-center" style="width: 20%;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($holidays as $holiday)
                                    @php
                                        $dateObj = \Carbon\Carbon::parse($holiday->date);
                                    @endphp
                                <tr>
                                    <td class="text-center fw-bold text-danger">
                                        {{ $dateObj->format('d/m') }}<br>
                                        <small class="text-muted fw-normal">{{ $dateObj->translatedFormat('Y') }}</small>
                                    </td>
                                    <td class="fw-bold text-primary">{{ $holiday->name }}</td>
                                    <td class="text-muted text-sm">{{ $holiday->description ?? '-' }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#editHolidayModal{{ $holiday->id }}" title="แก้ไข">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <form action="{{ route('holidays.destroy', $holiday->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบวันหยุดนี้?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm" title="ลบ">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                        ยังไม่มีการตั้งค่าวันหยุดในปี {{ $year }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($holidays as $holiday)
<div class="modal fade" id="editHolidayModal{{ $holiday->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning border-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square"></i> แก้ไขวันหยุดบริษัท</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('holidays.update', $holiday->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">วันที่หยุด <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ $holiday->date }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">ชื่อวันหยุด <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $holiday->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">รายละเอียดเพิ่มเติม</label>
                        <textarea name="description" class="form-control" rows="3">{{ $holiday->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning fw-bold"><i class="bi bi-check-circle"></i> บันทึกการเปลี่ยนแปลง</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection