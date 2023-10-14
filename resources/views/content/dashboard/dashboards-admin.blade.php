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
  var monthChoice = 0;
  var yearChoice = 0;
  // Konfigurasi untuk grafik
  let cardColor, headingColor, axisColor, shadeColor, borderColor;

  if (isDarkStyle) {
    cardColor = config.colors_dark.cardColor;
    headingColor = config.colors_dark.headingColor;
    axisColor = config.colors_dark.axisColor;
    borderColor = config.colors_dark.borderColor;
  } else {
    cardColor = config.colors.white;
    headingColor = config.colors.headingColor;
    axisColor = config.colors.axisColor;
    borderColor = config.colors.borderColor;
  }

  // data dari controller akan disimpan dlm variabel JavaScript
  var data = @json($total_visit_order_month_chart)

  // Proses data utk Chart.js
  var labels = [];
  var values = [];

  // Inisialisasi nilai nol utk setiap bulan
  for (let i = 0; i < 12; i++) {
    const monthName = moment().month(i).format('MMMM');
    const year = moment().year();
    labels.push(`${monthName} - ${year}`);
    values.push(0);
  }

  // Isi data yang diperoleh dari database
  data.forEach(item => {
    const index = labels.indexOf(item.month);
    if (index !== -1) {
      values[index] = item.total;
    }
  });

  const barChartTotal = document.getElementById('barChartTotal');
  barChartTotal.height = 400;
  const barChartVar = new Chart(barChartTotal, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Total Visit Order',
        data: values,
        backgroundColor: 'rgba(75, 192, 192,0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        title: {
          display: true,
          text: 'Total Visit Order per Month ' + moment().year()
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    },
  });

  // Order Statistics Chart
  // --------------------------------------------------------------------
  var list_order_each_status = @json($list_order_each_status);
  const chartOrderStatistics = document.querySelector('#orderStatisticsChart'),
  orderChartConfig = {
    chart: {
      height: 165,
      width: 130,
      type: 'donut'
    },
    labels: ['Open', 'Assigned', 'Cancel', 'Revisit', 'Visited', 'Validated', 'Can\'t billed', 'Paid to Client', 'Paid to Partner'],
    series: list_order_each_status,
    colors: [config.colors.secondary, config.colors.warning, config.colors.danger, config.colors.info, config.colors.primary, config.colors.success, config.colors.danger, config.colors.success, config.colors.primary],
    stroke: {
      width: 5,
      colors: [cardColor]
    },
    dataLabels: {
      enabled: false,
      formatter: function(val, opt) {
        return parseInt(val) + '%';
      }
    },
    legend: {
      show: false
    },
    grid: {
      padding: {
        top: 0,
        bottom: 0,
        right: 15
      }
    },
    plotOptions: {
      pie: {
        donut: {
          size: '75%',
          labels: {
            show: true,
            value: {
              fontSize: '1.5rem',
              fontFamily: 'Public Sans',
              color: headingColor,
              offsetY: -15,
              formatter: function(val) {
                return parseInt(val);
              }
            },
            name: {
              fontSize: '0.7rem',
              offsetY: 20,
              fontFamily: 'Public Sans'
            },
            total: {
              show: false,
              fontSize: '0.8125rem',
              color: axisColor,
              label: 'Weekly',
              formatter: function(w) {
                return '38%';
              }
            }
          }
        }
      }
    }
  };
  const statisticsChart = new ApexCharts(chartOrderStatistics, orderChartConfig);
  statisticsChart.render();

  // Earning Reports Bar Chart
  // --------------------------------------------------------------------
  const reportBarChartEl = document.querySelector('#reportBarChart'),
    reportBarChartConfig = {
      chart: {
        height: 180,
        type: 'bar',
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          barHeight: '60%',
          columnWidth: '50%',
          startingShape: 'rounded',
          endingShape: 'rounded',
          borderRadius: 4,
          distributed: true
        }
      },
      grid: {
        show: false,
        padding: {
          top: -35,
          bottom: -10,
          left: -10,
          right: -10
        }
      },
      colors: [
        config.colors.secondary, 
        config.colors.warning, 
        config.colors.danger, 
        config.colors.info, 
        config.colors.primary, 
        config.colors.success, 
        config.colors.danger, 
        config.colors.success, 
        config.colors.primary
      ],
      dataLabels: {
        enabled: false
      },
      series: [
        {
          data: list_order_each_status
        }
      ],
      legend: {
        show: false
      },
      xaxis: {
        categories: ['Open', 'Assign', 'Cancel', 'Revisit', 'Visited', 'Validated', ['Can\'t', 'billed'], ['Paid to', 'client'], ['Paid to', 'partner']],
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          rotate: -20,
          rotateAlways: true,
          style: {
            colors: config.colors.secondary,
            fontSize: '13px'
          }
        },
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };

  const barChart = new ApexCharts(reportBarChartEl, reportBarChartConfig);
  barChart.render();

  $(document).on('click', '#dropdown-item-month', function () {
    monthChoice = $(this).data('id');
    var month = $(this).text();
    $('#dropdown-month').text("Loading");
    $.ajax({
      type: 'GET',
      url: baseUrl + 'filter-year-month/' + yearChoice + '/' + monthChoice,
      success: function success(response) {
        $('#dropdown-month').text(month);
        $('#total_order').text(response.total_visit_order);
        for (let index = 0; index < response.list_order_each_status.length; index++) {
          $('#total_order_status_' + index).text(response.list_order_each_status[index]);
        }
        statisticsChart.updateSeries(response.list_order_each_status);
        barChart.updateSeries([{
          data: response.list_order_each_status
        }]);
      },
      error: function error(_error) {
        Swal.fire({
          title: 'Error!',
          text: "Internal Server Error",
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-primary'
          }
        });
      }
    });
  });

  $(document).on('click', '#dropdown-item-year', function () {
    yearChoice = $(this).data('id');
    var year = $(this).text();
    $('#dropdown-year').text("Loading");
    $.ajax({
      type: 'GET',
      url: baseUrl + 'filter-year-month/' + yearChoice + '/' + monthChoice,
      success: function success(response) {
        var newLabel = [];
        var newValue = [];
        $('#dropdown-year').text(year);
        $('#total_order').text(response.total_visit_order);
        for (let index = 0; index < response.list_order_each_status.length; index++) {
          $('#total_order_status_' + index).text(response.list_order_each_status[index]);
        }
        statisticsChart.updateSeries(response.list_order_each_status);
        barChart.updateSeries([{
          data: response.list_order_each_status
        }]);
        for (let i = 0; i < 12; i++) {
          const monthName = moment().month(i).format('MMMM');
          const year = (yearChoice == 0) ? moment().year() : yearChoice;
          newLabel.push(`${monthName} - ${year}`);
          newValue.push(0);
        }

        // Isi data yang diperoleh dari database
        response.total_visit_order_month_chart.forEach(item => {
          const index = labels.indexOf(item.month);
          if (index !== -1) {
            newValue[index] = item.total;
          }
        });
        barChartVar.data.labels = newLabel;
        barChartVar.data.datasets[0].data = newValue;
        barChartVar.options.plugins.title.text = 'Total Visit Order per Month ' + ((yearChoice == 0) ? moment().year() : yearChoice);
        barChartVar.update();
      },
      error: function error(_error) {
        Swal.fire({
          title: 'Error!',
          text: "Internal Server Error",
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-primary'
          }
        });
      }
    });
  });
