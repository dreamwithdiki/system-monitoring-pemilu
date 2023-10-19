@extends('layouts/contentNavbarLayout')

@section('title', 'Caleg - Master Data')
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
<script src="{{asset('js/pemilu/master-data/caleg.js')}}"></script>
<script>
  function hanyaAngka(event) {
    // Mendapatkan kode tombol yang ditekan
    var charCode = (event.which) ? event.which : event.keyCode;
    
    // Memastikan hanya karakter angka yang diperbolehkan
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
      return false;
    }
    
    // Memeriksa panjang input
    var input = event.target.value;
    if (input.length >= 16) {
      return false;
    }
    
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


<!-- Data Table Caleg -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-user-voice bx-sm me-sm-2"></i>Caleg Table</h5>
    </div>
    <div class="text-end pt-3 pt-md-0">
      <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#modalAddCaleg">
        <span><i class="tf-icons bx bx-plus-medical me-sm-2"></i> <span class="d-none d-sm-inline-block">Add Caleg</span></span>
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
          <th>Foto</th>
          <th>NIK</th>
          <th>Nama</th>
          <th>Provinsi</th>
          <th>Kabupaten</th>
          <th>Kecamatan</th>
          <th>Kelurahan</th>
          <th>Visi & Misi</th>
          <th>Nama Partai</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Data Table Caleg -->

<hr class="my-5">

<!-- Modal Add Caleg -->
<div class="modal fade" id="modalAddCaleg" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddCaleg" data-method="add">
        <div class="modal-body">
          <div id="addFormLabel" class="text-center mb-4">
            <h3><span>Caleg Form<span></h3>
            <p>Add new Caleg.</p>
          </div>
          <div class="m-4">
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="addStatus">Status <span style='color:red'>*</span></label>
                <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                  <label class="switch switch-primary">
                    <input id="addStatus" name="caleg_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
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
                <input type="text" id="addNik" name="caleg_nik" class="form-control" placeholder="NIK" onkeypress="return hanyaAngka(event)" max="16">
              </div>
              <div class="col mb-3">
                <label for="addName" class="form-label">Nama <span style='color:red'>*</span></label>
                <input type="text" id="addName" name="caleg_name" class="form-control" placeholder="Enter Name" >
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="addProvince">Provinsi <span style='color:red'>*</span></label>
                <select id="addProvince" name="caleg_province" class="ac_province form-select">
                  <option value="">Select Province Name</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="addRegency">Kabupaten <span style='color:red'>*</span></label>
                <select id="addRegency" name="caleg_regency" class="ac_regency form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="addDistrict">Kecamatan <span style='color:red'>*</span></label>
                <select id="addDistrict" name="caleg_district" class="ac_district form-select">
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="addVillage">Kelurahan <span style='color:red'>*</span></label>
                <select id="addVillage" name="caleg_village" class="ac_village form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="bs-validation-upload-file">Photo Caleg</label>
                <input type="file" class="form-control" name="caleg_photo" id="bs-validation-upload-file"/>
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="bs-validation-upload-file">Photo Partai</label>
                <input type="file" class="form-control" name="caleg_photo_partai" id="bs-validation-upload-file"/>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addVisiMisi" class="form-label">Visi & Misi <span style='color:red'>*</span></label>
                <textarea id="addVisiMisi" name="caleg_visi_misi" class="form-control" placeholder="Explanation about the new visi & misi" rows="5" style="max-height: 100px;resize: none;"></textarea>
              </div>
            </div>
            <div class="row g2">
                <div class="col mb-3">
                  <label for="addNamaPartai" class="form-label">Nama Partai <span style='color:red'>*</span></label>
                  <input type="text" id="addNamaPartai" name="caleg_nama_partai" class="form-control" placeholder="Enter Nama Partai" >
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
<!--/ Modal Add Caleg -->

<!-- Modal Edit Caleg -->
<div class="modal fade" id="modalEditCaleg" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditCaleg" data-method="add">
        <div class="modal-body">
          <div id="editFormLabel" class="text-center mb-4">
            <h3><span>Caleg Form<span></h3>
            <p>Add edit Caleg.</p>
          </div>
          <div class="m-4">
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="editStatus">Status <span style='color:red'>*</span></label>
                <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                  <label class="switch switch-primary">
                    <input id="editStatus" name="caleg_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
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
            <div class="row g-2">
              <div class="col mb-3">
                <label for="editNIK" class="form-label">NIK <span style='color:red'>*</span></label>
                <input type="text" id="editNIK" name="caleg_nik" class="form-control" placeholder="Enter NIK" @required(true)>
              </div>
              <div class="col mb-3">
                <label for="editName" class="form-label">Nama <span style='color:red'>*</span></label>
                <input type="text" id="editName" name="caleg_name" class="form-control" placeholder="Enter Name" @required(true)>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <input type="hidden" id="oldImage" name="oldImage" value="">
                <label class="form-label">Current Photo</label>
                <div class="col-md-3">
                    <img src="#" class="rounded current-photo" style="max-width: 100%; height: auto;">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="caleg_photo">Change Photo</label>
                <input type="file" class="form-control" id="caleg_photo" name="caleg_photo"/>
                <br>
                <div id="imagePreview"></div>
              </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label class="form-label" for="editProvince">Provinsi <span style='color:red'>*</span></label>
                <select id="editProvince" name="caleg_province" class="form-select" @required(true)>
                  <option value="">Select Province Name</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="editRegency">Kabupaten <span style='color:red'>*</span></label>
                <select id="editRegency" name="caleg_regency" class="form-select" @required(true)>
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label class="form-label" for="editDistrict">Kecamatan <span style='color:red'>*</span></label>
                <select id="editDistrict" name="caleg_district" class="form-select">
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="editVillage">Kelurahan <span style='color:red'>*</span></label>
                <select id="editVillage" name="caleg_village" class="form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="editVisiMisi" class="form-label">Visi & Misi <span style='color:red'>*</span></label>
                    <textarea id="editVisiMisi" name="caleg_visi_misi" class="form-control" placeholder="Explanation about the new visi & misi" rows="5" style="max-height: 100px;resize: none;"></textarea>
                </div>
            </div>
            <div class="row g2">
                <div class="col mb-3">
                    <label for="editNamaPartai" class="form-label">Nama Partai <span style='color:red'>*</span></label>
                    <input type="text" id="editNamaPartai" name="caleg_nama_partai" class="form-control" placeholder="Enter Nama Partai" >
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
<!--/ Modal Edit Caleg -->
@endsection