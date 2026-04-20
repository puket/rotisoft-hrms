@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center pt-3 pb-2">
                    <h5 class="mb-0 fw-bold">👥 ข้อมูลพนักงาน (Employee Information)</h5>
                    <a href="{{ url('/employees') }}" class="btn btn-light btn-sm fw-bold"> <i class="bi bi-arrow-left"></i> กลับหน้ารายชื่อ</a>
                </div>

                <div class="card-body">

                    {{-- ส่วนหัว: การ์ดโปรไฟล์สรุป --}}
                    <div class="card shadow-sm mb-4 border-0">
                        <div class="card-body d-flex align-items-center">
                            {{-- รูปโปรไฟล์จำลอง (ใช้ตัวอักษรตัวแรกของชื่อ) --}}
                            <div class="me-4">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 90px; height: 90px; font-size: 2.5rem;">
                                    {{ mb_substr($employee->first_name, 0, 1) }}
                                </div>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
                                <p class="text-muted mb-2 fs-5">
                                    <i class="fas fa-id-badge me-1"></i> {{ $employee->employee_code }} | 
                                    <i class="fas fa-briefcase me-1"></i> {{ $employee->position->title ?? 'ไม่มีตำแหน่ง' }} 
                                    (<i class="fas fa-building me-1"></i> {{ $employee->department->name ?? 'ไม่มีแผนก' }})
                                </p>
                                <span class="badge bg-{{ $employee->status == 'Active' ? 'success' : 'danger' }} px-3 py-2">
                                    สถานะ: {{ $employee->status }}
                                </span>
                                <span class="badge bg-info text-dark px-3 py-2 ms-2">
                                    {{ $employee->employee_type == 'Monthly' ? 'พนักงานรายเดือน' : 'พนักงานรายวัน' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- ส่วนรายละเอียด: แบ่งเป็นแท็บ (Tabs) --}}
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white pt-3 pb-0 border-bottom">
                            <ul class="nav nav-tabs border-0" id="profileTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-bold" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">ข้อมูลทั่วไป</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold" id="education-tab" data-bs-toggle="tab" data-bs-target="#education" type="button" role="tab">ประวัติการศึกษา</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold" id="experience-tab" data-bs-toggle="tab" data-bs-target="#experience" type="button" role="tab">ประวัติการทำงาน</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold" id="training-tab" data-bs-toggle="tab" data-bs-target="#training" type="button" role="tab">ประวัติอบรม</button>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="card-body p-4">
                            <div class="tab-content" id="profileTabsContent">
                                
                                {{-- Tab 1: ข้อมูลทั่วไป --}}
                                <div class="tab-pane fade show active" id="info" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <h5 class="text-primary border-bottom pb-2"><i class="fas fa-user"></i> ข้อมูลส่วนตัว</h5>
                                            <table class="table table-borderless table-sm mt-3">
                                                <tr><th width="35%" class="text-muted">อีเมล:</th><td>{{ $employee->email ?? '-' }}</td></tr>
                                                <tr><th class="text-muted">เบอร์โทรศัพท์:</th><td>{{ $employee->phone_number ?? '-' }}</td></tr>
                                                <tr><th class="text-muted">วันเกิด:</th><td>{{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d/m/Y') : '-' }}</td></tr>
                                                <tr><th class="text-muted">เลขบัตร ปชช:</th><td>{{ $employee->national_id ?? '-' }}</td></tr>
                                                <tr><th class="text-muted">เพศ:</th><td>{{ $employee->gender ?? '-' }}</td></tr>
                                                <tr><th class="text-muted">ที่อยู่:</th><td>{{ $employee->address ?? '-' }}</td></tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <h5 class="text-primary border-bottom pb-2"><i class="fas fa-file-signature"></i> ข้อมูลการจ้างงาน</h5>
                                            <table class="table table-borderless table-sm mt-3">
                                                <tr><th width="35%" class="text-muted">วันที่เริ่มงาน:</th><td>{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') : '-' }}</td></tr>
                                                <tr><th class="text-muted">สถานะการจ้าง:</th><td>{{ $employee->employment_status ?? '-' }}</td></tr>
                                                <tr><th class="text-muted">หัวหน้างาน:</th>
                                                    <td>
                                                        @if($employee->manager)
                                                            <a href="{{ route('employees.show', $employee->manager_id) }}">{{ $employee->manager->first_name }} {{ $employee->manager->last_name }}</a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr><th class="text-muted">ติดต่อฉุกเฉิน:</th><td>{{ $employee->emergency_contact_name ?? '-' }} ({{ $employee->emergency_contact_phone ?? '-' }})</td></tr>
                                                <tr><th class="text-muted">ธนาคาร:</th><td>{{ $employee->bank_name ?? '-' }} ({{ $employee->bank_account ?? '-' }})</td></tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tab 2: ประวัติการศึกษา --}}
                                <div class="tab-pane fade" id="education" role="tabpanel">
                                    @if($employee->educations->isEmpty())
                                        <div class="text-center text-muted py-5">
                                            <i class="fas fa-graduation-cap fs-1 mb-3"></i><br>ยังไม่มีข้อมูลประวัติการศึกษา
                                        </div>
                                    @else
                                        <table class="table table-hover mt-3">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ระดับการศึกษา</th>
                                                    <th>สาขาวิชา</th>
                                                    <th>สถาบันการศึกษา</th>
                                                    <th>ปีที่จบ</th>
                                                    <th>เกรดเฉลี่ย</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($employee->educations as $edu)
                                                <tr>
                                                    <td>{{ $edu->degree }}</td>
                                                    <td>{{ $edu->major }}</td>
                                                    <td>{{ $edu->institution }}</td>
                                                    <td>{{ $edu->graduation_year ?? '-' }}</td>
                                                    <td>{{ $edu->gpa ?? '-' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                                {{-- Tab 3: ประวัติการทำงาน --}}
                                <div class="tab-pane fade" id="experience" role="tabpanel">
                                    @if($employee->experiences->isEmpty())
                                        <div class="text-center text-muted py-5">
                                            <i class="fas fa-briefcase fs-1 mb-3"></i><br>ยังไม่มีข้อมูลประวัติการทำงาน
                                        </div>
                                    @else
                                        <div class="timeline mt-4">
                                            @foreach($employee->experiences->sortByDesc('start_date') as $exp)
                                                <div class="mb-4 border-start border-3 border-primary ps-3 ms-2">
                                                    <h5 class="mb-1 fw-bold">{{ $exp->job_title }}</h5>
                                                    <h6 class="text-primary mb-2">{{ $exp->company_name }}</h6>
                                                    <p class="text-muted mb-1 small">
                                                        <i class="far fa-calendar-alt"></i> 
                                                        {{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('d/m/Y') : '-' }} ถึง 
                                                        {{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('d/m/Y') : 'ปัจจุบัน' }}
                                                    </p>
                                                    <p class="mb-0">{{ $exp->job_description }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- Tab 4: ประวัติอบรม --}}
                                <div class="tab-pane fade" id="training" role="tabpanel">
                                    @if($employee->trainings->isEmpty())
                                        <div class="text-center text-muted py-5">
                                            <i class="fas fa-certificate fs-1 mb-3"></i><br>ยังไม่มีข้อมูลประวัติการฝึกอบรม
                                        </div>
                                    @else
                                        <table class="table table-hover mt-3">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ชื่อหลักสูตร</th>
                                                    <th>ผู้จัดอบรม/สถาบัน</th>
                                                    <th>วันที่อบรมเสร็จสิ้น</th>
                                                    <th>เลขที่ใบประกาศ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($employee->trainings as $tr)
                                                <tr>
                                                    <td>{{ $tr->course_name }}</td>
                                                    <td>{{ $tr->organizer ?? '-' }}</td>
                                                    <td>{{ $tr->completion_date ? \Carbon\Carbon::parse($tr->completion_date)->format('d/m/Y') : '-' }}</td>
                                                    <td>{{ $tr->certificate_no ?? '-' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                    
                </div>
           
            </div>

        </div>
    </div>
</div>
@endsection