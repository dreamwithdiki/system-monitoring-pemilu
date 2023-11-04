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
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <img src="${photoUrlPartai}" height="140" width="100%" alt="Caleg Photo">
                          </div>
                          <span>Nama Partai : ${data.caleg_nama_partai}</span>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <img src="${photoUrl}" height="140" width="100%" alt="Partai Photo">
                          </div>
                          <span>Nama Caleg : ${data.caleg_name}</span>
                        </div>
                      </div>
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

<!-- Bar chart -->
<script>
  var data = @json($data_chart_bar_tps); // Menyediakan data dari controller

  var ctx = document.getElementById('barChartKecamatan').getContext('2d');
  var myChart;

  function updateChart(district) {
      var filteredData = data.filter(function(item) {
          return district === '' || item.district.name === district;
      });

      if (myChart) {
          myChart.destroy();
      }

      myChart = new Chart(ctx, {
          type: 'bar',
          data: {
              labels: filteredData.map(function(item) {
                  return item.village.name;
              }),
              datasets: [{
                  label: 'Nilai Suara',
                  data: filteredData.map(function(item) {
                      return item.tps_suara_caleg;
                  }),
                  backgroundColor: 'rgba(112, 173, 71, 255)',
                  borderColor: 'rgba(75, 192, 192, 1',
                  borderWidth: 1
              }]
          },
          options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
          }
      });
  }

  document.getElementById('district').addEventListener('change', function() {
      var selectedDistrict = this.value;
      updateChart(selectedDistrict);
  });

  // Memuat grafik awal dengan semua data
  updateChart('');
</script>

<script>
  const doughnutChart = document.getElementById('doughnutChartDPT');

  let borderColor, axisColor;

  if (isDarkStyle) {
    borderColor = '#444564'; //theme border color
    tickColor = '#a3a4cc'; // x & y axis tick color
  } else {
    borderColor = '#eceef1'; // $gray-100 for light
    tickColor = '#697a8d'; // x & y axis tick color\
  }

  var totalDptMan = @json($total_dpt_man);
  var totalDptWoman = @json($total_dpt_woman);

  if (doughnutChart) {
    const doughnutChartVar = new Chart(doughnutChart, {
      type: 'doughnut',
      data: {
        labels: ['Laki-Laki', 'Perempuan'],
        datasets: [
          {
            data: [totalDptMan, totalDptWoman],
            backgroundColor: ['#696cff', '#71dd37'],
            borderWidth: 0,
            pointStyle: 'rectRounded'
          }
        ]
      },
      options: {
        responsive: true,
        animation: {
          duration: 500
        },
        cutout: '80%',
        plugins: {
          legend: {
            display: true,
            // position: 'left',
          },
          tooltip: {
            enabled: true,
            callbacks: {
              label: function (context) {
                const label = context.label || '';
                const value = context.parsed;
                return label + ': ' + value + ' Orang';
              }
            },
            // Updated default tooltip UI
            // rtl: isRtl,
            // backgroundColor: config.colors.white,
            // titleColor: config.colors.black,
            // bodyColor: config.colors.black,
            // borderWidth: 1,
            // borderColor: borderColor
          },
        }
      }
    });
  }
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
            <p class="mb-4">Welcome back to <span class="fw-bold">SIM PACALEG</span>.</p>
            <span>"Sistem Monitoring Pemenangan Calon Legislatif"</span>
          </div>
        </div>
        <div class="col-sm-5 text-center text-sm-left">
          <div class="card-body pb-0 px-0 px-md-4">
            <img src="{{asset('assets/img/illustrations/man-with-laptop-'.$configData['style'].'.png')}}" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-4 order-1">
    <a href="{{ route('pemilu-master-data-caleg') }}">
      <div class="row" id="latestCalegCard">
         <!-- Content will be updated here -->
      </div>
    </a>
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
            <small>Jumlah Suara </small>
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
            <small>Jumlah Suara</small>
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
            <small>Jumlah Suara</small>
          </div>
          <span class="badge bg-label-primary rounded p-2">
            <i class="fa fa-location-dot bx-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
   <div class="col-12 col-md-12 col-lg-8">
    <div class="row">
      <div class="col-sm-3 mb-4">
        <a href="{{ route('dpt') }}">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <span class="badge bg-label-primary rounded p-2">
                  <i class="fa fa-user-plus bx-sm"></i>
                </span>
              </div>
              <span class="fw-semibold d-block mb-1">Total Dukungan</span>
              <h2 class="card-title mb-2">{{ $total_dpt_is_active }}</h2>
            </div>
          </div>
        </a>
      </div>

      <div class="col-sm-3 mb-4">
        <a href="{{ route('dpt') }}">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <span class="badge bg-label-warning rounded p-2">
                  <i class="fa fa-user-xmark bx-sm"></i>
                </span>
              </div>
              <span class="fw-semibold d-block mb-1">Data Potensial</span>
              <h2 class="card-title mb-2">{{ $total_dpt_is_deactive }}</h2>
            </div>
          </div>
        </a>
      </div>

      <div class="col-sm-3 mb-4">
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

      <div class="col-sm-3 mb-4">
        <a href="{{ route('pemilu-master-data-tps') }}">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <span class="badge bg-label-primary rounded p-2">
                  <i class="fa fa-envelope bx-sm"></i> 
                </span>
              </div>
              <span class="fw-semibold d-block mb-1">Total Suara Caleg</span>
              <h2 class="card-title mb-2">{{ $totalSuaraCaleg }}</h2>
            </div>
          </div>
        </a>
      </div>
      

      <div class="col-sm-4 mb-4">
        <a href="{{ route('pemilu-master-data-user') }}">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <span class="badge bg-label-danger rounded p-2">
                  <i class="fa fa-users bx-sm"></i>
                </span>
              </div>
              <span class="fw-semibold d-block mb-1">Total Users</span>
              <h2 class="card-title mb-2">{{ $total_users_is_active }}</h2>
            </div>
          </div>
        </a>
      </div>

      <div class="col-sm-4 mb-4">
        <a href="{{ route('dpt') }}">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <span class="badge bg-label-primary rounded p-2">
                  <i class="fa fa-person bx-sm"></i>
                </span>
              </div>
              <span class="fw-semibold d-block mb-1">Jumlah Dukungan Laki-Laki</span>
              <h2 class="card-title mb-2">{{ $total_dpt_man }}</h2>
            </div>
          </div>
        </a>
      </div>

      <div class="col-sm-4 mb-4">
        <a href="{{ route('dpt') }}">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <span class="badge bg-label-success rounded p-2">
                  <i class="fa fa-person-dress bx-sm"></i> 
                </span>
              </div>
              <span class="fw-semibold d-block mb-1">Jumlah Dukungan Perempuan</span>
              <h2 class="card-title mb-2">{{ $total_dpt_woman }}</h2>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-10">
            <canvas id="doughnutChartDPT"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="row mt-3">
  <div class="col-xl-12 col-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
            <div class="col-xl-6 mx-auto">
                <label for="district" class="form-label">Pilih Kecamatan:</label>
                <select id="district" class="form-select">
                    <option value="">Semua Kecamatan</option>
                    @foreach ($data_districts as $district)
                        <option value="{{ $district }}">{{ $district }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <canvas id="barChartKecamatan" width="400" height="200"></canvas>
      </div>
    </div>
  </div>
</div>

@endsection
