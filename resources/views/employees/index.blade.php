@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center pt-3 pb-2">
                    <h5 class="mb-0 fw-bold">👥 รายชื่อพนักงาน (Employee Directory)</h5>
                    
                    @can('edit-employees')
                        <a href="/employees/create" class="btn btn-light btn-sm fw-bold">+ เพิ่มพนักงานใหม่</a>
                    @endcan
                </div>

                <div class="card-body">
                    
                    <form action="/employees" method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="ค้นหาด้วยรหัส, ชื่อ หรือ นามสกุล..." value="{{ $search }}">
                                    
                                    <button class="btn btn-primary" type="submit">🔍 ค้นหา</button>
                                    
                                    @if($search)
                                        <a href="/employees" class="btn btn-outline-secondary">ล้างค่า</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัสพนักงาน</th>
                                    <th>ข้อมูลพนักงาน</th>
                                    <th>แผนก / ตำแหน่ง</th>
                                    <th>หัวหน้างาน (Report To)</th>
                                    <th>สถานะ</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($employees->isEmpty())
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            ไม่พบข้อมูลพนักงานที่ค้นหา
                                        </td>
                                    </tr>
                                @endif

                                @foreach($employees as $emp)
                                <tr>
                                    <td class="text-muted fw-bold">{{ $emp->employee_code }}</td>
                                    <td>
                                        <strong>{{ $emp->first_name }} {{ $emp->last_name }}</strong><br>
                                        <small class="text-muted">📧 {{ $emp->email ?? '-' }}</small><br>
                                        <small class="text-muted">📞 {{ $emp->phone_number ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark mb-1">
                                            {{ $emp->department ? $emp->department->name : 'ไม่มีแผนก' }}
                                        </span><br>
                                        <small>{{ $emp->position ? $emp->position->title : '-' }}</small>
                                    </td>
                                    <td>
                                        @if($emp->manager)
                                            <span class="text-primary fw-bold">{{ $emp->manager->first_name }} {{ $emp->manager->last_name }}</span><br>
                                            <small class="text-muted">({{ $emp->manager->position ? $emp->manager->position->title : 'Manager' }})</small>
                                        @else
                                            <span class="text-muted">- ระดับสูงสุด -</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($emp->status == 'Active')
                                            <span class="badge bg-success">ทำงานอยู่</span>
                                        @elseif($emp->status == 'Resigned')
                                            <span class="badge bg-danger">ลาออก</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $emp->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="/employees/{{ $emp->id }}" class="btn btn-sm btn-outline-secondary">ดูข้อมูล</a>

                                        @can('edit-employees')
                                            <a href="/employees/{{ $emp->id }}/edit" class="btn btn-sm btn-outline-primary">แก้ไข</a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card-footer bg-white d-flex justify-content-end py-3">
                    {{ $employees->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection