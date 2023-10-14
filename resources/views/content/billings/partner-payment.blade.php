@extends('layouts/contentNavbarLayout')

@section('title', 'Partner Payment - Manage')
@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
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
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/form-wizard-icons.js')}}"></script>
<script src="{{asset('assets/js/wizard-ex-create-deal.js')}}"></script>
<script src="{{asset('js/billings/partner-payment.js')}}"></script>
@endsection

@section('content')
<style>
  #partner_payment_number {
    background-color: #e7e7ff;
  }
  #EditNumber {
    background-color: #e7e7ff;
  }

</style>
<!-- Data Table Partner Payment -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-user-voice bx-sm me-sm-2"></i>Partner Payment Table</h5>
    </div>
    <div class="text-end pt-3 pt-md-0">
      <button class="btn btn-primary fw-bold addButton" type="button" data-bs-toggle="modal" data-bs-target="#modalAddPartnerPayment">
        <span><i class="tf-icons bx bx-plus-medical me-sm-2"></i> <span class="d-none d-sm-inline-block">Add Partner Payment</span></span>
      </button>
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
          <th>Number</th>
          <th>Name</th>
          <th>Period Month</th>
          <th>Period Year</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Data Table Partner Payment -->

<hr class="my-5">

<!-- Modal Add Partner Payment -->
<div class="modal fade" id="modalAddPartnerPayment" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddPartnerPayment" data-method="add">
        <div class="modal-body">
          <div id="addFormLabel" class="text-center mb-4">
            <h3><span>Partner Payment Form<span></h3>
            <p>Add new Partner Payment data.</p>
          </div>
          <div class="m-4">
            <div class="row g-2">
              <div class="col mb-3">
                <label for="partner_payment_number" class="form-label">Number</label>
                <input type="text" id="partner_payment_number" name="partner_payment_number" class="form-control" placeholder="Enter Number" value="{{ $code_number }}" @required(true) @readonly(true)>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="partner_payment_name">Name <span style='color:red'>*</span></label>
                <input type="text" id="partner_payment_name" name="partner_payment_name" class="form-control" placeholder="Enter Name" @required(true)>
              </div>
            </div>
            <div class="row g-2">
                <div class="col mb-3">
                    <label for="partner_payment_month" class="form-label">Month <span style='color:red'>*</span></label>
                    <select class="form-select" id="partner_payment_month" name='partner_payment_month'>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>{{ month_name($i) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col mb-3">
                    <label for="partner_payment_year" class="form-label">Year <span style='color:red'>*</span></label>
                    <select class="form-select" id="partner_payment_year" name='partner_payment_year'>
                        @for ($i = date('Y'); $i <= date('Y') + 2; $i++)
                            <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>                  
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                  <label class="form-label">File</label>
                  <div class="dropzone needsclick dropzone-multi-file" id="add-file-payment">
                    <div class="dz-message needsclick">
                      Drop files here or click to upload file
                      <span class="note needsclick">(multiple file and max size are 1024 KB/file, format: jpeg, png, jpg, pdf, doc, docx, xls, xlsx, ppt, and pptx)</span>
                    </div>
                    <div class="fallback">
                      <input name="partner_payment_file" type="file"/>
                    </div>
                  </div>
                </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="partner_payment_desc" class="form-label">Description</label>
                <textarea id="partner_payment_desc" name="partner_payment_desc" class="form-control" placeholder="Explanation about the new desc" rows="5" style="max-height: 100px;resize: none;"></textarea>
              </div>
            </div>    
            <div class="card">
              <label for="visit_order" class="form-label">Visit Order</label>
              <div class="card-datatable text-nowrap">
                <table class="dt-add-visit-order table table-bordered">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Order Number</th>
                      <th>Date</th>
                      <th>Due Date</th>
                      <th>Client Name</th>
                      <th>Site Name</th>
                      <th>Location</th>
                      <th>Partner Name</th>
                      <th>Download Status</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>       
          </div>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-info" data-save-button>Save</button>
          <button type="submit" class="btn btn-primary" data-save-and-submit-button>Save and Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!--/ Modal Add Partner Payment -->

<!-- Modal Edit Partner Payment -->
<div class="modal fade" id="modalEditPartnerPayment" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <div class="modal-body">
        <!-- Default Icons Wizard -->
        <div class="col-12 mb-4">
          <div class="bs-stepper wizard-icons wizard-icons-example mt-2">

            <div class="bs-stepper-header">
              <div class="step" data-target="#data-partner">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle">
                    <i class="bx bx-wallet"></i>
                  </span>
                  <span class="bs-stepper-label">Data Partner</span>
                </button>
              </div>
              <div class="line"></div>
              <div class="step" data-target="#attachment" id="file_attachment">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle">
                    <i class="bx bx-paperclip"></i>
                  </span>
                  <span class="bs-stepper-label">Attachment </span>
                </button>
              </div>
            </div>
            <div class="bs-stepper-content">
               <!-- Data Partner Payment -->
               <form id="formEditPartnerPayment" data-method="edit">
                  <div id="data-partner" class="content">
                      <div id="editFormLabel" class="text-center mb-3">
                        <h3><span>Partner Payment Form<span></h3>
                        <p>Edit new Partner Payment data.</p>
                      </div>
                      <div class="m-4">
                        <div class="row g-2">
                          <div class="col mb-3">
                              <label for="EditNumber" class="form-label">Number</label>
                              <input type="text" id="EditNumber" name="partner_payment_number" class="form-control" placeholder="Enter Number" @readonly(true)>
                          </div>
                          <div class="col mb-3">
                              <label class="form-label" for="EditName">Name <span style='color:red'>*</span></label>
                              <input type="text" id="EditName" name="partner_payment_name" class="form-control" placeholder="Enter Name" @required(true)>
                          </div>
                        </div>
                        <div class="row g-2">
                          <div class="col mb-3">
                              <label for="EditMonth" class="form-label">Month <span style='color:red'>*</span></label>
                              <select class="form-select" id="EditMonth" name='partner_payment_month'>
                                  @for ($i = 1; $i <= 12; $i++)
                                      <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>{{ month_name($i) }}</option>
                                  @endfor
                              </select>
                          </div>
                          <div class="col mb-3">
                              <label for="EditYear" class="form-label">Year <span style='color:red'>*</span></label>
                              <select class="form-select" id="EditYear" name='partner_payment_year'>
                                  @for ($i = date('Y'); $i <= date('Y') + 2; $i++)
                                      <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                  @endfor
                              </select>                  
                          </div>
                      </div>
                      <div class="card">
                        <h5 class="card-header">Visit Order</h5>
                        <div class="card-datatable text-nowrap">
                          <table class="dt-edit-visit-order table table-bordered">
                            <thead>
                              <tr>
                                <th>No</th>
                                <th>Order Number</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Client Name</th>
                                <th>Site Name</th>
                                <th>Location</th>
                                <th>Partner Name</th>
                                <th>Download Status</th>
                                <th>Status</th>
                              </tr>
                            </thead>
                          </table>
                        </div>
                      </div>
                      <div class="row">
                          <div class="col mb-3">
                              <label for="EditDesc" class="form-label">Description</label>
                              <textarea id="EditDesc" name="partner_payment_desc" class="form-control" placeholder="Explanation about the new desc" rows="5" style="max-height: 100px;resize: none;"></textarea>
                          </div>
                      </div>           
                      <div class="modal-footer">
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                      </div>
                  </div>
                 </div>
                </form>
                <!-- End Data Partner Payment -->

               <!-- Attachment -->
               <form id="form_add_attchment">
                <div id="attachment" class="content">
                  <div class="text-center mb-3">
                    <h3 class="mb-0">Attachment</h3>
                    <small>Enter new file atachment.</small>
                  </div>
                  <div class="row g-3">
                    <div class="row">
                      <div class="col mb-3">
                        <label class="form-label">File</label>
                        <div class="dropzone needsclick dropzone-multi-file" id="add-file-attachment">
                          <div class="dz-message needsclick">
                            Drop files here or click to upload file
                            <span class="note needsclick">(multiple file and max size are 1024 KB/file, format: jpeg, png, jpg, pdf, doc, docx, xls, xlsx, ppt, and pptx)</span>
                          </div>
                          <div class="fallback">
                            <input name="partner_payment_file" id="partner_payment_file" type="file"/>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col mb-3">
                        <label for="partner_payment_file_desc" class="form-label">Description</label>
                        <textarea id="partner_payment_file_desc" name="partner_payment_file_desc" class="form-control" placeholder="Explanation about the new payment file desc" rows="5" style="max-height: 100px;resize: none;"></textarea>
                      </div>
                    </div>

                    <div class="d-flex flex-row-reverse">
                      <button type="submit" class="btn btn-primary">Add Payment File</button>
                    </div>
                    
                    <hr>
                    <!-- Data table attachment -->
                    <div class="card-datatable text-nowrap">
                      <table class="datatables-ajax-attachment table table-hover">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>File</th>
                            <th>Desc</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
              </form>
              <!-- End Attachment -->
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<!--/ Modal Edit Partner Payment, etc... -->

<!-- Modal Detail Partner Payment -->
<div class="modal fade" id="modalDetailPartnerPayment" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Default Icons Wizard -->
        <div class="col-12">
          <div class="bs-stepper wizard-icons wizard-modern-icons-example mt-2">

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
              <div class="step" data-target="#attachment">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle">
                    <i class="bx bx-paperclip"></i>
                  </span>
                  <span class="bs-stepper-label">Attachment </span>
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
              <form id="formDetPartnerPayment" data-method="detail">
                <div id="detail" class="content">
                  <div id="detFormLabel" class="text-center mb-4">
                    <h3><span>Detail Partner Payment<span></h3>
                    <p>detail.</p>
                  </div>
                  <div class="">
                    <div class="row g-2">
                        <div class="col mb-3">
                          <label class="form-label" for="detNumber">Number</label>
                          <h6 id="detNumber"></h6>
                        </div>
                        <div class="col mb-3">
                          <label class="form-label" for="detName">Name</label>
                          <h6 id="detName"></h6>
                        </div>
                    </div>
                    <div class="row g-2">
                      <div class="col mb-3">
                        <label for="detMonth" class="form-label">Month</label>
                        <h6 id="detMonth"></h6>
                      </div>
                      <div class="col mb-3">
                        <label for="detYear" class="form-label">Year</label>
                        <h6 id="detYear"></h6>
                      </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                          <label class="form-label" for="detDesc">Desc</label>
                          <h6 id="detDesc"></h6>
                        </div>
                    </div>

                    <hr>
                    <h5>List Visit Order</h5>
                    <div class="card-datatable text-nowrap">
                      <table class="datatables-ajax-visit-order-detail table table-bordered">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Order Number</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th>Client Name</th>
                            <th>Site Name</th>
                            <th>Location</th>
                            <th>Partner Name</th>
                            <th>Download Status</th>
                            <th>Status</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
              </form>
              <!-- Detail -->

              <!-- Attachment -->
              <form id="formDetAttachment" enctype="multipart/form-data">
                <div id="attachment" class="content">
                  <div id="detFormLabel" class="text-center mb-4">
                    <h3><span>Attachment<span></h3>
                    <p>upload.</p>
                  </div>
                  <div class="card-body">
                    <div class="row g-4" id="payment-file">
                      
                    </div>
                  </div>
                </div>
              </form>
              <!-- End Attachment -->

              <!-- History -->
              <div id="history" class="content">
                <div id="editFormLabel" class="text-center mb-4">
                  <h3><span>History Partner Payment<span></h3>
                  <p>history.</p>
                </div>
                <div class="">
                  <ul class="timeline" id="history_partner_payment">
                    
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

<!-- Modal Paid Partner Payment -->
<div class="modal fade" id="modalPaidPartnerPayment" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="bs-stepper wizard-icons wizard-vertical-icons-example mt-2">
        <div class="bs-stepper-header">
          <div class="step" data-target="#edit">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle">
                <i class="bx bx-wallet"></i>
              </span>
              <span class="bs-stepper-label">Paid </span>
            </button>
          </div>
          <div class="line"></div>
          <div class="step" id="attach_button" data-target="#attach">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle">
                <i class="bx bx-paperclip"></i>
              </span>
              <span class="bs-stepper-label">Attachment </span>
            </button>
          </div>
        </div>
        <div class="bs-stepper-content">
          <!-- Paid -->
          <div id="edit" class="content">
            <form id="formPaidPartnerPayment" data-method="edit">
              <div id="addFormLabel" class="text-center mb-4">
                <h3><span>Partner Payment Form<span></h3>
                <p>Paid Partner Payment.</p>
              </div>
              <div class="m-4">
                <div class="row g-2">
                  <div class="col mb-3">
                      <label for="PaidNumber" class="form-label">Number</label>
                      <input type="text" id="PaidNumber" name="partner_payment_number" class="form-control" placeholder="Enter Number" @readonly(true) @disabled(true)>
                  </div>
                  <div class="col mb-3">
                      <label class="form-label" for="PaidName">Name <span style='color:red'>*</span></label>
                      <input type="text" id="PaidName" name="partner_payment_name" class="form-control" placeholder="Enter Name" @required(true) @disabled(true)>
                  </div>
                </div>
                <div class="row g-2">
                  <div class="col mb-3">
                      <label for="PaidMonth" class="form-label">Month <span style='color:red'>*</span></label>
                      <select class="form-select" id="PaidMonth" name='partner_payment_month' @disabled(true)>
                          @for ($i = 1; $i <= 12; $i++)
                              <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>{{ month_name($i) }}</option>
                          @endfor
                      </select>
                  </div>
                  <div class="col mb-3">
                      <label for="PaidYear" class="form-label">Year <span style='color:red'>*</span></label>
                      <select class="form-select" id="PaidYear" name='partner_payment_year' @disabled(true)>
                          @for ($i = date('Y'); $i <= date('Y') + 2; $i++)
                              <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
                          @endfor
                      </select>                  
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="PaidDesc" class="form-label">Description</label>
                    <textarea id="PaidDesc" name="partner_payment_desc" class="form-control" placeholder="Explanation about the new desc" rows="5" style="max-height: 100px;resize: none;" disabled></textarea>
                  </div>
                </div>
                <div class="card-datatable text-nowrap">
                    <table class="dt-paid-visit-order table table-bordered">
                      <thead>
                        <tr>
                          <th></th>
                          <th>Order Number</th>
                          <th>Date</th>
                          <th>Due Date</th>
                          <th>Client Name</th>
                          <th>Site Name</th>
                          <th>Location</th>
                          <th>Partner Name</th>
                          <th>Download Status</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                    </table>
                </div>
              </div>
              <div class="modal-footer">
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-money"></i> Paid</button>
              </div>
            </form>
          </div>
          <!-- End Paid -->

          <!-- Attachment -->
          <form id="formDetAttachment" enctype="multipart/form-data">
            <div id="attach" class="content">
              <div id="detFormLabel" class="text-center mb-4">
                <h3><span>Attachment<span></h3>
                <p>upload.</p>
              </div>
              <div class="card-body">
                <div class="row g-4" id="payment-file-paid">
                  
                </div>
              </div>
            </div>
          </form>
          <!-- End Attachment -->

        </div>
      </div>
      </div>

    </div>
  </div>
</div>
<!--/ Modal Paid Partner Payment -->

<!-- Modal Reject Partner Payment -->
<div class="modal fade" id="modalChangeStatusRejected" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <div class="modal-body">
          <form id="formChangeStatus" data-method="reject">
            <div id="rejectFormLabel" class="text-center mb-4">
              <h3><span id="title-header">Reject<span></h3>
              <p id="desc-header">reject.</p>
            </div>
            <div class="m-4">
              <div class="card-body">
                <div class="row g-2">
                  <div class="col mb-3">
                      <label for="rejectNumber" class="form-label">Number</label>
                      <h6 id="rejectNumber"></h6>
                  </div>
                  <div class="col mb-3">
                      <label class="form-label" for="rejectName">Name </label>
                      <h6 id="rejectName"></h6>
                  </div>
                </div>
                <div class="row g-2">
                  <div class="col mb-3">
                      <label for="rejectMonth" class="form-label">Month </label>
                      <h6 id="rejectMonth"></h6>
                  </div>
                  <div class="col mb-3">
                      <label for="rejectYear" class="form-label">Year </label>
                      <h6 id="rejectYear"></h6>                
                  </div>
                </div>
                <div class="col mb-3">
                  <label class="form-label" for="reject_desc">Desc <span style='color:red'>*</span></label>
                  <textarea id="reject_desc" rows="5" name="partner_payment_desc" class="form-control note-maxlength" maxlength="255" placeholder="Enter Desc" @required(true)></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Reject</button>
            </div>
          </form>
        </div>

    </div>
  </div>
</div>
<!--/ Modal Reject Partner Payment -->
@endsection