@extends('layouts.auth.regist')
@section('css')
<link rel="stylesheet" href="{{ asset('/assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
<style>
    /* Custom styling untuk toastr di halaman external */
    .toast-success {
        background-color: #28a745 !important;
        color: white !important;
    }
    
    .toast-error {
        background-color: #dc3545 !important;
        color: white !important;
    }
    
    .toast-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }
    
    .toast-info {
        background-color: #17a2b8 !important;
        color: white !important;
    }
    
    .toast-top-right {
        top: 20px !important;
        right: 20px !important;
    }
    
    .toast-message {
        font-size: 14px !important;
        line-height: 1.4 !important;
    }
    
    .toast-title {
        font-weight: bold !important;
        font-size: 16px !important;
    }
    
    /* Animasi untuk toastr */
    .toast-enter {
        transform: translateX(100%);
        opacity: 0;
    }
    
    .toast-enter-active {
        transform: translateX(0);
        opacity: 1;
        transition: all 0.3s ease;
    }
    
    .toast-exit {
        transform: translateX(0);
        opacity: 1;
    }
    
    .toast-exit-active {
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    /* Animasi shake untuk validasi */
    @keyframes shakeX {
        0%, 100% {
            transform: translateX(0);
        }
        10%, 30%, 50%, 70%, 90% {
            transform: translateX(-5px);
        }
        20%, 40%, 60%, 80% {
            transform: translateX(5px);
        }
    }
    
    .animate__shakeX {
        animation: shakeX 0.6s ease-in-out;
    }
    
    /* Styling untuk form yang lebih baik */
    .form-control:focus,
    .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    /* Styling untuk button */
    .btn-primary:disabled {
        background-color: #6c757d;
        border-color: #6c757d;
        cursor: not-allowed;
    }
    
    .btn-success {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: white !important;
    }
    
    .btn-success:hover {
        background-color: #218838 !important;
        border-color: #1e7e34 !important;
    }
    
    .btn-success:focus {
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5) !important;
    }
    
    /* Loading indicator */
    .btn-loading {
        position: relative;
        color: transparent !important;
    }
    
    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
@endsection
@section('content')

<!-- Content -->

<div class="authentication-wrapper authentication-cover">
    <!-- Logo -->
    <a href="index.html" class="app-brand auth-cover-brand">
        <img src="{{ asset('assets/img/branding/logo.png') }}" alt="logo" class="img-fluid" width="100">
        <span class="app-brand-text demo text-heading fw-bold">Kainnova</span>
    </a>
    <!-- /Logo -->
    <div class="authentication-inner row m-0">
        <!-- /Left Text -->
        <div class="d-none d-lg-flex col-lg-8 p-0">
            <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                <img src="{{ asset('assets/img/illustrations/auth-register-illustration-light.png') }}" alt="auth-register-cover"
                    class="my-5 auth-illustration"
                    data-app-light-img="illustrations/auth-register-illustration-light.png"
                    data-app-dark-img="illustrations/auth-register-illustration-dark.png" />

                <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" alt="auth-register-cover"
                    class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png"
                    data-app-dark-img="illustrations/bg-shape-image-dark.png" />
            </div>
        </div>
        <!-- /Left Text -->

        <!-- Register -->
        <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
            <div class="w-px-400 mx-auto mt-12 pt-5">
                <h4 class="mb-1">Adventure starts here 🚀</h4>
                <p class="mb-6">Make your app management easy and fun!</p>

                <form id="formProgramRegist" class="form-horizontal" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-6">
                        <label class="form-label" for="program_id">Program</label>
                        <select id="program_id" class="select2 form-select" name="program_id">
                            <option selected disabled>Pilih Program</option>
                            @foreach ($program as $prg)
                                <option value="{{ $prg->id }}">{{ $prg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="alasan">Alasan</label>
                        <input
                            type="text"
                            id="alasan"
                            class="form-control"
                            placeholder="Alasan"
                            aria-label="Alasan"
                            name="alasan"
                        />
                    </div>
                    <button class="btn btn-primary d-grid w-100 mb-6" id="btn-simpan" value="create" type="submit">Daftar</button>
                </form>
            </div>
        </div>
        <!-- /Register -->
    </div>
</div>

<!-- / Content -->
@endsection

@section('script')
<script>
    // Check selected custom option
    window.Helpers.initCustomOptionCheck();
</script>
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
<script src="{{ asset('assets/ajax/program_regist.js') }}"></script>
@endsection
