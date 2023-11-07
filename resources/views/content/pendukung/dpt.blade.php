@extends('layouts/contentNavbarLayout')

@section('title', 'DPT - Pendukung')
@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css')}}" />
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
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('js/pendukung/dpt.js')}}"></script>
<script>
  // Check selected custom option
  window.Helpers.initCustomOptionCheck();

  // untuk karakter hanya angka.
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

<script>
  $('.btn_download').on('click', function(event) {
      event.preventDefault();
      Swal.fire({
          title: "Download Template?",
          text: "Download template import data DPT",
          icon: "info",
          showCancelButton: true,
          confirmButtonClass: "btn btn-info w-md mt-2",
          cancelButtonClass: "btn btn-danger w-md mt-2",
          cancelButtonColor: "#f46a6a",
          confirmButtonText: "Ya",
          cancelButtonText: "Tidak",
          closeOnConfirm: false,
          closeOnCancel: false
      }).then(function(t) {
          if (t.value) {
              Swal.fire({
                  title: "Download Berhasil !",
                  text: "Template Data DPT",
                  confirmButtonColor: "#34c38f",
                  icon: "success"
              }).then(function(t) {
                  window.location.href = "{{ asset('assets/import/example_format_data_dpt.xlsx') }}"
              })
          } else if (t.dismiss === Swal.DismissReason.cancel) {
              Swal.fire({
                  title: "Download Dibatalkan",
                  text: "Template DPT batal di download :(",
                  showConfirmButton: false,
                  timer: 2000,
                  icon: "warning"
              });
          }
      });
  });
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

      @if(session('role_id') == 1 || session('role_id') == 2 || session('role_id') == 3)
      <button class="btn btn-outline-success btn-sm btn-responsive px-3 waves-light waves-effect btn_download"><span><i class="tf-icons fa fa-download me-sm-2"></i><span class="d-none d-sm-inline-block"> Download template Excel</span></span></button>
      <button class="btn btn-outline-info btn-sm btn-responsive px-3 waves-light waves-effect" data-bs-toggle="modal" data-animation="bounce" data-bs-target=".modal-import"><span><i class="tf-icons fa fa-file-excel me-sm-2"></i><span class="d-none d-sm-inline-block"> Import</span></span></button>
      @endif
      
      @if(session('role_id') == 1)
      <a id="dropdownMenuExcel" href="/pendukung/dpt/excel" class="btn btn-success btn-sm"><span><i class="tf-icons fa fa-file-excel me-sm-2"></i> <span class="d-none d-sm-inline-block">Print Excel</span></span></a>
      @endif

      @if(session('role_id') == 1 || session('role_id') == 2 || session('role_id') == 3 || session('role_id') == 4)
      <button class="btn btn-primary fw-bold btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#modalAddDpt">
        <span><i class="tf-icons bx bx-plus-medical me-sm-2"></i> <span class="d-none d-sm-inline-block">Add DPT</span></span>
      </button>
      @endif
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
          <th>Alamat</th>
          <th>RT</th>
          <th>RW</th>
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
              <div class="col-xl mb-3">
                <label for="addNik" class="form-label">NIK <span style='color:red'>*</span></label>
                <input type="text" id="addNik" name="dpt_nik" class="form-control" placeholder="Enter NIK" onkeypress="return hanyaAngka(event)" max="16" @required(true) >
              </div>
              <div class="col-xl mb-3">
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
              <div class="col-md-12">
                <label class="form-label" for="dpt_address">Alamat <span style='color:red'>*</span></label>
                <textarea id="autosize-address" rows="5" name="dpt_address" class="form-control address-maxlength" maxlength="255"></textarea>
              </div>
            </div>
            <div class="row g2">
              <div class="col-xl mb-3">
                <label for="addRT" class="form-label">RT <span style='color:red'>*</span></label>
                <input type="number" id="addRT" name="dpt_rt" class="form-control" placeholder="Enter RT" min="0" max="999" @required(true) >
              </div>
              <div class="col-xl mb-3">
                <label for="addRW" class="form-label">RW <span style='color:red'>*</span></label>
                <input type="number" id="addRW" name="dpt_rw" class="form-control" placeholder="Enter RW" min="0" max="999" @required(true)>
              </div>
            </div>
            <div class="row g2">
              <div class="col-xl mb-3">
                <label class="form-label" for="addProvince">Provinsi <span style='color:red'>*</span></label>
                <select id="addProvince" name="dpt_province" class="ac_province form-select">
                  <option value="">Select Province Name</option>
                </select>
              </div>
              <div class="col-xl mb-3">
                <label class="form-label" for="addRegency">Kabupaten <span style='color:red'>*</span></label>
                <select id="addRegency" name="dpt_regency" class="ac_regency form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g2">
              <div class="col-xl mb-3">
                <label class="form-label" for="addDistrict">Kecamatan <span style='color:red'>*</span></label>
                <select id="addDistrict" name="dpt_district" class="ac_district form-select">
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col-xl mb-3">
                <label class="form-label" for="addVillage">Kelurahan <span style='color:red'>*</span></label>
                <select id="addVillage" name="dpt_village" class="ac_village form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row">
                <div class="col-xl mb-3">
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
                <div class="col-xl mb-3">
                  <label for="editNik" class="form-label">NIK <span style='color:red'>*</span></label>
                  <input type="text" id="editNik" name="dpt_nik" class="form-control" placeholder="Enter NIK" onkeypress="return hanyaAngka(event)" max="16" @required(true)>
                </div>
                <div class="col-xl mb-3">
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
                        <input class="form-check-input" type="radio" name="dpt_jenkel" id="editParamWoman" value="2">
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
            <div class="row gy-3">
              <div class="col-md-12">
                <label class="form-label" for="dpt_address">Alamat <span style='color:red'>*</span></label>
                <textarea id="edit-autosize-address" rows="5" name="dpt_address" class="form-control address-maxlength" maxlength="255"></textarea>
              </div>
            </div>
            <div class="row g2">
              <div class="col-xl mb-3">
                <label for="editRT" class="form-label">RT <span style='color:red'>*</span></label>
                <input type="number" id="editRT" name="dpt_rt" class="form-control" placeholder="Enter RT" min="0" max="999" @required(true) >
              </div>
              <div class="col-xl mb-3">
                <label for="editRW" class="form-label">RW <span style='color:red'>*</span></label>
                <input type="number" id="editRW" name="dpt_rw" class="form-control" placeholder="Enter RW" min="0" max="999" @required(true)>
              </div>
            </div>
            <div class="row g-2">
              <div class="col-xl mb-3">
                <label class="form-label" for="editProvince">Provinsi <span style='color:red'>*</span></label>
                <select id="editProvince" name="dpt_province" class="form-select" @required(true)>
                  <option value="">Select Province Name</option>
                </select>
              </div>
              <div class="col-xl mb-3">
                <label class="form-label" for="editRegency">Kabupaten <span style='color:red'>*</span></label>
                <select id="editRegency" name="dpt_regency" class="form-select" @required(true)>
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g-2">
              <div class="col-xl mb-3">
                <label class="form-label" for="editDistrict">Kecamatan <span style='color:red'>*</span></label>
                <select id="editDistrict" name="dpt_district" class="form-select">
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col-xl mb-3">
                <label class="form-label" for="editVillage">Kelurahan <span style='color:red'>*</span></label>
                <select id="editVillage" name="dpt_village" class="form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row">
                <div class="col-xl mb-3">
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

