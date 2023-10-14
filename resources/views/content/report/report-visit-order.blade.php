@extends('layouts/contentNavbarLayout')

@section('title', 'Report - Visit Order')
@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/shepherd/shepherd.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/autosize/autosize.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/shepherd/shepherd.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/form-wizard-icons.js')}}"></script>
<script src="{{asset('assets/js/wizard-ex-create-deal.js')}}"></script>
<script src="{{asset('js/report/report-visit-order.js')}}"></script>
@endsection

@section('content')
<!-- Form Visit Order -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-list-ol bx-sm me-sm-2"></i>Form Visit Order</h5>
    </div>
  </div>
  <div class="container mb-3">
    <form id="formFilterVisitOrder">
      <div class="row g-3">
        <div class="col-md-3">
          Param <span style='color:red'>*</span>
        </div>
        <div class="col-md-3">
          <input type="radio" id="client" class="form-check-input" name="searchParam" value="client" checked>
          <label for="client">Client</label>
          <input type="radio" id="site" class="form-check-input" name="searchParam" value="site">
          <label for="site">Site</label>
          <input type="radio" id="partner" class="form-check-input" name="searchParam" value="partner">
          <label for="partner">Partner</label>
        </div>
        <div class="col-md-6 client_select">
          <label class="form-label" for="client_id">Client Name</label>
          <select id="client_id" name="client_id" class="ac_client form-select" @required(true)>
            <option value="">Select Client Name</option>
          </select>
        </div>
        <div class="col-md-6 site_select" style="display: none">
          <label class="form-label" for="site_id">Site Name</label>
          <select id="site_id" name="site_id" class="ac_site form-select" @required(true)>
            <option value="">Select Site Name</option>
          </select>
        </div>
        <div class="col-md-6 partner_select" style="display: none">
          <label class="form-label" for="partner_id">Partner Name</label>
          <select id="partner_id" name="partner_id" class="ac_partner form-select" @required(true)>
            <option value="">Select Partner Name</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="status_id">Status Name <span style='color:red'>*</span></label>
          <select id="status_id" name="status_id" class="ac_status form-select" @required(true)>
            <option value="">Select Status Name</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="time_id">Filter Time <span style='color:red'>*</span></label>
          <select id="time_id" name="time_id" class="ac_time form-select" @required(true)>
            <option value="">Select Filter Time</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="site_id">Start Date <span style='color:red'>*</span></label>
          <input type="text" id="start_date" name="start_date" class="dt form-control" placeholder="YYYY-MM-DD" @required(true)/>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="site_id">End Date <span style='color:red'>*</span></label>
          <input type="text" id="end_date" name="end_date" class="dt form-control" placeholder="YYYY-MM-DD" @required(true)/>
        </div>
      </div>
      <div class="pt-4">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
        <button type="button" class="btn btn-success me-sm-3 me-1" id="printToExcel">Print to Excel</button>
      </div>
    </form>
  </div>
</div>
<!--/ Form Visit Order -->

<br class="my-2">

<!-- Data Table Visit Order -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-list-ol bx-sm me-sm-2"></i>Report Visit Order</h5>
    </div>
  </div>
  <div class="card-datatable text-nowrap">
    <div class="d-flex justify-content-end pe-3">
      <div style="width: 25%">
        <input type="text" id="search" name="search" class="form-control search mt-3" placeholder="Search">
      </div>
    </div>
    <table class="datatables-ajax table table-hover">
      <thead>
        <tr>
          <th>No</th>
          <th>Order Number</th>
          <th>Date</th>
          <th>Due Date</th>
          <th>Debtor Name</th>
          <th>Client Name</th>
          <th>Site Name</th>
          <th>Province Name</th>
          <th>Regency Name</th>
          <th>Location</th>
          <th>Partner Name</th>
          <th>Custom Order Number</th>
          <th>Download Status</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Data Table Visit Order -->

<hr class="my-5">

