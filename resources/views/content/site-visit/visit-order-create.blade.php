@extends('layouts/contentNavbarLayout')

@section('title', 'Visit Order - Create')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css')}}" />
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
<!-- Include jQuery UI Autocomplete JS -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- Include jQuery UI Autocomplete CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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

    /* CSS style for the Visit Order Number, Latitude & Longitude */
    #visit_order_number {
      background-color: #e7e7ff;
    }
    #visit_order_latitude {
      background-color: #e7e7ff;
    }
    #visit_order_longitude {
      background-color: #e7e7ff;
    }

    /* Custom styles for the Autocomplete dropdown menu */
    .ui-autocomplete {
      max-height: 300px;
      overflow-y: auto;
      z-index: 9999;
      position: fixed;
      top: 0;
      left: 0;
    }

    /* Custom styles for the Autocomplete dropdown items */
    .ui-menu-item {
      display: block;
      padding: 5px 10px;
    }

    /* Custom styles for the Autocomplete dropdown items on hover */
    .ui-menu-item:hover {
      background-color: #f0f0f0;
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
            <a href="/site-visit/visit-order-manage" class="btn btn-label-secondary me-3"><span class="align-middle"> Cancel</span></a>
            <button type="submit" id="visit_order_submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-8 mx-auto">
              <!-- Visit Order -->
              <h5 class="mb-4">Visit Order</h5>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label" for="client_id">Client <span style='color:red'>*</span></label>
                  <select id="client_id" name="client_id" class="ac_client form-select" @required(true)>
                    <option value="">Select Client</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="site_id">Site <span style='color:red'>*</span></label>
                  <select id="site_id" name="site_id" class="ac_site form-select" @required(true)>
                    <option value="">Select Site</option>
                  </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="site_contact_id">Site Contact <span style='color:red'>*</span></label>
                    <input type="text" class="form-control" id="site_contact_id" name="site_contact_id" placeholder="Enter new site contact">
                </div>
                <div class="col-md-1 mt-5">
                  <button type="button" class="btn btn-primary me-sm-3 me-1" id="save_to_site_contact" title="Add new site contact"><i class="bx bx-plus-circle"></i></button>
                </div>  
                <div class="col-md-5">
                    <label class="form-label" for="debtor_id">Debtor <span style='color:red'>*</span></label>
                    <input type="text" class="form-control" id="debtor_id" name="debtor_id" placeholder="Enter new debtor">
                </div>
                <div class="col-md-1 mt-5">
                  <button type="button" class="btn btn-primary me-sm-3 me-1" id="save_to_debtor" title="Add new debtor"><i class="bx bx-plus-circle"></i></button>
                </div>                               
                <div class="col-md-6">
                  <label class="form-label" for="visit_type_id">Visit Type <span style='color:red'>*</span></label>
                  <select id="visit_type_id" name="visit_type_id" class="ac_visit_type form-select" @required(true)>
                    <option value="">Select Visit Type</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="partner_id">Partner</label>
                  <select id="partner_id" name="partner_id" class="ac_partner form-select" @required(true)>
                    <option value="">Select Partner</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="visit_order_number">Number <span style='color:red'>*</span></label>
                  <input type="text" id="visit_order_number" name="visit_order_number" class="form-control" placeholder="Order Number" value="{{ $code_number }}" @required(true) @readonly(true)>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="visit_order_custom_number">Custom Number </label>
                  <input type="text" id="visit_order_custom_number" name="visit_order_custom_number" class="form-control" placeholder="Custom Order Number">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="visit_order_date">Order Date <span style='color:red'>*</span></label>
                  <input type="text" id="visit_order_date" name="visit_order_date" class="dt form-control" placeholder="YYYY-MM-DD" @required(true)/>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="visit_order_due_date">Due Date <span style='color:red'>*</span></label>
                  <input type="text" id="visit_order_due_date" name="visit_order_due_date" class="dt form-control" placeholder="YYYY-MM-DD" @required(true)/>
                </div>
                <div class="col-md-12">
                  <label class="form-label" for="visit_order_location">Location <span style='color:red'>*</span></label>
                  <textarea id="autosize-location" rows="5" name="visit_order_location" class="form-control location-maxlength" maxlength="255" placeholder="Location" @required(true)></textarea>
                </div>

                <div class="col-md-6">
                  <label class="form-label" for="visit_order_province">Province <span style='color:red'>*</span></label>
                  <select id="visit_order_province" name="visit_order_province" class="form-select" @required(true)>
                    <option value="">Select Province Name</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="visit_order_regency">Regency <span style='color:red'>*</span></label>
                  <select id="visit_order_regency" name="visit_order_regency" class="form-select" @required(true)>
                    <option value="">Choice</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label" for="visit_order_district">District</label>
                  <select id="visit_order_district" name="visit_order_district" class="form-select">
                    <option value="">Choice</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label" for="visit_order_village">Village</label>
                  <select id="visit_order_village" name="visit_order_village" class="form-select">
                    <option value="">Choice</option>
                  </select>
                </div>

                <div class="col-md-12">
                  <div id="map"></div>
                </div>

                <div class="col-md-12">
                  <label class="form-label" for="visit_order_location_map">Location Map </label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text" id="search_maps_api"><i class="bx bx-search"></i></span>
                    <input type="text" id="visit_order_location_map" name="visit_order_location_map" class="form-control" placeholder="Search Location ..." aria-label="Search Location ..." aria-describedby="search_maps_api"  />
                    <div class="result-list"></div>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label" for="visit_order_latitude">Latitude </label>
                  <input type="text" id="visit_order_latitude" name="visit_order_latitude" class="form-control" placeholder="Latitude" @required(true) @readonly(true)/>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="visit_order_longitude">Longitude </label>
                  <input type="text" id="visit_order_longitude" name="visit_order_longitude" class="form-control" placeholder="Longitude" @required(true) @readonly(true)/>
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
                <button type="submit" class="btn btn-primary me-sm-3 me-1" id="visit_order_submit">Submit</button>
                <a href="/site-visit/visit-order-manage" class="btn btn-label-secondary">Cancel</a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- /Visit Order -->

<script>
  function initMap() {
    var input = document.getElementById('visit_order_location_map');
    var latitudeInput = document.getElementById('visit_order_latitude');
    var longitudeInput = document.getElementById('visit_order_longitude');
    var mapElement = document.getElementById('map');

    var autocomplete = new google.maps.places.Autocomplete(input);
    var map = new google.maps.Map(mapElement, {
      zoom: 12,
      center: { lat: -6.21462, lng: 106.84513 } // Set initial map center
    });
    var marker = new google.maps.Marker({
      map: map,
      draggable: true // Allow marker to be dragged
    });

    // Add a click event listener to the map
    map.addListener('click', function(event) {
      var location = event.latLng;
      latitudeInput.value = location.lat();
      longitudeInput.value = location.lng();
      marker.setPosition(location); // Update marker position
      updateAddressFromMarker(location);
    });

    // Add a dragend event listener to the marker
    marker.addListener('dragend', function(event) {
      var location = event.latLng;
      latitudeInput.value = location.lat();
      longitudeInput.value = location.lng();
      updateAddressFromMarker(location);
    });

    autocomplete.addListener('place_changed', function() {
      var place = autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }

      var location = place.geometry.location;
      latitudeInput.value = location.lat();
      longitudeInput.value = location.lng();

      // Update map center and marker position
      map.setCenter(location);
      marker.setPosition(location);
      updateAddressFromMarker(location);
    });

    // Function to update the address field from marker position
    function updateAddressFromMarker(location) {
      var geocoder = new google.maps.Geocoder();
      geocoder.geocode({ 'location': location }, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
          if (results[0]) {
            input.value = results[0].formatted_address;
          } else {
            input.value = 'Address not found';
          }
        } else {
          input.value = 'Geocoder failed due to: ' + status;
        }
      });
    }
  }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>

@endsection