<!-- Modal Detail DPT -->
<div class="modal fade" id="modalDetailDpt" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formDetailDpt" data-method="detail">
          <div id="detFormLabel" class="text-center mb-4">
            <h3><span>Detail DPT<span></h3>
            <p>detail.</p>
          </div>
          <div class="m-4">
            <div class="row g-2">
                <div class="col-xl mb-3">
                  <label class="form-label" for="detNik">NIK</label>
                  <h6 id="detNik"></h6>
                </div>
                <div class="col-xl mb-3">
                  <label class="form-label" for="detNama">Nama</label>
                  <h6 id="detNama"></h6>
                </div>
            </div>
            <div class="row g-2">
              <div class="col-xl mb-3">
                <label for="detJenkel" class="form-label">Jenis Kelamin</label>
                <h6 id="detJenkel"></h6>
              </div>
              <div class="col-xl mb-3">
                <label for="detTps" class="form-label">TPS</label>
                <h6 id="detTps"></h6>
              </div>
            </div>
            <div class="row g-2">
              <div class="col-xl mb-3">
                <label for="detRT" class="form-label">RT (Rukun Tetangga)</label>
                <h6 id="detRT"></h6>
              </div>
              <div class="col-xl mb-3">
                <label for="detRW" class="form-label">RW (Rukun Warga)</label>
                <h6 id="detRW"></h6>
              </div>
            </div>
            <div class="row">
              <div class="col-xl mb-3">
                <label for="detAddress" class="form-label">Alamat</label>
                <h6 id="detAddress"></h6>
              </div>
            </div>
            <div class="row g-2">
                <div class="col-xl mb-3">
                  <label class="form-label" for="detProvince">Provinsi</label>
                  <h6 id="detProvince"></h6>
                </div>
                <div class="col-xl mb-3">
                  <label class="form-label" for="detRegency">Kabupaten</label>
                  <h6 id="detRegency"></h6>
                </div>
            </div>
            <div class="row g-2">
              <div class="col-xl mb-3">
                <label class="form-label" for="detDistrict">Kecamatan</label>
                <h6 id="detDistrict"></h6>
              </div>
              <div class="col-xl mb-3">
                <label class="form-label" for="detVillage">kelurahan</label>
                <h6 id="detVillage"></h6>
              </div>
             </div>
             <div class="row">
              <div class="col-xl mb-3">
                <label class="form-label" for="detStatus">Status</label> 
                <h6 id="detStatus"></h6>
              </div>
              <div class="col-xl mb-3">
                <label class="form-label" for="detCreatedBy">Created By</label> 
                <h6 id="detCreatedBy"></h6>
              </div>
             </div>
        
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
<!--/ Modal Detail DPT -->

<!-- Modal Import Excel DPT -->
<div class="modal fade modal-import" id="modalImportDpt" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm">
      <div class="modal-content modal-block-loader">
          <div class="modal-header text-white bg-primary">
              <h5 class="modal-title align-self-center text-white" id="myImportModalLabel">Import format xls,xlsx,csv,ods</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formImportDpt" data-method="import">
                <div class="form-group">
                  <div class="col-xl mb-3">
                    <label for="import_excel">Choose file</label>
                    <input type="file" class="form-control" id="file" name="file">
                  </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100 mt-3" id="import_excel_coy">Import</button>
            </form>
          </div>
      </div>
  </div>
</div>

<!--/ Modal Import Excel DPT -->
@endsection