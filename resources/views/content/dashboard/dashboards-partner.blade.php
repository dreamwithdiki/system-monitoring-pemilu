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
    $result = "Selamat Sore 😔😔😔";
}
else if($hour > 18 && $hour <= 23) {
    $result = "Selamat Malam 😴😴😴";
}
else {
    $result = "Kenapa belum tidur ? ini sudah larut malam";
}
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Dashboard - Partner')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/cards-statistics.js')}}"></script>
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
            <p class="mb-4">Welcome back to <span class="fw-bold">VisitApp</span>.</p>
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

<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span>Visit Order (Assign)</span>
            <div class="d-flex align-items-end mt-2">
              <h4 class="mb-0 me-2">{{ $total_visit_order_assign }}</h4>
            </div>
            <small>Total Visit Order Status Assign </small>
          </div>
          <span class="badge bg-label-primary rounded p-2">
            <i class="fa fa-location-dot bx-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span>Visit Order (Visited)</span>
            <div class="d-flex align-items-end mt-2">
              <h4 class="mb-0 me-2">{{ $total_visit_order_visited }}</h4>
            </div>
            <small>Total Visit Order Status Visited</small>
          </div>
          <span class="badge bg-label-success rounded p-2">
            <i class="fa fa-location-dot bx-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
