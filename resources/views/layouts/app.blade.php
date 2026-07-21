<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RotiSoft') }}</title>

    {{-- Fonts: Inter (Latin) + Noto Sans Thai (Thai) --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|noto-sans-thai:400,500,600,700" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
@php
    $user = auth()->user();
    // Active-state helper
    $isActive = fn($patterns) => collect((array) $patterns)->contains(fn($p) => request()->is($p)) ? 'active' : '';
@endphp

<div id="app">
    @auth
    <div class="app-shell">

        {{-- ============================ SIDEBAR ============================ --}}
        <aside class="app-sidebar" id="appSidebar">
            <a href="{{ url('/home') }}" class="sidebar-brand">
                <span class="brand-logo">R</span>
                <span>{{ config('app.name', 'RotiSoft') }}</span>
                <span class="brand-badge">HRMS</span>
            </a>

            <div class="sidebar-company">
                <span class="company-avatar">{{ mb_substr(optional($user->company)->name ?? 'R', 0, 1) }}</span>
                <div>
                    <div class="company-name">{{ optional($user->company)->name ?? 'RotiSoft' }}</div>
                    <div class="company-role">{{ $user->role ?? 'member' }}</div>
                </div>
            </div>

            <nav class="sidebar-scroll">
                {{-- Main --}}
                <div class="nav-section-title">หลัก</div>
                <a href="{{ url('/home') }}" class="sidebar-link {{ $isActive(['home', '/']) }}">
                    <i class="bi bi-grid-1x2-fill"></i> แดชบอร์ด
                </a>

                {{-- Employee self-service --}}
                <div class="nav-section-title">บริการพนักงาน (ESS)</div>
                <a href="{{ url('/attendance') }}" class="sidebar-link {{ $isActive('attendance') }}">
                    <i class="bi bi-clock-fill"></i> ลงเวลาเข้า-ออกงาน
                </a>
                <a href="{{ url('/leaves') }}" class="sidebar-link {{ $isActive('leaves') }}">
                    <i class="bi bi-calendar2-x-fill"></i> ระบบลางาน
                </a>
                <a href="{{ url('/ot-requests') }}" class="sidebar-link {{ $isActive('ot-requests') }}">
                    <i class="bi bi-hourglass-split"></i> ขอทำล่วงเวลา (OT)
                </a>
                <a href="{{ url('/my-schedule') }}" class="sidebar-link {{ $isActive('my-schedule') }}">
                    <i class="bi bi-calendar3"></i> ตารางงานของฉัน
                </a>
                <a href="{{ url('/my-payslips') }}" class="sidebar-link {{ $isActive('my-payslips') }}">
                    <i class="bi bi-receipt"></i> สลิปเงินเดือน
                </a>
                <a href="{{ url('/organization-chart') }}" class="sidebar-link {{ $isActive('organization-chart') }}">
                    <i class="bi bi-diagram-3-fill"></i> แผนผังองค์กร
                </a>

                @can('is-manager')
                <div class="nav-section-title">สำหรับหัวหน้างาน (MSS)</div>
                <a href="{{ url('/leave-approvals') }}" class="sidebar-link {{ $isActive('leave-approvals') }}">
                    <i class="bi bi-check2-square"></i> อนุมัติการลา
                </a>
                <a href="{{ url('/attendance-approvals') }}" class="sidebar-link {{ $isActive('attendance-approvals') }}">
                    <i class="bi bi-check2-square"></i> อนุมัติคำร้องเวลา
                </a>
                <a href="{{ url('/ot-approvals') }}" class="sidebar-link {{ $isActive('ot-approvals') }}">
                    <i class="bi bi-check2-square"></i> อนุมัติ OT
                </a>
                @endcan

                @can('is-hr')
                <div class="nav-section-title">จัดการระบบ (HR)</div>
                <a href="{{ url('/employees') }}" class="sidebar-link {{ $isActive('employees*') }}">
                    <i class="bi bi-people-fill"></i> พนักงานทั้งหมด
                </a>
                <a href="{{ url('/shifts') }}" class="sidebar-link {{ $isActive('shifts') }}">
                    <i class="bi bi-clock-history"></i> กะการทำงาน
                </a>
                <a href="{{ url('/shift-assignments') }}" class="sidebar-link {{ $isActive('shift-assignments') }}">
                    <i class="bi bi-calendar-week-fill"></i> จัดตารางงานพนักงาน
                </a>
                <a href="{{ url('/salaries') }}" class="sidebar-link {{ $isActive('salaries*') }}">
                    <i class="bi bi-cash-stack"></i> ฐานเงินเดือน
                </a>
                <a href="{{ url('/payrolls') }}" class="sidebar-link {{ $isActive('payrolls*') }}">
                    <i class="bi bi-wallet2"></i> รันเงินเดือน (Payroll)
                </a>
                <a href="{{ url('/attendance-report') }}" class="sidebar-link {{ $isActive('attendance-report') }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> รายงานการลงเวลา
                </a>
                @endcan

                @can('is-tenant-admin')
                <div class="nav-section-title">ตั้งค่าองค์กร</div>
                <a href="{{ url('/departments') }}" class="sidebar-link {{ $isActive('departments*') }}">
                    <i class="bi bi-diagram-2-fill"></i> แผนก (Departments)
                </a>
                <a href="{{ url('/positions') }}" class="sidebar-link {{ $isActive('positions*') }}">
                    <i class="bi bi-person-badge-fill"></i> ตำแหน่ง (Positions)
                </a>
                <a href="{{ url('/holidays') }}" class="sidebar-link {{ $isActive('holidays*') }}">
                    <i class="bi bi-calendar-heart-fill"></i> วันหยุดบริษัท
                </a>
                <a href="{{ url('/admin/ot-settings') }}" class="sidebar-link {{ $isActive('admin/ot-settings*') }}">
                    <i class="bi bi-sliders"></i> ตั้งค่ากฎ OT
                </a>
                @endcan

                @can('is-super-admin')
                <div class="nav-section-title">ผู้ดูแลระบบ</div>
                <a href="{{ url('/companies') }}" class="sidebar-link {{ $isActive('companies*') }}">
                    <i class="bi bi-buildings-fill"></i> จัดการบริษัทลูกค้า
                </a>
                @endcan
            </nav>
        </aside>

        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        {{-- ============================ MAIN ============================== --}}
        <div class="app-main">
            <header class="app-topbar">
                <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle menu">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <div class="topbar-search d-none d-sm-block">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="ค้นหา...">
                </div>

                <div class="ms-auto d-flex align-items-center gap-2">
                    <button class="topbar-icon-btn" type="button" aria-label="Notifications">
                        <i class="bi bi-bell"></i><span class="dot"></span>
                    </button>

                    <div class="dropdown">
                        <div class="topbar-user" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar-circle">{{ mb_substr($user->name ?? 'U', 0, 1) }}</span>
                            <div class="d-none d-md-block lh-sm">
                                <div class="fw-semibold" style="font-size:.85rem">{{ $user->name }}</div>
                                <div class="text-muted" style="font-size:.72rem">{{ $user->email }}</div>
                            </div>
                            <i class="bi bi-chevron-down text-muted d-none d-md-inline" style="font-size:.7rem"></i>
                        </div>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="/profile"><i class="bi bi-person me-2"></i>ข้อมูลส่วนตัว</a>
                            <hr class="dropdown-divider">
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="app-content">
                @yield('content')
            </main>
        </div>
    </div>

    @else
    {{-- Guest (login / register) — simple centered shell --}}
    <main class="py-4">
        @yield('content')
    </main>
    @endauth
</div>

<script>
    (function () {
        var sidebar  = document.getElementById('appSidebar');
        var toggle   = document.getElementById('sidebarToggle');
        var backdrop = document.getElementById('sidebarBackdrop');
        function open()  { sidebar && sidebar.classList.add('open');  backdrop && backdrop.classList.add('show'); }
        function close() { sidebar && sidebar.classList.remove('open'); backdrop && backdrop.classList.remove('show'); }
        toggle   && toggle.addEventListener('click', function () {
            sidebar.classList.contains('open') ? close() : open();
        });
        backdrop && backdrop.addEventListener('click', close);
    })();
</script>
</body>
</html>
