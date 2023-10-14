@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Login')

@section('vendor-style')
<!-- Vendor -->
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
@endsection

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-auth.js')}}"></script>
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">
  <div class="authentication-inner row m-0">
    <!-- /Left Text -->
    <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center">
      <div class="w-100 d-flex justify-content-center">
        <img src="{{asset('assets/img/illustrations/login-cover.webp')}}" class="img-fluid" alt="Login image" width="700" >
      </div>
    </div>
    <!-- /Left Text -->

    <!-- Login -->
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
        <!-- Logo -->
        <div class="app-brand mb-4">
          <a href="{{url('/')}}" class="app-brand-link gap-2">
            <span>
              <img src="{{asset('assets/img/branding/LogoVA.webp')}}" class="img-fluid" alt="Logo">
            </span>
          </a>
        </div>
        @error('user_login')
          <div class="alert alert-danger alert-dismissible" role="alert">
            <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Info !!</h6>
            <p class="mb-0">{{ $message }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
          </div>
        @enderror
        @error('message')
          <div class="alert alert-primary alert-dismissible" role="alert">
            <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Info !!</h6>
            <p class="mb-0">{{ $message }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
          </div>
        @enderror

        <form id="formAuthentication" class="mb-3" action="{{ route('auth-do-login') }}" method="POST">
          @csrf
          <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control py-2" id="email" name="user_email" placeholder="Enter your email" autofocus>
            @error('user_email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
          </div>
          <div class="mb-4 form-password-toggle">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="password">Password</label>
            </div>
            <div class="input-group input-group-merge">
              <input type="password" id="password" class="form-control py-2" name="user_password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
              <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
              @error('user_password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
              @enderror
            </div>
            <div class="d-flex justify-content-end">
              <a href="{{url('auth/forgot-password-cover')}}">
                <small>Forgot Password?</small>
              </a>
            </div>
          </div>
          <button class="btn btn-primary d-grid w-100 py-2 mt-4 rounded">
            Sign in
          </button>
        </form>

      </div>
    </div>
    <!-- /Login -->
  </div>
</div>
@endsection
