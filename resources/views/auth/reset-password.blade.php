@extends('layouts.auth.app')
@section('title', 'Reset Password')
@section('content')
<div class="authentication-wrapper authentication-cover">
    <!-- Logo -->
    <a href="{{ route('login') }}" class="app-brand auth-cover-brand">
        <span class="app-brand-logo demo">
            <img src="{{ url('assets/img/branding/logo.png') }}" alt="Logo" width="40">
        </span>
        <span class="app-brand-text demo menu-text fw-bold">Kainnova</span>
    </a>
    <!-- /Logo -->
    <div class="authentication-inner row m-0">
        <!-- /Left Text -->
        <div class="d-none d-lg-flex col-lg-8 p-0">
            <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                <img src="{{ url('assets/img/illustrations/auth-reset-password-illustration-light.png') }}"
                    alt="reset-kata-sandi-cover" class="my-5 auth-illustration"
                    data-app-light-img="illustrations/auth-reset-password-illustration-light.png"
                    data-app-dark-img="illustrations/auth-reset-password-illustration-dark.png" />

                <img src="{{ url('assets/img/illustrations/bg-shape-image-light.png') }}"
                    alt="reset-kata-sandi-cover" class="platform-bg"
                    data-app-light-img="illustrations/bg-shape-image-light.png"
                    data-app-dark-img="illustrations/bg-shape-image-dark.png" />
            </div>
        </div>
        <!-- /Left Text -->

        <!-- Reset Password -->
        <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-6 p-sm-12">
            <div class="w-px-400 mx-auto mt-12 pt-5">
                <h4 class="mb-1">Reset Kata Sandi 🔒</h4>
                <p class="mb-6">
                    <span class="fw-medium">Kata sandi baru Anda harus berbeda dari kata sandi yang pernah digunakan</span>
                </p>
                            
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-check-circle me-2"></i>
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form id="formAuthentication" class="mb-6" action="{{ route('password.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <input type="hidden" name="email" value="{{ $request->email }}">
                    @error('email')
                        <div class="text-danger mb-3">{{ $message }}</div>
                    @enderror
                    @error('token')
                        <div class="text-danger mb-3">{{ $message }}</div>
                    @enderror
                    <div class="mb-6 form-password-toggle">
                        <label class="form-label" for="password">Kata Sandi Baru</label>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password" class="form-control" name="password"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password" />
                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            @error('password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-6 form-password-toggle">
                        <label class="form-label" for="confirm-password">Konfirmasi Kata Sandi</label>
                        <div class="input-group input-group-merge">
                            <input type="password" id="confirm-password" class="form-control" name="password_confirmation"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password" />
                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            @error('password_confirmation')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <button class="btn btn-primary d-grid w-100 mb-6">Atur Ulang Kata Sandi</button>
                    <div class="text-center">
                        <a href="{{ route('login') }}">
                            <i class="ti ti-chevron-left scaleX-n1-rtl me-1_5"></i>
                            Kembali ke login
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <!-- /Reset Password -->
    </div>
</div>

@if (session('status'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        toastr.success('{{ session('status') }}', 'Berhasil!', {
            closeButton: true,
            progressBar: true,
            timeOut: 5000
        });
    });
</script>
@endif

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach ($errors->all() as $error)
            toastr.error('{{ $error }}', 'Error!', {
                closeButton: true,
                progressBar: true,
                timeOut: 5000
            });
        @endforeach
    });
</script>
@endif
@endsection
