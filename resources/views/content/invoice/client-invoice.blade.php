@extends('layouts/contentNavbarLayout')

@section('title', 'Client Invoice')
@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
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
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/form-wizard-icons.js')}}"></script>
<script src="{{asset('assets/js/wizard-ex-create-deal.js')}}"></script>
<script src="{{asset('js/client/client-invoice.js')}}"></script>
@endsection

@section('content')
<!-- Data Table Client -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-user-voice bx-sm me-sm-2"></i>Client Invoice Table</h5>
    </div>
    <div class="text-end pt-3 pt-md-0">
      <button class="btn btn-primary fw-bold addButton" type="button" data-bs-toggle="modal" data-bs-target="#modalAddClientInvoice">
        <span><i class="tf-icons bx bx-plus-medical me-sm-2"></i> <span class="d-none d-sm-inline-block">Add Client Invoice</span></span>
      </button>
    </div>
  </div>
  <div class="card-datatable text-nowrap">
    <div class="d-flex justify-content-end pe-3">
      <div style="width: 25%">
        <input type="text" id="search" name="search" class="form-control search mt-3" placeholder="Search">
      </div>
    </div>
    <table class="datatables-ajax-client-invoice table table-hover">
      <thead>
        <tr>
          <th>No</th>
          <th>Number</th>
          <th>Name</th>
          <th>Site Name</th>
          <th>Month</th>
          <th>Year</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Data Table Client -->

<hr class="my-5">

<!-- Modal Add Client -->
<div class="modal fade" id="modalAddClientInvoice" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddClientInvoice" data-method="add">
        <div class="modal-body">
          <div id="addFormLabel" class="text-center mb-4">
            <h3><span>Client Invoice Form<span></h3>
            <p>Add Client Invoice.</p>
          </div>
          <div class="m-4">
            <div class="row g2">
                <div class="col mb-3">
                  <label for="addName" class="form-label">Name <span style='color:red'>*</span></label>
                  <input type="text" id="addName" name="client_invoice_name" class="form-control" placeholder="Enter Name" @required(true)>
                </div>
                <div class="col mb-3">
                  <label class="form-label" for="site_id">Site Name <span style='color:red'>*</span></label>
                  <select id="addSite" name="site_id" class="ac_site form-select" @required(true)>
                    <option value="">Select Site Name</option>
                  </select>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="year-month" class="col-md-2 col-form-label">Month <span style='color:red'>*</span></label>
                    <input class="form-control" type="month" id="year-month" name="year_month" />
                </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label class="form-label">File</label>
                <div class="dropzone needsclick dropzone-multi-file" id="add-file-attachment">
                  <div class="dz-message needsclick">
                    Drop files here or click to upload file
                    <span class="note needsclick">(multiple file and max size are 1024 KB/file, format: jpeg, png, and jpg)</span>
                  </div>
                  <div class="fallback">
                    <input name="client_invoice_file" type="file"/>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addDesc" class="form-label">Description</label>
                <textarea id="addDesc" name="client_invoice_desc" class="form-control" placeholder="Explanation about the new desc" rows="5" style="max-height: 100px;resize: none;"></textarea>
              </div>
            </div>
            <div class="card-datatable text-nowrap">
                <table class="datatables-ajax-visit-order table table-bordered">
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
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!--/ End Modal Add Client -->

