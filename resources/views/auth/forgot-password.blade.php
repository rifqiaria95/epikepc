@extends('layouts.auth.app')
@section('title', 'Forgot Password')
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
            <img
              src="{{ url('assets/img/illustrations/auth-forgot-password-illustration-light.png') }}"
              alt="lupa-kata-sandi-cover"
              class="my-5 auth-illustration d-lg-block d-none"
              data-app-light-img="illustrations/auth-forgot-password-illustration-light.png"
              data-app-dark-img="illustrations/auth-forgot-password-illustration-dark.png" />

            <img
              src="{{ url('assets/img/illustrations/bg-shape-image-light.png') }}"
              alt="lupa-kata-sandi-cover"
              class="platform-bg"
              data-app-light-img="illustrations/bg-shape-image-light.png"
              data-app-dark-img="illustrations/bg-shape-image-dark.png" />
          </div>
        </div>
        <!-- /Left Text -->

        <!-- Forgot Password -->
        <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
          <div class="w-px-400 mx-auto mt-12 mt-5">
            <h4 class="mb-1">Lupa Kata Sandi? 🔒</h4>
            <p class="mb-6">Masukkan email Anda dan kami akan mengirimkan instruksi untuk mengatur ulang kata sandi Anda</p>
            
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-check-circle me-2"></i>
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form id="formAuthentication" class="mb-6" action="{{ route('password.email') }}" method="POST">
              @csrf
              <div class="mb-6">
                <label for="email" class="form-label">Email</label>
                <input
                  type="text"
                  class="form-control"
                  id="email"
                  name="email"
                  placeholder="Masukkan email Anda"
                  autofocus />
                @error('email')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
              </div>
              <button class="btn btn-primary d-grid w-100">Kirim Tautan Reset</button>
            </form>
            <div class="text-center">
              <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
                <i class="ti ti-chevron-left scaleX-n1-rtl me-1_5"></i>
                Kembali ke login
              </a>
            </div>
          </div>
        </div>
        <!-- /Forgot Password -->
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
