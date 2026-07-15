@extends('layouts.errors')

@section('content')
    <!-- Content -->

    <!-- Not Authorized -->
    <div class="container-xxl container-p-y">
      <div class="misc-wrapper">
        <h1 class="mb-2 mx-2" style="line-height: 6rem; font-size: 6rem">403</h1>
        <h4 class="mb-2 mx-2">You are not authorized! 🔐</h4>
        <p class="mb-6 mx-2">You do not have permission to access this page. Please return to the home page!</p>
        <a href="{{ url('/dashboard') }}" class="btn btn-primary">Back to Home</a>
        <div class="mt-12">
          <img
            src="{{ asset('assets/img/illustrations/page-misc-you-are-not-authorized.png') }}"
            alt="halaman-tidak-diizinkan"
            width="170"
            class="img-fluid" />
        </div>
      </div>
    </div>
    <div class="container-fluid misc-bg-wrapper">
      <img
        src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}"
        height="355"
        alt="halaman-tidak-diizinkan"
        data-app-light-img="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}"
        data-app-dark-img="{{ asset('assets/img/illustrations/bg-shape-image-dark.png') }}" />
    </div>
    <!-- /Not Authorized -->

    <!-- / Content -->
@endsection