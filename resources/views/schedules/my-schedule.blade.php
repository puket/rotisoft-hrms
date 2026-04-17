@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="fw-bold text-primary mb-0">📅 ปฏิทินตารางทำงาน (Schedule)</h4>
            <p class="text-muted mb-0">
                แสดงตารางงานของ: <strong class="text-dark">{{ $viewEmployee->first_name }} {{ $viewEmployee->last_name }}</strong>
            </p>
        </div>
        
        @can('edit-employees')
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <form action="/my-schedule" method="GET" class="d-inline-flex align-items-center">
                <label class="me-2 fw-bold text-muted small">ดูตารางของพนักงาน:</label>
                <select name="employee_id" class="form-select form-select-sm w-auto shadow-sm" onchange="this.form.submit()">
                    @foreach($employeesList as $emp)
                        <option value="{{ $emp->id }}" {{ $viewEmployee->id == $emp->id ? 'selected' : '' }}>
                            {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        @endcan
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/th.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'th', // ใช้ภาษาไทย
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek' // ให้ดูแบบเดือน หรือแบบสัปดาห์ได้
            },
            // ดึงข้อมูลผ่าน API ที่เราสร้างไว้ใน Controller
            events: {
                url: '/api/schedules',
                extraParams: {
                    employee_id: '{{ $viewEmployee->id }}' // ส่ง ID พนักงานไปให้ Backend ดึงข้อมูล
                }
            },
            eventDisplay: 'block',
        });
        calendar.render();
    });
</script>

<style>
    /* ตกแต่งปฏิทินให้ดูสะอาดตา */
    .fc-event {
        cursor: pointer;
        border: none !important;
        border-radius: 4px;
        padding: 2px 4px;
        font-size: 0.85em;
    }
    .fc-toolbar-title {
        font-weight: bold !important;
        color: #0d6efd;
    }
</style>
@endsection