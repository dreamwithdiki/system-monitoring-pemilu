@extends('layouts/contentNavbarLayout')

@section('title', 'Visit Order - Manage')
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
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css')}}" />
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
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<script src="{{asset('assets/vendor/libs/autosize/autosize.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/shepherd/shepherd.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<!-- Include jQuery UI Autocomplete JS -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- Include jQuery UI Autocomplete CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('page-script')
<script src="{{asset('assets/js/form-wizard-icons.js')}}"></script>
<script src="{{asset('assets/js/wizard-ex-create-deal.js')}}"></script>
<script src="{{asset('js/site-visit/visit-order-manage.js')}}"></script>
@endsection

@section('content')
<style>
  #map {
      height: 400px;
      width: 100%;
  }

  /* don't remove this css bro soalnya autocomplete tidak muncul kalau tidak pakai css ini buat di modal */
  .pac-container {
    z-index: 9999 !important;
  }

  .pac-container {
    position: absolute !important;
  }
  /* end don't remove this css */

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
      z-index: 9999 !important;
      position: absolute !important;
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

    .btn-refresh {
      border: 1px solid transparent;
      border-radius: 7px;
    }
</style>
<!-- Data Table Visit Order -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-list-ol bx-sm me-sm-2"></i>Visit Order Table</h5>
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
          <th style="width: 2%;">No</th>
          <th style="width: 5%;">Order Number</th>
          <th style="width: 3%;">Date</th>
          <th style="width: 3%;">Due Date</th>
          <th style="width: 10%;">Debtor Name</th>
          <th style="width: 10%;">Client Name</th>
          <th style="width: 10%;">Site Name</th>
          <th style="width: auto;">Location</th>
          <th style="width: 10%;">Partner Name</th>
          <th style="width: 5%;">Custom Order Number</th>
          <th style="width: 3%;">Download Status</th>
          <th style="width: 3%;">Status</th>
          <th style="width: 1%;">Actions</th>
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
                          <a id="openGoogleMapsBtn" class="btn btn-sm btn-primary" title="Open in Google Maps" target="_blank" style="display: none;">Open in Google Maps</a>
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
                <div class="text-center mb-4">
                  <h3><span>Visual Visit Order<span></h3>
                  <p>upload.</p>
                </div>
                <div class="card-body">
                  <div class="row g-3" id="visual-file">
                    
                  </div>
                </div>
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

<!-- Modal Change Status -->
<div class="modal fade" id="modalChangeStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <div class="modal-body">
          <form id="formChangeStatus">
            <div id="checklistFormLabel" class="text-center mb-4">
              <h3><span id="title-header">Checklist<span></h3>
              <p id="desc-header">checklist.</p>
            </div>
            <div class="m-4">
              <div class="card-body">
                <div class="col mb-3" id="partner">
                  <label class="form-label" for="partner_id">Partner Name <span style='color:red'>*</span></label>
                  <select id="partner_id" name="partner_id" class="ac_partner_re_visit form-select" @required(true)>
                    <option value="">Select Partner Name</option>
                  </select>
                </div>
                <div class="col mb-3">
                  <label class="form-label" for="edit_note">Note <span style='color:red'>*</span></label>
                  <textarea id="edit_note" rows="5" name="edit_note" class="form-control note-maxlength" maxlength="255" placeholder="Enter Desc" @required(true)></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
          </form>
        </div>

    </div>
  </div>
</div>
<!--/ Modal Change Status -->

<!-- Modal Checklist Visit Order -->
<div class="modal fade" id="modalChecklistVisitOrder" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="checklistFormLabel" class="text-center mb-4">
          <h3><span>Checklist<span></h3>
          <p>checklist.</p>
        </div>
        <div class="card-body">
          <div class="row">
            <div id="checkboxContainer"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveChangesButton">Save Changes</button>
      </div>

    </div>
  </div>
</div>
<!--/ Modal Checklist Visit Order -->

<!-- Modal Visual Visit Order -->
<div class="modal fade" id="modalVisualVisitOrder" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formVisualVisitOrder" enctype="multipart/form-data">
        <div class="modal-body">
          <div id="editFormLabel" class="text-center mb-4">
            <h3><span>Upload Visual Visit Order<span></h3>
            <p>upload.</p>
          </div>
          <div id="file_upload">
              
          </div>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save and Done</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!--/ Modal Visual Visit Order -->

