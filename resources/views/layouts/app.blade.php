<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                        <!--
                        <li class="nav-item">
                            <a class="nav-link fw-bold text-primary" href="/home">📊 แดชบอร์ด</a>
                        </li>-->

                        @can('access-admin')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-gear-fill"></i> ตั้งค่าระบบ
                            </a>
                            <ul class="dropdown-menu">
                                
                                @can('edit-employees')
                                <li>
                                    <a class="dropdown-item" href="{{ route('ot-settings.index') }}">
                                        <i class="bi bi-clock-history me-2"></i> ตั้งค่า OT
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->is('holidays*') ? 'active' : '' }}" href="{{ route('holidays.index') }}">
                                        <i class="bi bi-calendar-check me-2"></i> วันหยุดบริษัท
                                    </a>
                                </li>
                                @endcan
                                
                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <a class="dropdown-item" href="/companies">
                                        <i class="bi bi-building me-2"></i> จัดการบริษัท
                                    </a>
                                </li>
                                
                                <li>
                                    <a class="dropdown-item" href="/departments">
                                        <i class="bi bi-building me-2"></i> จัดการแผนก
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/positions">
                                        <i class="bi bi-building me-2"></i> จัดการตำแหน่ง
                                    </a>
                                </li>
                                @can('edit-employees')
                                @endcan

                            </ul>
                        </li>
                    @endcan

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-bold text-success" href="#" role="button" data-bs-toggle="dropdown">
                                🧑‍💻 ระบบบริการตนเอง
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0">
                                <li><h6 class="dropdown-header text-primary fw-bold">พนักงาน (ESS)</h6></li>
                                <li><a class="dropdown-item" href="/my-schedule">📅 ปฏิทินตารางทำงาน (My Schedule)</a></li>
                                <li><a class="dropdown-item" href="/leaves">📝 ระบบการลา (My Leaves)</a></li>
                                <li><a class="dropdown-item" href="/attendance">⏱️ บันทึกเวลาเข้า-ออก</a></li>
                                <li><a class="dropdown-item fw-bold" href="/ot-requests">🕒 ขออนุมัติล่วงเวลา (OT Plan)</a></li>
                                <li><a class="dropdown-item fw-bold" href="/my-payslips">💰 สลิปเงินเดือน (My Payslips)</a></li>
                                <li><a class="dropdown-item text-muted" href="#">🎯 เป้าหมาย KPI ของฉัน (เร็วๆ นี้)</a></li>
                                <li><a class="dropdown-item text-muted" href="#">🎓 ประวัติการอบรม (เร็วๆ นี้)</a></li>
                                
                                @can('is-manager')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header text-danger fw-bold">หัวหน้างาน (MSS)</h6></li>
                                    <li><a class="dropdown-item fw-bold" href="/leave-approvals">✅ อนุมัติใบลาทีม (Approvals)</a></li>
                                    <li><a class="dropdown-item fw-bold" href="/attendance-approvals">⏱️ อนุมัติคำร้องเวลา (Timesheet)</a></li>
                                    <li><a class="dropdown-item fw-bold text-success" href="/ot-approvals">🕒 อนุมัติล่วงเวลาทีม (OT)</a></li>
                                    <li><a class="dropdown-item text-muted" href="#">✍️ ประเมินผลลูกน้อง (เร็วๆ นี้)</a></li>
                                    <li><a class="dropdown-item text-muted" href="#">📅 ตารางกะงานของทีม (เร็วๆ นี้)</a></li>
                                @endcan
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                1. ศูนย์กลางพนักงาน
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0">
                                @can('view-employees-menu')
                                    <li><a class="dropdown-item" href="/employees">👥 รายชื่อพนักงานทั้งหมด</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endcan
                                <li><a class="dropdown-item fw-bold" href="/organization-chart">🏢 โครงสร้างองค์กร (Org Chart)</a></li>
                                @can('edit-employees')
                                    <li><a class="dropdown-item text-muted" href="#">📂 ทะเบียนประวัติ/เอกสาร (เร็วๆ นี้)</a></li>
                                @endcan
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                2. เวลาและการลา
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0">
                                <li><a class="dropdown-item text-muted" href="#">📅 ปฏิทินวันหยุดบริษัท (เร็วๆ นี้)</a></li>
                                @can('view-all-employees')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-muted" href="#">⚙️ ตั้งค่าโควต้าการลา (เร็วๆ นี้)</a></li>
                                @endcan
                                @can('edit-employees')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item fw-bold" href="/attendance-report">📊 รายงานการลงเวลา (Report)</a></li>
                                    <li><a class="dropdown-item" href="/shifts">⚙️ จัดการกะการทำงาน (Shifts)</a></li>
                                    <li><a class="dropdown-item" href="/shift-assignments">🧑‍💼 มอบหมายกะพนักงาน</a></li>
                                    <li><a class="dropdown-item" href="{{ route('schedules.table') }}">📅 ตารางการทำงานแบบรายการ</a></li>
                                @endcan
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                3. เงินเดือน
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0">
                                <li><a class="dropdown-item text-muted" href="#">🧾 นโยบายภาษี/สวัสดิการ (เร็วๆ นี้)</a></li>
                                @can('edit-employees')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item fw-bold" href="/salaries">💰 ตั้งค่าฐานเงินเดือน (Setup)</a></li>
                                    <li><a class="dropdown-item fw-bold text-danger" href="/payrolls">⚡ ประมวลผลเงินเดือน (Run)</a></li>
                                @endcan
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                4. สรรหาบุคลากร
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0">
                                <li><a class="dropdown-item text-muted" href="#">📢 ตำแหน่งที่เปิดรับทั้งหมด (เร็วๆ นี้)</a></li>
                                @can('edit-employees')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-muted" href="#">📝 จัดการประกาศงาน (เร็วๆ นี้)</a></li>
                                    <li><a class="dropdown-item text-muted" href="#">🧑‍💼 ระบบคัดกรองผู้สมัคร (เร็วๆ นี้)</a></li>
                                @endcan
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                5. ประเมินผล
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0">
                                @can('edit-employees')
                                    <li><a class="dropdown-item text-muted" href="#">🎯 จัดการแบบฟอร์มประเมิน (เร็วๆ นี้)</a></li>
                                    <li><a class="dropdown-item text-muted" href="#">📈 สรุปผลการประเมินองค์กร (เร็วๆ นี้)</a></li>
                                @else
                                    <li><a class="dropdown-item text-muted" href="#">ℹ️ นโยบายการประเมินผล (เร็วๆ นี้)</a></li>
                                @endcan
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                6. ฝึกอบรม
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0">
                                <li><a class="dropdown-item text-muted" href="#">📚 แคตตาล็อกหลักสูตรบริษัท (เร็วๆ นี้)</a></li>
                                @can('edit-employees')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-muted" href="#">🛠️ จัดการคอร์สเรียน (เร็วๆ นี้)</a></li>
                                    <li><a class="dropdown-item text-muted" href="#">📊 รายงานผลการอบรม (เร็วๆ นี้)</a></li>
                                @endcan
                            </ul>
                        </li>
                    </ul>
                    
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="/profile">
                                        👤 ข้อมูลส่วนตัว (My Profile)
                                    </a>

                                    <hr class="dropdown-divider">

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
