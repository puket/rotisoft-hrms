@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="page-title"><i class="bi bi-calendar3 text-primary me-2"></i>ปฏิทินตารางทำงาน</h1>
        <div class="page-subtitle">แสดงตารางงานของ: <strong class="text-dark">{{ $viewEmployee->first_name }} {{ $viewEmployee->last_name }}</strong></div>
    </div>

    @can('edit-employees')
    <form action="/my-schedule" method="GET" class="d-inline-flex align-items-center">
        <label class="me-2 fw-semibold text-muted small">ดูตารางของพนักงาน:</label>
        <select name="employee_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
            @foreach($employeesList as $emp)
                <option value="{{ $emp->id }}" {{ $viewEmployee->id == $emp->id ? 'selected' : '' }}>
                    {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})
                </option>
            @endforeach
        </select>
    </form>
    @endcan
</div>

<div class="card">
    <div class="card-body p-4">
        <div id="calendar"></div>
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
        color: var(--rs-primary);
    }
</style>
@endsection