<!-- Modal Edit Client Invoice -->
<div class="modal fade" id="modalEditClientInvoice" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="bs-stepper wizard-icons wizard-icons-example mt-2">
        <div class="bs-stepper-header">
          <div class="step" data-target="#edit">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle">
                <i class="bx bx-edit"></i>
              </span>
              <span class="bs-stepper-label">Edit </span>
            </button>
          </div>
          <div class="line"></div>
          <div class="step" id="attach_button" data-target="#attach">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle">
                <i class="bx bx-list-check"></i>
              </span>
              <span class="bs-stepper-label">Attachment </span>
            </button>
          </div>
        </div>
        <div class="bs-stepper-content">
          {{-- Detail --}}
          <div id="edit" class="content">
            <form id="formEditClientInvoice" data-method="edit">
              <div id="addFormLabel" class="text-center mb-4">
                <h3><span>Client Invoice Form<span></h3>
                <p>Edit Client Invoice.</p>
              </div>
              <div class="m-4">
                <div class="row g2">
                    <div class="col mb-3">
                      <label for="editName" class="form-label">Name <span style='color:red'>*</span></label>
                      <input type="text" id="editName" name="client_invoice_name" class="form-control" placeholder="Enter Name" @required(true)>
                    </div>
                    <div class="col mb-3">
                      <label class="form-label" for="site_id">Site Name <span style='color:red'>*</span></label>
                      <select id="editSite" name="site_id" class="ac_site_edit form-select" @required(true)>
                        <option value="">Select Site Name</option>
                      </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="year-month" class="col-md-2 col-form-label">Month <span style='color:red'>*</span></label>
                        <input class="form-control" type="month" id="edit-year-month" name="year_month" />
                    </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="editDesc" class="form-label">Description</label>
                    <textarea id="editDesc" name="client_invoice_desc" class="form-control" placeholder="Explanation about the new desc" rows="5" style="max-height: 100px;resize: none;"></textarea>
                  </div>
                </div>
                <div class="card-datatable text-nowrap">
                    <table class="datatables-ajax-visit-order-edit table table-bordered">
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
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
            </form>
          </div>
          {{-- End Detail --}}

          {{-- Image --}}
          <div id="attach" class="content">
            <div id="editFormLabel" class="text-center mb-4">
              <h3><span>Image Client Invoice<span></h3>
              <p>image.</p>
            </div>
            <form id="formEditUploadFileClientInvoice">
              <div class="m-4">

                <div class="row">
                  <div class="col mb-3">
                    <label class="form-label">File</label>
                    <div class="dropzone needsclick dropzone-multi-file" id="edit-file-attachment">
                      <div class="dz-message needsclick">
                        Drop files here or click to upload file
                        <span class="note needsclick">(multiple file and max size are 1024 KB/file, format: jpeg, png, and jpg)</span>
                      </div>
                      <div class="fallback">
                        <input name="client_invoice_file" type="file"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="editDesc" class="form-label">Description</label>
                    <textarea id="editDesc" name="client_invoice_desc" class="form-control" placeholder="Explanation about the new desc" rows="5" style="max-height: 100px;resize: none;"></textarea>
                  </div>
                </div>
                <div class="card-datatable text-nowrap">
                  <table class="datatables-ajax-client-invoice-file-edit table table-bordered">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Client Invoice</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                  </table>
              </div>

              </div>
              <div class="modal-footer">
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
            </form>
          </div>
          {{-- End Image --}}

        </div>
      </div>
      </div>

    </div>
  </div>
</div>
<!--/ Modal Edit Client Invoice -->

<!-- Modal Paid Client Invoice -->
<div class="modal fade" id="modalPaidClientInvoice" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="bs-stepper wizard-icons wizard-icons-example mt-2">
        <div class="bs-stepper-header">
          <div class="step" data-target="#edit">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle">
                <i class="bx bx-edit"></i>
              </span>
              <span class="bs-stepper-label">Paid </span>
            </button>
          </div>
          <div class="line"></div>
          <div class="step" id="attach_button" data-target="#attach">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle">
                <i class="bx bx-list-check"></i>
              </span>
              <span class="bs-stepper-label">Attachment </span>
            </button>
          </div>
        </div>
        <div class="bs-stepper-content">
          {{-- Detail --}}
          <div id="edit" class="content">
            <form id="formPaidClientInvoice" data-method="edit">
              <div id="addFormLabel" class="text-center mb-4">
                <h3><span>Client Invoice Form<span></h3>
                <p>Paid Client Invoice.</p>
              </div>
              <div class="m-4">
                <div class="row g2">
                    <div class="col mb-3">
                      <label for="paidName" class="form-label">Name <span style='color:red'>*</span></label>
                      <input type="text" id="paidName" name="client_invoice_name" class="form-control" placeholder="Enter Name" @required(true) disabled>
                    </div>
                    <div class="col mb-3">
                      <label class="form-label" for="paidSite">Site Name <span style='color:red'>*</span></label>
                      <select id="paidSite" name="site_id" class="ac_site_paid form-select" @required(true) disabled>
                        <option value="">Select Site Name</option>
                      </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="paid-year-month" class="col-md-2 col-form-label">Month <span style='color:red'>*</span></label>
                        <input class="form-control" type="month" id="paid-year-month" name="year_month" disabled/>
                    </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="paidDesc" class="form-label">Description</label>
                    <textarea id="paidDesc" name="client_invoice_desc" class="form-control" placeholder="Explanation about the new desc" rows="5" style="max-height: 100px;resize: none;" disabled></textarea>
                  </div>
                </div>
                <div class="card-datatable text-nowrap">
                    <table class="datatables-ajax-visit-order-paid table table-bordered">
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
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
            </form>
          </div>
          {{-- End Detail --}}

          {{-- Image --}}
          <div id="attach" class="content">
            <div id="editFormLabel" class="text-center mb-4">
              <h3><span>Image Client Invoice<span></h3>
              <p>image.</p>
            </div>
            <div class="m-4">

              <div class="card-datatable text-nowrap">
                <table class="datatables-ajax-client-invoice-file-paid table table-bordered">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Client Invoice</th>
                      <th>Description</th>
                      <th>Image</th>
                    </tr>
                  </thead>
                </table>
            </div>

            </div>
          </div>
          {{-- End Image --}}

        </div>
      </div>
      </div>

    </div>
  </div>
