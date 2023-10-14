@extends('layouts/contentNavbarLayout')

@section('title', 'Visit Order - Create')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/leaflet/leaflet.css')}}" />
{{-- <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" /> --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.0.0/dist/geosearch.css"/>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-sticky/jquery-sticky.js')}}"></script>
<script src="{{asset('assets/vendor/libs/autosize/autosize.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('js/site-visit/visit-order-create.js')}}"></script>
@endsection

@section('content')
<style>
  #map {
      height: 400px;
      width: 100%;
  }

  .result-list {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 5px;
    }

    .result-item {
        cursor: pointer;
        padding: 5px;
        border-bottom: 1px solid #ccc;
    }
</style>
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Forms/</span>
  Visit Order
</h4>
<!-- Visit Order -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <form id="formAddVisitOrder" data-method="add">
        <div class="card-header sticky-element bg-label-secondary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
          <h5 class="card-title mb-sm-0 me-2">Visit Order Bar</h5>
          <div class="action-btns">
            <button class="btn btn-label-primary me-3">
              <span class="align-middle"> Back</span>
            </button>
            <button type="submit" id="visit_order" class="btn btn-primary">Visit Order</button>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-8 mx-auto">
              <!-- Visit Order -->
              <h5 class="mb-4">Visit Order</h5>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label" for="client_id">Client Name <span style='color:red'>*</span></label>
                  <select id="client_id" name="client_id" class="ac_client form-select" @required(true)>
                    <option value="">Select Client Name</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="site_id">Site Name <span style='color:red'>*</span></label>
                  <select id="site_id" name="site_id" class="ac_site form-select" @required(true)>
                    <option value="">Select Site Name</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="debtor_id">Debtor Name <span style='color:red'>*</span></label>
                  <select id="debtor_id" name="debtor_id" class="ac_debtor form-select" @required(true)>
                    <option value="">Select Debtor Name</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="visit_type_id">Visit Type Name <span style='color:red'>*</span></label>
                  <select id="visit_type_id" name="visit_type_id" class="ac_visit_type form-select" @required(true)>
                    <option value="">Select Visit Type Name</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="partner_id">Partner Name <span style='color:red'>*</span></label>
                  <select id="partner_id" name="partner_id" class="ac_partner form-select" @required(true)>
                    <option value="">Select Partner Name</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="visit_order_number">Number <span style='color:red'>*</span></label>
                  <input type="text" id="visit_order_number" name="visit_order_number" class="form-control" placeholder="Order Number" @required(true)/>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="visit_order_location">Location <span style='color:red'>*</span></label>
                  <input type="text" id="visit_order_location" name="visit_order_location" class="form-control" placeholder="Order Location" @required(true)/>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="visit_order_date">Date <span style='color:red'>*</span></label>
                  <input type="text" id="visit_order_date" name="visit_order_date" class="dt form-control" placeholder="YYYY-MM-DD" @required(true)/>
                </div>

                <div id="map"></div>

                <div class="col-md-12">
                  <label class="form-label" for="visit_order_location_map">Location Map <span style='color:red'>*</span></label>
                  {{-- <input type="text" id="visit_order_location_map" name="visit_order_location_map" class="form-control" placeholder="Search Location Map" @required(true)/> --}}

                  <div class="search-control">
                      <input type="text" id="visit_order_location_map" name="visit_order_location_map" class="form-control" placeholder="Search Location" />
                      <div class="result-list"></div>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label" for="visit_order_latitude">Latitude <span style='color:red'>*</span></label>
                  <input type="text" id="visit_order_latitude" name="visit_order_latitude" class="form-control" placeholder="Latitude" @required(true)/>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="visit_order_longitude">Longitude <span style='color:red'>*</span></label>
                  <input type="text" id="visit_order_longitude" name="visit_order_longitude" class="form-control" placeholder="Longitude" @required(true)/>
                </div>
              </div>
              <hr>
              <!-- Note -->
              <h5 class="my-4">Leave a note</h5>
              <div class="row gy-3">
                <div class="col-md-12">
                  <label class="form-label" for="visit_order_note">Note</label>
                  <textarea id="autosize-note" rows="5" name="visit_order_note" class="form-control note-maxlength" maxlength="255"></textarea>
                </div>
              </div>
              <div class="pt-4">
                <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                <button type="reset" class="btn btn-label-secondary">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- /Visit Order -->

<script src="{{asset('assets/vendor/libs/leaflet/leaflet.js')}}"></script>
{{-- <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script> --}}
<script src="https://unpkg.com/leaflet-geosearch@3.0.0/dist/geosearch.umd.js"></script>
<script>
// var map = L.map('map').setView([0, 0], 13);

var map = new L.Map('map', {
			zoom: 8,
			center: new L.latLng(-0.0652, 109.3443)
		});

L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiZmFjaHJlYWwiLCJhIjoiY2xqcGc5eWx6MDB6ajN0bXY5OWh0N3ZtZiJ9.5eW33FzHi7vbpC-rrGFAzA', {
    attribution: 'Developed by dev © <a href="https://dpi.co.id/">PT. Dana Purna Investama</a>',
    id: 'mapbox/streets-v11'
}).addTo(map);

// L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
//     attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
// }).addTo(map);

var searchInput = document.getElementById('visit_order_location_map');
var latitudeInput = document.getElementById('visit_order_latitude');
var longitudeInput = document.getElementById('visit_order_longitude');

// var provider = new GeoSearch.OpenStreetMapProvider();

// searchInput.addEventListener('input', function(e) {
//     var value = e.target.value;
//     provider.search({ query: value }).then(function(result) {
//         if (result.length > 0) {
//             var location = result[0];
//             var center = L.latLng(location.y, location.x);
//             map.flyTo(center, 13);
//             latitudeInput.value = center.lat;
//             longitudeInput.value = center.lng;
//         }
//     });
// });

var provider = new GeoSearch.OpenStreetMapProvider({
    params: {
      countrycodes: 'ID' // Batasi pencarian hanya di Indonesia
    }
  });


  searchInput.addEventListener('input', function(e) {
    var value = e.target.value;
    provider.search({ query: value }).then(function(result) {
      var resultContainer = document.querySelector('.result-list');
      resultContainer.innerHTML = '';

      result.forEach(function(item) {
        var resultItem = document.createElement('div');
        resultItem.classList.add('result-item');
        resultItem.textContent = item.label;
        resultItem.addEventListener('click', function() {
          searchInput.value = item.label;
          map.flyTo([item.y, item.x], 13);
          latitudeInput.value = item.y;
          longitudeInput.value = item.x;
          resultContainer.innerHTML = ''; // Hapus hasil autocomplete setelah dipilih
        });
        resultContainer.appendChild(resultItem);
      });
    });
  });

</script>

@endsection
