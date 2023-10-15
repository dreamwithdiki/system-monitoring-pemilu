@extends('layouts/contentNavbarLayout')

@section('title', 'DPT - Pendukung')
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
<script src="{{asset('js/pendukung/dpt.js')}}"></script>
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

@section('content')
<style>
  #imagePreview {
      width: 150px;
      height: 150px;
      background-position: center center;
      background-size: cover;
      -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, .3);
      display: inline-block;
  }
</style>


<!-- Data Table DPT -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-id-card bx-sm me-sm-2"></i>DPT Table</h5>
    </div>
    <div class="text-end pt-3 pt-md-0">
      <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#modalAddDpt">
        <span><i class="tf-icons bx bx-plus-medical me-sm-2"></i> <span class="d-none d-sm-inline-block">Add DPT</span></span>
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
          <th>NIK</th>
          <th>Nama</th>
          <th>Jenis Kelamin</th>
          <th>Provinsi</th>
          <th>Kabupaten</th>
          <th>Kecamatan</th>
          <th>Kelurahan</th>
          <th>TPS</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Data Table DPT -->

<hr class="my-5">

<!-- Modal Add DPT -->
<div class="modal fade" id="modalAddDpt" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddDpt" data-method="add">
        <div class="modal-body">
          <div id="addFormLabel" class="text-center mb-4">
            <h3><span>DPT Form<span></h3>
            <p>Add new DPT.</p>
          </div>
          <div class="m-4">
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="addStatus">Status <span style='color:red'>*</span></label>
                <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                  <label class="switch switch-primary">
                    <input id="addStatus" name="dpt_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
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
            <div class="row g2">
              <div class="col mb-3">
                <label for="addNik" class="form-label">NIK <span style='color:red'>*</span></label>
                <input type="text" id="addNik" name="dpt_nik" class="form-control" placeholder="Enter NIK" >
              </div>
              <div class="col mb-3">
                <label for="addNama" class="form-label">Nama <span style='color:red'>*</span></label>
                <input type="text" id="addNama" name="dpt_name" class="form-control" placeholder="Enter Name" >
              </div>
            </div>
            <div class="card-body">
                <div class="row">
                  <label class="form-label">Jenis Kelamin</label>
                  <div class="col-md mb-md-0 mb-2">
                    <div class="form-check custom-option custom-option-basic">
                      <label class="form-check-label custom-option-content" id="addParamMan">
                        <input class="form-check-input" type="radio" name="dpt_jenkel" id="addParamMan" value="1">
                        <span class="custom-option-header">
                          <span class="h6 mb-0">Laki-Laki</span>
                        </span>
                        <span class="custom-option-body">
                          <small>For Man</small>
                        </span>
                      </label>
                    </div>
                  </div>
                  <div class="col-md">
                    <div class="form-check custom-option custom-option-basic">
                      <label class="form-check-label custom-option-content" id="addParamWoman">
                        <input class="form-check-input" type="radio" name="dpt_jenkel" id="addParamWoman" value="2" @checked(true)>
                        <span class="custom-option-header">
                          <span class="h6 mb-0">Perempuan</span>
                        </span>
                        <span class="custom-option-body">
                          <small>For Woman</small>
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="addProvince">Provinsi <span style='color:red'>*</span></label>
                <select id="addProvince" name="dpt_province" class="ac_province form-select">
                  <option value="">Select Province Name</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="addRegency">Kabupaten <span style='color:red'>*</span></label>
                <select id="addRegency" name="dpt_regency" class="ac_regency form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="addDistrict">Kecamatan <span style='color:red'>*</span></label>
                <select id="addDistrict" name="dpt_district" class="ac_district form-select">
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="addVillage">Kelurahan <span style='color:red'>*</span></label>
                <select id="addVillage" name="dpt_village" class="ac_village form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label" for="tps_id">TPS <span style='color:red'>*</span></label>
                    <select id="addTps" name="tps_id" class="ac_tps form-select" @required(true)>
                        <option value="">Select TPS</option>
                    </select>
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
<!--/ Modal Add DPT -->

<!-- Modal Edit DPT -->
<div class="modal fade" id="modalEditDpt" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditDpt" data-method="add">
        <div class="modal-body">
          <div id="editFormLabel" class="text-center mb-4">
            <h3><span>DPT Form<span></h3>
            <p>Add edit DPT.</p>
          </div>
          <div class="m-4">
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="editStatus">Status <span style='color:red'>*</span></label>
                <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                  <label class="switch switch-primary">
                    <input id="editStatus" name="dpt_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
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
            <div class="row g2">
                <div class="col mb-3">
                  <label for="editNik" class="form-label">NIK <span style='color:red'>*</span></label>
                  <input type="text" id="editNik" name="dpt_nik" class="form-control" placeholder="Enter NIK" @required(true)>
                </div>
                <div class="col mb-3">
                  <label for="editName" class="form-label">Nama <span style='color:red'>*</span></label>
                  <input type="text" id="editName" name="dpt_name" class="form-control" placeholder="Enter Name" @required(true)>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                  <label class="form-label">Jenis Kelamin</label>
                  <div class="col-md mb-md-0 mb-2">
                    <div class="form-check custom-option custom-option-basic">
                      <label class="form-check-label custom-option-content" id="editParamMan">
                        <input class="form-check-input" type="radio" name="dpt_jenkel" id="editParamMan" value="1">
                        <span class="custom-option-header">
                          <span class="h6 mb-0">Laki-Laki</span>
                        </span>
                        <span class="custom-option-body">
                          <small>For Man</small>
                        </span>
                      </label>
                    </div>
                  </div>
                  <div class="col-md">
                    <div class="form-check custom-option custom-option-basic">
                      <label class="form-check-label custom-option-content" id="editParamWoman">
                        <input class="form-check-input" type="radio" name="dpt_jenkel" id="editParamWoman" value="2" @checked(true)>
                        <span class="custom-option-header">
                          <span class="h6 mb-0">Perempuan</span>
                        </span>
                        <span class="custom-option-body">
                          <small>For Woman</small>
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label class="form-label" for="editProvince">Provinsi <span style='color:red'>*</span></label>
                <select id="editProvince" name="dpt_province" class="form-select" @required(true)>
                  <option value="">Select Province Name</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="editRegency">Kabupaten <span style='color:red'>*</span></label>
                <select id="editRegency" name="dpt_regency" class="form-select" @required(true)>
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label class="form-label" for="editDistrict">Kecamatan <span style='color:red'>*</span></label>
                <select id="editDistrict" name="dpt_district" class="form-select">
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="editVillage">Kelurahan <span style='color:red'>*</span></label>
                <select id="editVillage" name="dpt_village" class="form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label" for="tps_id">TPS <span style='color:red'>*</span></label>
                    <select id="editTps" name="tps_id" class="ac_edit_tps form-select" @required(true)>
                        <option value="">Select TPS</option>
                    </select>
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
<!--/ Modal Edit DPT -->
@endsection