</div>
<!--/ Modal Paid Client Invoice -->

<!-- Modal Detail Client Invoice -->
<div class="modal fade" id="modalDetailClientInvoice" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="bs-stepper wizard-icons wizard-icons-example mt-2">
        <div class="bs-stepper-header">
          <div class="step" data-target="#detail">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle">
                <i class="bx bx-detail"></i>
              </span>
              <span class="bs-stepper-label">Data </span>
            </button>
          </div>
          <div class="line"></div>
          <div class="step" data-target="#image">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle">
                <i class="bx bx-list-check"></i>
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
          {{-- Detail --}}
          <div id="detail" class="content">
            <div id="editFormLabel" class="text-center mb-4">
              <h3><span>Data Client Invoice<span></h3>
              <p>data.</p>
            </div>
              <div class="row g-2">
                <div class="col mb-3">
                  <label class="form-label" for="edit_number">Number</label>
                  <h6 id="edit_number"></h6>
                </div>
                <div class="col mb-3">
                  <label class="form-label" for="edit_name">Name</label>
                  <h6 id="edit_name"></h6>
                </div>
              </div>
              <div class="row g-2">
                <div class="col mb-3">
                  <label for="edit_month" class="form-label">Month</label>
                  <h6 id="edit_month"></h6>
                </div>
                <div class="col mb-3">
                  <label for="edit_year" class="form-label">Year</label>
                  <h6 id="edit_year"></h6>
                </div>
              </div>
              <div class="row g-2">
                  <div class="col mb-3">
                    <label class="form-label" for="edit_site_name">Site Name</label>
                    <h6 id="edit_site_name"></h6>
                  </div>
                  <div class="col mb-3">
                    <label class="form-label" for="edit_desc">Desc</label>
                    <h6 id="edit_desc"></h6> 
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
          {{-- End Detail --}}

          {{-- Image --}}
          <div id="image" class="content">
            <div id="editFormLabel" class="text-center mb-4">
              <h3><span>Attachment Client Invoice<span></h3>
              <p>attachment.</p>
            </div>
            <div class="">
              <div class="card-datatable text-nowrap">
                <table class="datatables-ajax-client-invoice-file-detail table table-bordered">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Client Invoice</th>
                      <th>Description</th>
                      <th>Image</th>
                    </tr>
                  </thead>
                </table>
            </div>
            </div>
          </div>
          {{-- End Image --}}

          {{-- History --}}
          <div id="history" class="content">
            <div id="editFormLabel" class="text-center mb-4">
              <h3><span>History Client Invoice<span></h3>
              <p>history.</p>
            </div>
            <div class="">
              <ul class="timeline" id="history_client_invoice">
                
              </ul>
            </div>
          </div>
          {{-- End history --}}

        </div>
      </div>
      </div>

    </div>
  </div>
</div>
<!--/ Modal Detail Client Invoice -->

@endsection