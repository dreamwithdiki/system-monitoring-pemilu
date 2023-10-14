@extends('layouts/contentNavbarLayout')

@section('title', 'Client - Master Data')
@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
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
@endsection

@section('page-script')
<script src="{{asset('js/site-visit/master-data/client.js')}}"></script>
@endsection

@section('content')
<!-- Data Table Client -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-user-voice bx-sm me-sm-2"></i>Client Table</h5>
    </div>
    <div class="text-end pt-3 pt-md-0">
      <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#modalAddClient">
        <span><i class="tf-icons bx bx-plus-medical me-sm-2"></i> <span class="d-none d-sm-inline-block">Add Client</span></span>
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
          <th>Code</th>
          <th>Name</th>
          <th>Adress</th>
          <th>Total Order</th>
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
<div class="modal fade" id="modalAddClient" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddClient" data-method="add">
        <div class="modal-body">
          <div id="addFormLabel" class="text-center mb-4">
            <h3><span>Client Form<span></h3>
            <p>Add new client.</p>
          </div>
          <div class="m-4">
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="addStatus">Status <span style='color:red'>*</span></label>
                <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                  <label class="switch switch-primary">
                    <input id="addStatus" name="client_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
                    <span class="switch-toggle-slider">
                      <span class="switch-on">
                        <i class="bx bx-check"></i>
                      </span>
                      <span class="switch-off">
                        <i class="bx bx-x"></i>
                      </span>
                    </span>
                  </label>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <label class="form-label">Param</label>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-basic">
                    <label class="form-check-label custom-option-content" id="addParamCorporate">
                      <input class="form-check-input" type="radio" name="client_param" id="addParamCorporate" value="1">
                      <span class="custom-option-header">
                        <span class="h6 mb-0">Corporate</span>
                      </span>
                      <span class="custom-option-body">
                        <small>For Corporate</small>
                      </span>
                    </label>
                  </div>
                </div>
                <div class="col-md">
                  <div class="form-check custom-option custom-option-basic">
                    <label class="form-check-label custom-option-content" id="addParamIndividu">
                      <input class="form-check-input" type="radio" name="client_param" id="addParamIndividu" value="2">
                      <span class="custom-option-header">
                        <span class="h6 mb-0">Individu</span>
                      </span>
                      <span class="custom-option-body">
                        <small>For Individu</small>
                      </span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addCode" class="form-label">Code <span style='color:red'>*</span></label>
                <input type="text" id="addCode" name="client_code" class="form-control" placeholder="Enter Code" @required(true)>
              </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label for="addName" class="form-label">Name <span style='color:red'>*</span></label>
                <input type="text" id="addName" name="client_name" class="form-control" placeholder="Enter Name" @required(true)>
              </div>
              <div class="col mb-3">
                <label for="addPhone" class="form-label">Phone</label>
                <input type="text" id="addPhone" name="client_phone" class="form-control" placeholder="Enter Phone" onkeypress="return hanyaAngka(event)">
              </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label for="addFax" class="form-label">Fax</label>
                <input type="text" id="addFax" name="client_fax" class="form-control" placeholder="Enter Fax" onkeypress="return hanyaAngka(event)">
              </div>
              <div class="col mb-3">
                <label for="addEmail" class="form-label">Email</label>
                <input type="email" id="addEmail" name="client_email" class="form-control" placeholder="Enter Email" @required(true) >
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addAddress" class="form-label">Adress</label>
                <textarea id="addAddress" name="client_address" class="form-control" placeholder="Enter detail address" rows="5" style="max-height: 100px;resize: none;"></textarea>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addDesc" class="form-label">Description</label>
                <textarea id="addDesc" name="client_desc" class="form-control" placeholder="Explanation about the new desc" rows="5" style="max-height: 100px;resize: none;"></textarea>
              </div>
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
<!--/ Modal Add Client -->

<script>
  // Check selected custom option
  window.Helpers.initCustomOptionCheck();

  // untuk karakter hanya angka.
  function hanyaAngka(evt) {
      var charCode = (evt.which) ? evt.which : event.keyCode
      if (charCode > 31 && (charCode < 48 || charCode > 57))

          return false;
      return true;
  }
</script>
@endsection