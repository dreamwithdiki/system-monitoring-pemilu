@extends('layouts/contentNavbarLayout')

@section('title', 'Partner - Visit')
@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/shepherd/shepherd.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
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
<script src="{{asset('assets/vendor/libs/autosize/autosize.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/shepherd/shepherd.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/form-wizard-icons.js')}}"></script>
<script src="{{asset('assets/js/wizard-ex-create-deal.js')}}"></script>
<script src="{{asset('js/partner/partner-visit.js')}}"></script>
@endsection

@section('content')
<style>
  .btn-refresh {
      border: 1px solid transparent;
      border-radius: 7px;
    }
</style>

<div class="d-flex justify-content-end mb-4">
  <div style="width: 25%">
    <input type="text" id="search" name="search" class="form-control search mt-3" placeholder="Search">
  </div>
</div>
<!--Visit Order -->
<div class="row">
  <!-- Container for the cards -->
  <div id="cardContainer" class="row"></div>

  <!-- Pagination -->
  <div class="card-body">
    <div class="row">
        <nav aria-label="Page navigation">
          <ul class="pagination justify-content-end">
            <li class="page-item prev"></li>
            <!-- pagination links generated dynamically by the script -->
            <li class="page-item next"></li>
          </ul>
        </nav>
    </div>
  </div>
<!--/ end Pagination -->
</div>
<!--/Visit Order -->

<hr class="my-5">

<!-- Modal View Product -->
<div class="modal fade" id="modalViewVisitOrder" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <div class="modal-body p-0">
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
                    <hr>
                    <div class="row">
                      <!-- Add the Google Maps iframe container -->
                      <iframe id="mapIframe" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
                    <hr>
                    <div class="row g-3">
                      <div id="checklist-file">
                        
                      </div>
                    </div>
                    <hr>
                    <div class="row g-3" id="visual-file">
                      
                    </div>
                    <hr>
                    <div class="col mb-3">
                      <label class="form-label" for="edit_desc">Note <span style='color:red'>*</span></label>
                      <textarea id="autosize-note" rows="5" name="edit_desc" class="form-control note-maxlength" maxlength="255" placeholder="Enter Desc" @required(true)></textarea>
                    </div>
                  </div>
                  <div class="modal-footer">
                      <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Save and Done</button>
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
                <div class="">
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
              <!-- End Checklist -->

              <!-- Visual -->
              <div id="visual" class="content">
                <form id="formVisualVisitOrder" enctype="multipart/form-data">
                  <div id="editFormLabel" class="text-center mb-4">
                    <h3><span>Upload Visual Visit Order<span></h3>
                    <p>upload.</p>
                  </div>
                  <div class="">          
                    <div id="file_upload">
                      
                    </div>
                  </div>
                  <div class="modal-footer">
                      <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Save and Done</button>
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
<!--/ Modal View Product, etc... -->

@endsection