<!-- Modal Edit Visit Order -->
<div class="modal fade" id="modalEditVisitOrder" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditVisitOrder" data-method="edit">
        <div class="modal-body">
          <div id="editFormLabel" class="text-center mb-4">
            <h3><span>Edit Visit Order<span></h3>
            <p>edit.</p>
          </div>
          <div class="m-4">
            <div class="card-body">
              <div class="row">
                
                  <div class="row">
                    <input type="hidden" id="visit_order_status" name="visit_order_status" class="form-control">
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
                    <div class="col-md-1 mt-4">
                      <button type="button" class="btn btn-primary me-sm-3 me-1" id="save_to_site_contact" title="Add new site contact"><i class="bx bx-plus-circle"></i></button>
                    </div> 
                    <div class="col-md-5">
                        <label class="form-label" for="debtor_id">Debtor <span style='color:red'>*</span></label>
                        <input type="text" class="form-control" id="debtor_id" name="debtor_id" placeholder="Enter new debtor">
                    </div>
                    <div class="col-md-1 mt-4">
                      <button type="button" class="btn btn-primary me-sm-3 me-1" id="save_to_debtor" title="Add new debtor"><i class="bx bx-plus-circle"></i></button>
                    </div> 
                    <div class="col-md-6">
                      <label class="form-label" for="visit_type_id">Visit Type <span style='color:red'>*</span></label>
                      <select id="visit_type_id" name="visit_type_id" class="ac_visit_type form-select" @required(true)>
                        <option value="">Select Visit Type</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label" for="partner_id">Partner <span style='color:red'>*</span></label>
                      <select id="partner_select" name="partner_id" class="ac_partner form-select" @required(true)>
                        <option value="">Select Partner</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label" for="visit_order_number">Number <span style='color:red'>*</span></label>
                      <input type="text" id="visit_order_number" name="visit_order_number" class="form-control" placeholder="Order Number" @required(true) @readonly(true)>
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
                    <!-- Note -->
                    <h5 class="my-4">Leave a note</h5>
                      <div class="col-md-12">
                        <label class="form-label" for="visit_order_note">Note</label>
                        <textarea id="visit_order_note-autosize" rows="5" name="visit_order_note" class="form-control note-maxlength" maxlength="255"></textarea>
                      </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
</div>
<!--/ Modal Edit Visit Order -->

<!-- Modal Set Visited Visit Order -->
<div class="modal fade" id="modalSetVisitedVisitOrder" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formSetVisitedVisitOrder" data-method="edit">
        <div class="modal-body">
          <div id="editFormLabel" class="text-center mb-4">
            <h3><span>Set Visited Visit Order<span></h3>
            <p>set visited.</p>
          </div>
          <div class="m-4">
            <div class="card-body">                
                <div class="row">
                  <div class="col-md-6">
                    <label class="form-label" for="partner_id">Partner <span style='color:red'>*</span></label>
                    <select id="partner_select_set_visited" name="partner_id" class="ac_partner_set_visited form-select" @required(true)>
                      <option value="">Select Partner</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="visit_order_date">Date <span style='color:red'>*</span></label>
                    <input type="text" id="visit_order_date_set_visited" name="visit_order_date" class="dt form-control" placeholder="YYYY-MM-DD" @required(true)/>
                  </div>
                  <hr class="mt-2">
                  <div id="checkboxContainerVisited"></div>
                  <hr>
                  <div id="file_upload_visited"></div>
                  <hr>
                  <!-- Note -->
                  <div class="col-md-12">
                    <label class="form-label" for="visit_order_note">Note</label>
                    <textarea id="visit_order_note_set_visited" rows="5" name="visit_order_note" class="form-control note-maxlength" maxlength="255"></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" id="saveChangesSetVisitedButton" class="btn btn-primary">Save changes</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
</div>
<!--/ Modal Set Visited Visit Order -->

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

    // Get the existing marker location and set it on the map
    var latitude = parseFloat(latitudeInput.value);
    var longitude = parseFloat(longitudeInput.value);

    if (!isNaN(latitude) && !isNaN(longitude)) {
      var existingLocation = {
        lat: latitude,
        lng: longitude,
      };
      marker.setPosition(existingLocation);
      map.setCenter(existingLocation);
      updateAddressFromMarker(existingLocation);
    }
  }

  document.getElementById('modalEditVisitOrder').addEventListener('shown.bs.modal', function() {
    setTimeout(function() {
      initMap();
    }, 500); // Adjust the delay as needed
  });

</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>

@endsection
