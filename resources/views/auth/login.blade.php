@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            
            <div class="text-center mb-4">
                <h1 class="display-5 fw-bold text-primary">RotiSoft</h1>
                <p class="lead text-muted">ระบบบริหารงานบุคคล (HRM) <br>ที่ครบวงจรและทันสมัยสำหรับองค์กรของคุณ</p>
            </div>

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-white text-center py-3 border-0 mt-2">
                    <h5 class="mb-0 fw-bold">เข้าสู่ระบบ (Login)</h5>
                </div>

                <div class="card-body p-4 pt-2">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">อีเมล (Email Address)</label>
                            <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="name@example.com">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">รหัสผ่าน (Password)</label>
                            <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                จดจำการเข้าระบบ
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-3">
                                🚀 เข้าสู่ระบบ
                            </button>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a class="btn btn-link text-decoration-none text-muted" href="{{ route('password.request') }}">
                                    ลืมรหัสผ่านใช่หรือไม่?
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4 text-muted small">
                &copy; {{ date('Y') }} RotiSoft HRMS. All rights reserved.
            </div>

        </div>
    </div>
</div>
@endsection