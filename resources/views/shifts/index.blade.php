@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <h4 class="fw-bold text-primary">⚙️ จัดการรูปแบบกะการทำงาน (Shift Management)</h4>
            <p class="text-muted">สร้างและจัดการช่วงเวลาการทำงานเพื่อนำไปผูกกับพนักงาน</p>
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

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4 bg-light">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">➕ เพิ่มกะการทำงานใหม่</h6>
                    <form action="/shifts" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">ชื่อกะการทำงาน <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="เช่น กะเช้า (Morning), กะดึก (Night)" required>
                        </div>
                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="form-label fw-bold small">เวลาเริ่มงาน <span class="text-danger">*</span></label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">เวลาเลิกงาน <span class="text-danger">*</span></label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">บันทึกกะการทำงาน</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>ชื่อกะ</th>
                                    <th>เวลาเริ่มงาน</th>
                                    <th>เวลาเลิกงาน</th>
                                    <th>จำนวนชั่วโมง (ประมาณ)</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shifts as $shift)
                                    @php
                                        // คำนวณชั่วโมงคร่าวๆ (ถ้าเลิกข้ามวันให้บวก 24 ชม.)
                                        $start = \Carbon\Carbon::parse($shift->start_time);
                                        $end = \Carbon\Carbon::parse($shift->end_time);
                                        if ($end->lessThan($start)) {
                                            $end->addDay();
                                        }
                                        $hours = $start->diffInHours($end);
                                    @endphp
                                <tr>
                                    <td class="text-start ps-4 fw-bold text-dark">{{ $shift->name }}</td>
                                    <td class="text-success fw-bold">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} น.</td>
                                    <td class="text-danger fw-bold">{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }} น.</td>
                                    <td><span class="badge bg-secondary">{{ $hours }} ชม.</span></td>
                                    <td>
                                        <form action="/shifts/{{ $shift->id }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('ยืนยันการลบกะการทำงานนี้?')">🗑️ ลบ</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-muted py-4">ยังไม่มีข้อมูลกะการทำงานในระบบ</td>
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
@endsection