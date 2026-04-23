@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center pt-3 pb-2">
                    <h5 class="mb-0 fw-bold">👥 ข้อมูลพนักงาน (Employee Information)</h5>
                    <a href="{{ url('/employees') }}" class="btn btn-light btn-sm fw-bold"> <i class="bi bi-arrow-left"></i> กลับหน้ารายชื่อพนักงาน</a>
                </div>

                <div class="card-body">

                    {{-- 🌟 แจ้งเตือนเมื่อบันทึกข้อมูลสำเร็จ หรือ Error --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>สำเร็จ!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

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
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold" id="document-tab" data-bs-toggle="tab" data-bs-target="#document" type="button" role="tab">เอกสารแนบ</button>
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
                                    {{-- 🌟 เพิ่มปุ่ม Add --}}
                                    <div class="d-flex justify-content-end mb-3">
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEducationModal">
                                            <i class="fas fa-plus"></i> เพิ่มประวัติการศึกษา
                                        </button>
                                    </div>

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
                                                    <th class="text-center">จัดการ</th>
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
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                                {{-- Tab 3: ประวัติการทำงาน --}}
                                <div class="tab-pane fade" id="experience" role="tabpanel">
                                    {{-- 🌟 เพิ่มปุ่ม Add --}}
                                    <div class="d-flex justify-content-end mb-3">
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addExperienceModal">
                                            <i class="fas fa-plus"></i> เพิ่มประวัติการทำงาน
                                        </button>
                                    </div>

                                    @if($employee->experiences->isEmpty())
                                        <div class="text-center text-muted py-5">
                                            <i class="fas fa-briefcase fs-1 mb-3"></i><br>ยังไม่มีข้อมูลประวัติการทำงาน
                                        </div>
                                    @else
                                        <div class="timeline mt-4">
                                            @foreach($employee->experiences->sortByDesc('start_date') as $exp)
                                                <div class="mb-4 border-start border-3 border-primary ps-3 ms-2 position-relative">
                                                    {{-- ปุ่มลบ --}}
                                                    <button class="btn btn-sm btn-outline-danger position-absolute top-0 end-0"><i class="fas fa-trash"></i></button>
                                                    
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
                                    {{-- 🌟 เพิ่มปุ่ม Add --}}
                                    <div class="d-flex justify-content-end mb-3">
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTrainingModal">
                                            <i class="fas fa-plus"></i> เพิ่มประวัติอบรม
                                        </button>
                                    </div>

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
                                                    <th class="text-center">จัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($employee->trainings as $tr)
                                                <tr>
                                                    <td>{{ $tr->course_name }}</td>
                                                    <td>{{ $tr->organizer ?? '-' }}</td>
                                                    <td>{{ $tr->completion_date ? \Carbon\Carbon::parse($tr->completion_date)->format('d/m/Y') : '-' }}</td>
                                                    <td>{{ $tr->certificate_no ?? '-' }}</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                                {{-- Tab 5: เอกสารแนบ --}}
                                <div class="tab-pane fade" id="document" role="tabpanel">
                                    <div class="d-flex justify-content-end mb-3">
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                                            <i class="fas fa-upload"></i> อัปโหลดเอกสาร
                                        </button>
                                    </div>

                                    @if($employee->documents->isEmpty())
                                        <div class="text-center text-muted py-5">
                                            <i class="fas fa-folder-open fs-1 mb-3"></i><br>ยังไม่มีเอกสารแนบ
                                        </div>
                                    @else
                                        <table class="table table-hover mt-3">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ชื่อเอกสาร</th>
                                                    <th>ประเภทเอกสาร</th>
                                                    <th>วันที่อัปโหลด</th>
                                                    <th class="text-center">ดูไฟล์</th>
                                                    <th class="text-center">จัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($employee->documents as $doc)
                                                <tr>
                                                    <td>{{ $doc->document_name }}</td>
                                                    <td>
                                                        @php
                                                            $docTypes = [
                                                                'ID_Card' => 'บัตรประชาชน', 'House_Registration' => 'ทะเบียนบ้าน',
                                                                'Bookbank' => 'หน้าสมุดบัญชี', 'Resume' => 'เรซูเม่',
                                                                'Certificate' => 'ใบรับรอง/วุฒิ', 'Contract' => 'สัญญาจ้าง', 'Other' => 'อื่นๆ'
                                                            ];
                                                        @endphp
                                                        <span class="badge bg-secondary">{{ $docTypes[$doc->document_type] ?? $doc->document_type }}</span>
                                                    </td>
                                                    <td>{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i></a>
                                                    </td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                                <div class="text-end mb-3 mt-4 border-top pt-3">
                                    <a href="/employees/{{ $employee->id }}/edit" class="btn btn-warning px-4 fw-bold"><i class="fas fa-edit"></i> แก้ไขข้อมูลหลักพนักงาน</a>
                                </div>

                            </div>
                        </div>

                    </div>
                    
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- 🌟 MODALS: Pop-up สำหรับกรอกข้อมูลเพิ่มประวัติต่างๆ --}}
{{-- ========================================================= --}}

{{-- 1. Modal ประวัติการศึกษา --}}
<div class="modal fade" id="addEducationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-graduation-cap"></i> เพิ่มประวัติการศึกษา</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('employees.educations.store', $employee->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">ระดับการศึกษา <span class="text-danger">*</span></label>
                        <input type="text" name="degree" class="form-control" placeholder="เช่น ปริญญาตรี" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">สาขาวิชา <span class="text-danger">*</span></label>
                        <input type="text" name="major" class="form-control" placeholder="เช่น วิทยาการคอมพิวเตอร์" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">สถาบันการศึกษา <span class="text-danger">*</span></label>
                        <input type="text" name="institution" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">ปีที่จบ (ค.ศ.)</label>
                            <input type="text" name="graduation_year" class="form-control" placeholder="เช่น 2023" maxlength="4">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">เกรดเฉลี่ย (GPA)</label>
                            <input type="number" name="gpa" class="form-control" step="0.01" min="0" max="4.00" placeholder="เช่น 3.50">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 2. Modal ประวัติการทำงาน --}}
<div class="modal fade" id="addExperienceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-briefcase"></i> เพิ่มประวัติการทำงาน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            {{-- ตรงนี้เดี๋ยวเราค่อยไปสร้าง Route กันครับ --}}
            <form action="{{ url('/employees/'.$employee->id.'/experiences') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">ชื่อบริษัท <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">ตำแหน่งงาน <span class="text-danger">*</span></label>
                        <input type="text" name="job_title" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">วันที่เริ่มงาน</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">วันที่สิ้นสุด</label>
                            <input type="date" name="end_date" class="form-control">
                            <small class="text-muted">ปล่อยว่างถ้าเป็นงานปัจจุบัน</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">รายละเอียดงาน</label>
                        <textarea name="job_description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 3. Modal ประวัติอบรม --}}
<div class="modal fade" id="addTrainingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-certificate"></i> เพิ่มประวัติการฝึกอบรม</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            {{-- ตรงนี้เดี๋ยวเราค่อยไปสร้าง Route กันครับ --}}
            <form action="{{ url('/employees/'.$employee->id.'/trainings') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">ชื่อหลักสูตร <span class="text-danger">*</span></label>
                        <input type="text" name="course_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">ผู้จัดอบรม / สถาบัน</label>
                        <input type="text" name="organizer" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">วันที่อบรมเสร็จสิ้น</label>
                        <input type="date" name="completion_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">เลขที่ใบประกาศนียบัตร</label>
                        <input type="text" name="certificate_no" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 4. Modal อัปโหลดเอกสารแนบ --}}
<div class="modal fade" id="addDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-file-upload"></i> อัปโหลดเอกสารพนักงาน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            {{-- 🌟 ต้องมี enctype="multipart/form-data" เสมอเวลาอัปโหลดไฟล์ --}}
            <form action="{{ url('/employees/'.$employee->id.'/documents') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">ชื่อเอกสาร <span class="text-danger">*</span></label>
                        <input type="text" name="document_name" class="form-control" placeholder="เช่น สำเนาบัตรประชาชน" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">ประเภทเอกสาร <span class="text-danger">*</span></label>
                        <select name="document_type" class="form-select" required>
                            <option value="">-- เลือกประเภท --</option>
                            <option value="ID_Card">บัตรประชาชน</option>
                            <option value="House_Registration">ทะเบียนบ้าน</option>
                            <option value="Bookbank">หน้าสมุดบัญชีธนาคาร</option>
                            <option value="Resume">เรซูเม่ (Resume)</option>
                            <option value="Certificate">ใบรับรอง / วุฒิการศึกษา</option>
                            <option value="Contract">สัญญาจ้าง</option>
                            <option value="Other">อื่นๆ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">เลือกไฟล์ <span class="text-danger">*</span></label>
                        <input type="file" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="text-muted">รองรับไฟล์ PDF, JPG, PNG ขนาดไม่เกิน 5MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">💾 อัปโหลดไฟล์</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection