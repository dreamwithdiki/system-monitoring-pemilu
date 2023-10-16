@php
$configData = Helper::appClasses();

date_default_timezone_set("Asia/Jakarta");  

$hour = date('H', time());

if( $hour > 5 && $hour <= 11) {
    $result = "Selamat Pagi 😃😃😃";
}
else if($hour > 11 && $hour <= 15) {
    $result = "Selamat Siang 🥰🥰🥰";
}
else if($hour > 15 && $hour <= 18) {
    $result = "Selamat Sore 😄😄😄";
}
else if($hour > 18 && $hour <= 23) {
    $result = "Selamat Malam 😴😴😴";
}
else {
    $result = "Kenapa belum tidur ? ini sudah larut malam";
}
@endphp


@extends('layouts/layoutMaster')

@section('title', 'Dashboard - Admin')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script src="{{asset('assets/vendor/libs/chartjs/chartjs.js')}}"></script>
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
@endsection

@section('page-script')

@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Dashboard
</h4>

<!-- Cards with few info -->
<div class="row">
  <div class="col-lg-12 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="d-flex justify-content-between">
        <div class="col-sm-7">
          <div class="card-body">
            <h5 class="card-title text-primary">Hello {{ session('user_uniq_name') }}, {{ $result }}</h5>
            <p class="mb-4">Welcome back to <span class="fw-bold">Monitoring</span>.</p>
          </div>
        </div>
        <div class="col-sm-5 text-center text-sm-left">
          <div class="card-body pb-0 px-0 px-md-4">
            <img src="{{asset('assets/img/illustrations/man-with-laptop-'.$configData['style'].'.png')}}" height="140" alt="View Badge User" data-app-light-img="illustrations/man-with-laptop-light.png" data-app-dark-img="illustrations/man-with-laptop-dark.png">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Cards with few info -->

  <!-- Cards with unicons & charts -->
<div class="row">
  <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-4">
    <a href="{{ route('dpt') }}">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <span class="badge bg-label-primary rounded p-2">
              <i class="fa fa-headset bx-sm"></i>
            </span>
          </div>
          <span class="fw-semibold d-block mb-1">Total Dukungan</span>
          <h2 class="card-title mb-2">{{ $total_dpt_is_active }}</h2>
        </div>
      </div>
    </a>
  </div>
  <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-4">
    <a href="{{ route('dpt') }}">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <span class="badge bg-label-primary rounded p-2">
              <i class="fa fa-mars bx-sm"></i>
            </span>
          </div>
          <span class="fw-semibold d-block mb-1">Total Laki-Laki</span>
          <h2 class="card-title mb-2">{{ $total_dpt_man }}</h2>
        </div>
      </div>
    </a>
  </div>
  <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-4">
    <a href="{{ route('dpt') }}">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <span class="badge bg-label-success rounded p-2">
              <i class="fa fa-venus bx-sm"></i> 
            </span>
          </div>
          <span class="fw-semibold d-block mb-1">Total Perempuan</span>
          <h2 class="card-title mb-2">{{ $total_dpt_woman }}</h2>
        </div>
      </div>
    </a>
  </div>
  <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-4">
    <a href="{{ route('pemilu-master-data-tps') }}">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <span class="badge bg-label-info rounded p-2">
              <i class="fa fa-gift bx-sm"></i> 
            </span>
          </div>
          <span class="fw-semibold d-block mb-1">Total Suara Partai</span>
          <h2 class="card-title mb-2">{{ $totalSuaraPartai }}</h2>
        </div>
      </div>
    </a>
  </div>
  <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-4">
    <a href="{{ route('pemilu-master-data-tps') }}">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <span class="badge bg-label-primary rounded p-2">
              <i class="fa fa-envelope bx-sm"></i> 
            </span>
          </div>
          <span class="fw-semibold d-block mb-1">Total Jumlah Pemilih</span>
          <h2 class="card-title mb-2">{{ $totalSuaraCaleg }}</h2>
        </div>
      </div>
    </a>
  </div>
  <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-4">
    <a href="{{ route('pemilu-master-data-user') }}">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <span class="badge bg-label-warning rounded p-2">
              <i class="fa fa-users bx-sm"></i>
            </span>
          </div>
          <span class="fw-semibold d-block mb-1">Total Users</span>
          <h2 class="card-title mb-2">{{ $total_users_is_active }}</h2>
        </div>
      </div>
    </a>
  </div>
</div>
<!--/ Cards with unicons & charts -->
@endsection