</script>
@endsection

@section('content')
<div class="row">

  <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <ul class="p-0 m-0">
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-secondary"><i class='bx bxs-lock-open'></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Open</h6>
              </div>
              <div class="user-progress">
                <h5 class="fw-bold" id="total_order_status_0">{{ $list_order_each_status[0] }}</h5>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-warning"><i class='bx bx-cart-download'></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Assigned</h6>
              </div>
              <div class="user-progress">
                <h5 class="fw-bold" id="total_order_status_1">{{ $list_order_each_status[1] }}</h5>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-danger"><i class='bx bxs-checkbox-minus'></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Cancelled</h6>
              </div>
              <div class="user-progress">
                <h5 class="fw-bold" id="total_order_status_2">{{ $list_order_each_status[2] }}</h5>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <ul class="p-0 m-0">
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-info"><i class='bx bx-revision'></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Revisit</h6>
              </div>
              <div class="user-progress">
                <h5 class="fw-bold" id="total_order_status_3">{{ $list_order_each_status[3] }}</h5>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-primary"><i class='bx bxs-map'></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Visited</h6>
              </div>
              <div class="user-progress">
                <h5 class="fw-bold" id="total_order_status_4">{{ $list_order_each_status[4] }}</h5>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-success"><i class='bx bxs-check-square'></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Validated</h6>
              </div>
              <div class="user-progress">
                <h5 class="fw-bold" id="total_order_status_5">{{ $list_order_each_status[5] }}</h5>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <ul class="p-0 m-0">
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-danger"><i class='bx bx-coin'></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Can't billed</h6>
              </div>
              <div class="user-progress">
                <h5 class="fw-bold" id="total_order_status_6">{{ $list_order_each_status[6] }}</h5>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-success"><i class='bx bx-credit-card'></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Paid to client</h6>
              </div>
              <div class="user-progress">
                <h5 class="fw-bold" id="total_order_status_7">{{ $list_order_each_status[7] }}</h5>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-primary"><i class='bx bxs-dollar-circle'></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Paid to Partner</h6>
              </div>
              <div class="user-progress">
                <h5 class="fw-bold" id="total_order_status_8">{{ $list_order_each_status[8] }}</h5>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="col-md-12 col-lg-12 col-xl-12 order-0 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row">
          <div class="col-md-2 d-flex align-items-center justify-content-center" style="text-align: center">
            <div class="">
              <h2 class="mb-2" id="total_order">{{ $total_visit_order }}</h2>
              <span>Total Orders</span>
            </div>
          </div>
          <div class="col-md-8">
            <div id="reportBarChart"></div>
          </div>
          <div class="col-md-2">
            <div class="d-flex flex-column align-items-center gap-1">
              <div class="row">
                <div class="text-center col-md-6 col-sm-6">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-label-secondary dropdown-toggle" type="button" id="dropdown-year" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      All
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="growthReportId">
                      <a class="dropdown-item" id="dropdown-item-year" data-id="0" href="javascript:void(0);">All</a>
                      @foreach ($list_year as $year)
                        <a class="dropdown-item" id="dropdown-item-year" data-id="{{ $year }}" href="javascript:void(0);">{{ $year }}</a>
                      @endforeach
                    </div>
                  </div>
                </div>
                <div class="text-center col-md-6 col-sm-6">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-label-secondary dropdown-toggle" type="button" id="dropdown-month" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      All
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="growthReportId">
                      <a class="dropdown-item" id="dropdown-item-month" data-id="0" href="javascript:void(0);">All</a>
                      @foreach ($list_month as $key => $month)
                        <a class="dropdown-item" id="dropdown-item-month" data-id="{{ $key + 1 }}" href="javascript:void(0);">{{ $month }}</a>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
              <div id="orderStatisticsChart"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-12 col-12">
    <div class="card">
      <div class="card-body">
        <canvas id="barChartTotal" class="chartjs"></canvas>
      </div>
    </div>
  </div>
  <!-- /Bar Charts -->
</div>
@endsection