<!-- Modal Detail Visit Order -->
<div class="modal fade" id="modalDetailVisitOrder" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Default Icons Wizard -->
        <div class="col-12">
          <div class="bs-stepper wizard-icons wizard-icons-example mt-2">

            <div class="bs-stepper-header">
              <div class="step" data-target="#detail">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle">
                    <i class="bx bx-detail"></i>
                  </span>
                  <span class="bs-stepper-label">Detail</span>
                </button>
              </div>
              <div class="line"></div>
              <div class="step" data-target="#checklist">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle">
                    <i class="bx bx-list-check"></i>
                  </span>
                  <span class="bs-stepper-label">Checklist </span>
                </button>
              </div>
              <div class="line"></div>
              <div class="step" data-target="#visual">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle">
                    <i class="bx bx-paperclip"></i>
                  </span>
                  <span class="bs-stepper-label">Visual </span>
                </button>
              </div>
              <div class="line"></div>
              <div class="step" data-target="#history">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle">
                    <i class="bx bx-history"></i>
                  </span>
                  <span class="bs-stepper-label">History </span>
                </button>
              </div>
            </div>
            <div class="bs-stepper-content">

              <!-- Detail -->
              <div id="detail" class="content">
                <form id="formNoteVisitOrder" enctype="multipart/form-data">
                  <div id="editFormLabel" class="text-center mb-4">
                    <h3><span>Detail Visit Order<span></h3>
                    <p>detail.</p>
                  </div>
                  <div class="">
                    <div class="row g-2">
                        <div class="col mb-3">
                          <label class="form-label" for="edit_number">Number</label>
                          <h6 id="edit_number"></h6>
                        </div>
                        <div class="col mb-3">
                          <label class="form-label" for="edit_debtor_name">Debtor Name</label>
                          <h6 id="edit_debtor_name"></h6>
                        </div>
                    </div>
                    <div class="row g-2">
                      <div class="col mb-3">
                        <label for="edit_location" class="form-label">Location</label>
                        <h6 id="edit_location"></h6>
                      </div>
                      <div class="col mb-3">
                        <label for="edit_debtor_address" class="form-label">Debtor Address</label>
                        <h6 id="edit_debtor_address"></h6>
                      </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                          <label class="form-label" for="edit_client_name">Client Name</label>
                          <h6 id="edit_client_name"></h6>
                        </div>
                        <div class="col mb-3">
                          <label class="form-label" for="edit_site_name">Site Name</label>
                          <h6 id="edit_site_name"></h6> 
                        </div>
                    </div>
                    <div class="row g-2">
                      <div class="col mb-3">
                        <label class="form-label" for="edit_site_contact_name">Site Contact Name</label>
                        <h6 id="edit_site_contact_name"></h6> 
                      </div>
                      <div class="col mb-3">
                        <label class="form-label" for="edit_visit_type_name">Visit Type Name</label>
                        <h6 id="edit_visit_type_name"></h6> 
                      </div>
                    </div>
                    <div class="row g-2">
                      <div class="col mb-3">
                        <label for="edit_date" class="form-label">Order Date</label>
                        <h6 id="edit_date"></h6>
                      </div>
                      <div class="col mb-3">
                        <label for="edit_visited_date" class="form-label">Visited Date</label>
                        <h6 id="edit_visited_date"></h6>
                      </div>
                    </div>
                    <div class="row g-2">
                      <div class="col mb-3">
                        <label for="edit_custom_number" class="form-label">Custom Number</label>
                        <h6 id="edit_custom_number"></h6>
                      </div>
                    </div>
                    <hr>
                    <div class="row">
                      <!-- Add the Google Maps iframe container -->
                      <iframe id="mapIframe" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" style="display: none;"></iframe>
                      <div class="d-grid gap-2 col-lg-12 mx-auto mt-3">
                        <!-- Add the Google Maps link -->
                        <a id="openGoogleMapsBtn" class="btn btn-sm btn-primary" title="Open in Google Maps" target="_blank">Open in Google Maps</a>
                      </div>
                      <hr>
                      <div class="col-12">
                        <label class="form-label" for="detLocationMap">Location Map</label>
                        <h6 id="detLocationMap"></h6>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              <!-- Detail -->

              <!-- Checklist -->
              <div id="checklist" class="content">
                <div id="checklistFormLabel" class="text-center mb-4">
                  <h3><span>Checklist<span></h3>
                  <p>checklist.</p>
                </div>
                <div class="card-body">
                  <div class="row g-3">
                    <div id="checklist-file">
                      
                    </div>
                  </div>
                </div>
              </div>
              <!-- End Checklist -->

              <!-- Visual -->
              <div id="visual" class="content">
                <form id="formVisualVisitOrder" enctype="multipart/form-data">
                  <div id="editFormLabel" class="text-center mb-4">
                    <h3><span>Visual Visit Order<span></h3>
                    <p>upload.</p>
                  </div>
                  <div class="card-body">
                    <div class="row g-3" id="visual-file">
                      
                    </div>
                  </div>
                </form>
              </div>
              <!-- End Visual -->

              <!-- History -->
              <div id="history" class="content">
                <div id="editFormLabel" class="text-center mb-4">
                  <h3><span>History Visit Order<span></h3>
                  <p>history.</p>
                </div>
                <div class="">
                  <ul class="timeline" id="history_visit_order">
                    
                  </ul>
                </div>
              </div>
              <!-- End History -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Modal Detail Visit Order -->

@endsection
