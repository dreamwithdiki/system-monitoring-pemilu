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
                <h5 class="fw-bold" id="total_order_status_0">1</h5>
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
                <h5 class="fw-bold" id="total_order_status_1">2</h5>
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
                <h5 class="fw-bold" id="total_order_status_2">3</h5>
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
                <h5 class="fw-bold" id="total_order_status_3">5</h5>
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
                <h5 class="fw-bold" id="total_order_status_4">6</h5>
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
                <h5 class="fw-bold" id="total_order_status_5">7</h5>
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
                <h5 class="fw-bold" id="total_order_status_6">8</h5>
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
                <h5 class="fw-bold" id="total_order_status_7">8</h5>
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
                <h5 class="fw-bold" id="total_order_status_8">8</h5>
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
              <h2 class="mb-2" id="total_order">5</h2>
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
                      {{-- @foreach ($list_year as $year)
                        <a class="dropdown-item" id="dropdown-item-year" data-id="{{ $year }}" href="javascript:void(0);">{{ $year }}</a>
                      @endforeach --}}
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
                      {{-- @foreach ($list_month as $key => $month)
                        <a class="dropdown-item" id="dropdown-item-month" data-id="{{ $key + 1 }}" href="javascript:void(0);">{{ $month }}</a>
                      @endforeach --}}
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
