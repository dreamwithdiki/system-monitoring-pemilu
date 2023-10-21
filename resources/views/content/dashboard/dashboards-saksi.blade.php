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

@section('title', 'Dashboard - Timses')

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

<script>
  function updateLatestCaleg() {
      $.ajax({
          url: '/get-latest-caleg',
          type: 'GET',
          dataType: 'json',
          success: function(data) {
              if (data) {
                  var latestCalegCard = $('#latestCalegCard');

                  // Display current photo caleg
                  if (data.caleg_photo) {
                      var photoUrl = baseUrl + 'uploads/' + data.caleg_id;
                      latestCalegCard.find('.current-photo').attr('src', photoUrl);
                  } else {
                      latestCalegCard.find('.current-photo').attr('src', '#');
                      latestCalegCard.find('.current-photo').attr('alt', 'No Photo');
                  }

                  // Display current photo partai
                  if (data.caleg_photo_partai) {
                      var photoUrlPartai = baseUrl + 'uploads_partai/' + data.caleg_id;
                      latestCalegCard.find('.current-photo-partai').attr('src', photoUrlPartai);
                  } else {
                      latestCalegCard.find('.current-photo-partai').attr('src', '#');
                      latestCalegCard.find('.current-photo-partai').attr('alt', 'No Photo');
                  }

                  latestCalegCard.html(`
                    <div class="card-header flex-grow-0">
                      <div class="d-flex">
                        <div class="avatar flex-shrink-0 me-3">
                          <img src="${photoUrl}" alt="Caleg Photo" class="rounded-circle">
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-1">
                          <div class="me-2">
                            <h5 class="mb-0">${data.caleg_name}</h5>
                            <small class="text-muted">${data.caleg_nama_partai}</small>
                          </div>
                        </div>
                      </div>
                    </div>
                    <img class="img-fluid" src="${photoUrlPartai}" alt="Partai Photo" />
                    <div class="featured-date mt-n4 ms-4 bg-white rounded w-px-50 shadow text-center p-1">
                      <h5 class="mb-0 text-dark">{{ $total_caleg_is_active }}</h5>
                      <span class="text-primary">Caleg</span>
                    </div>
                  `);
              }
          }
      });
  }

  // Initially load the latest caleg data
  updateLatestCaleg();

  // Refresh the latest data every 30 seconds (adjust the interval as needed)
  setInterval(updateLatestCaleg, 30000);
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Dashboard
</h4>

<!-- Cards with few info -->
<div class="row">
  <div class="col-lg-8 col-md-6 col-sm-6 mb-4">
    <div class="card h-100">
      <div class="d-flex justify-content-between">
        <div class="col-sm-7">
          <div class="card-body">
            <h5 class="card-title text-primary">Hello {{ session('user_uniq_name') }}, {{ $result }}</h5>
            <p class="mb-4">Welcome back to <span class="fw-bold">Monitoring</span>.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card h-100" id="latestCalegCard">
        <!-- Content will be updated here -->
      </div>
  </div>

</div>
<!--/ Cards with few info -->

<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span>TAMANSARI</span>
            <div class="d-flex align-items-end mt-2">
              <h2 class="mb-0 me-2">{{ $total_dpt_tamansari }}</h2>
            </div>
            <small>Jumlah Dukungan Tamansari </small>
          </div>
          <span class="badge bg-label-primary rounded p-2">
            <i class="fa fa-location-dot bx-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span>CIBEREUM</span>
            <div class="d-flex align-items-end mt-2">
              <h2 class="mb-0 me-2">{{ $total_dpt_cibereum }}</h2>
            </div>
            <small>Jumlah Dukungan Cibereum</small>
          </div>
          <span class="badge bg-label-primary rounded p-2">
            <i class="fa fa-location-dot bx-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span>PURBARATU</span>
            <div class="d-flex align-items-end mt-2">
              <h2 class="mb-0 me-2">{{ $total_dpt_purbaratu }}</h2>
            </div>
            <small>Jumlah Dukungan Purbaratu</small>
          </div>
          <span class="badge bg-label-primary rounded p-2">
            <i class="fa fa-location-dot bx-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>

  <!-- Cards with unicons & charts -->
<div class="row">
  <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-4">
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
  </div>
  <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <span class="badge bg-label-primary rounded p-2">
              <i class="fa fa-mars bx-sm"></i>
            </span>
          </div>
          <span class="fw-semibold d-block mb-1">Jumlah Dukungan Laki-Laki</span>
          <h2 class="card-title mb-2">{{ $total_dpt_man }}</h2>
        </div>
      </div>
  </div>
  <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <span class="badge bg-label-success rounded p-2">
              <i class="fa fa-venus bx-sm"></i> 
            </span>
          </div>
          <span class="fw-semibold d-block mb-1">Jumlah Dukungan Perempuan</span>
          <h2 class="card-title mb-2">{{ $total_dpt_woman }}</h2>
        </div>
      </div>
  </div>
  <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-4">
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
  </div>
  <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 mb-4">
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
  </div>
</div>
<!--/ Cards with unicons & charts -->
@endsection
