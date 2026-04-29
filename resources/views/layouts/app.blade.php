<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/home') }}">หน้าแรก</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    บริการพนักงาน (ESS)
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ url('/attendance') }}">ลงเวลาเข้า-ออกงาน</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/leaves') }}">ระบบลางาน</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/ot-requests') }}">ขอทำล่วงเวลา (OT)</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/my-schedule') }}">ตารางงานของฉัน</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/my-payslips') }}">สลิปเงินเดือน</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ url('/organization-chart') }}">แผนผังองค์กร</a></li>
                                </ul>
                            </li>

                            @can('is-manager')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle fw-bold text-primary" href="#" role="button" data-bs-toggle="dropdown">
                                    สำหรับหัวหน้างาน
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ url('/leave-approvals') }}">✓ อนุมัติการลา</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/attendance-approvals') }}">✓ อนุมัติคำร้องขอแก้ไขเวลา</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/ot-approvals') }}">✓ อนุมัติ OT</a></li>
                                </ul>
                            </li>
                            @endcan

                            @can('is-hr')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle fw-bold text-success" href="#" role="button" data-bs-toggle="dropdown">
                                    จัดการระบบ (HR)
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ url('/employees') }}">พนักงานทั้งหมด</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ url('/shifts') }}">กะการทำงาน</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/shift-assignments') }}">จัดตารางงานพนักงาน</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ url('/salaries') }}">ฐานเงินเดือน</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/payrolls') }}">รันเงินเดือน (Payroll)</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ url('/attendance-report') }}">รายงานการลงเวลา</a></li>
                                </ul>
                            </li>
                            @endcan

                            @can('is-tenant-admin')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle fw-bold text-warning" href="#" role="button" data-bs-toggle="dropdown">
                                    ตั้งค่าองค์กร
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ url('/departments') }}">แผนก (Departments)</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/positions') }}">ตำแหน่ง (Positions)</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ url('/holidays') }}">วันหยุดบริษัท</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/admin/ot-settings') }}">ตั้งค่ากฎ OT</a></li>
                                </ul>
                            </li>
                            @endcan

                            @can('is-super-admin')
                            <li class="nav-item">
                                <a class="nav-link fw-bold text-danger" href="{{ url('/companies') }}">👑 จัดการบริษัทลูกค้า</a>
                            </li>
                            @endcan
                        @endauth
                    </ul>
                    
                    <ul class="navbar-nav ms-auto">
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