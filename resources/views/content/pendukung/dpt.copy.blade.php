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
                <input type="text" id="addNik" name="dpt_nik" class="form-control" placeholder="Enter NIK" onkeypress="return hanyaAngka(event)" max="16" @required(true) >
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
            <div class="row mt-3">
              <div class="col-xl mb-3">
                <label for="addChecklistKecamatan" class="form-label">Daerah Pemilihan <span style='color:red'>*</span></label>
                <div class="row" style="padding-left: 15px; padding-right: 15px">
                  @php 
                  $tmp_group_id = 'x';
                  $tmp_group_kec_count = 0;

                  foreach($ceklisKecamatan as $key => $check){
                    if($check->kecamatan_id!=$tmp_group_id){
                      if($tmp_group_kec_count==0||$tmp_group_kec_count % 2==0){
                              echo '</div><div class="row" style="padding-left: 15px; padding-right: 15px">';
                          }
                          if ($tmp_group_kec_count > 0) {
                              echo '</div>';
                          }

                          echo '<div class="col mb-6 kecamatan_type">';
                          $tmp_group_id = $check->kecamatan_id;
                          $tmp_group_kec_count++;
                      }

                      echo '
                          <div class="form-check form-check-primary mt-3">
                              <input class="form-check-input checkbox-item-modal-add-kecamatan checkbox-item" type="checkbox" onClick="_check_kecamatan_checkbox_item(this);" name="kecamatan_type[]" id="kecamatan_type_'.$check->kecamatan_id.'" value="'.$check->kecamatan_id.'" required>
                              <label class="form-check-label" for="kecamatan_type_'.$check->kecamatan_id.'">'.$check->kecamatan_name.'</label>
                          </div>
                      ';
                  }

                  if ($tmp_group_kec_count > 0) {
                      echo '</div>';
                  }
                @endphp
               </div>
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
                  <input type="text" id="editNik" name="dpt_nik" class="form-control" placeholder="Enter NIK" onkeypress="return hanyaAngka(event)" max="16" @required(true)>
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
            <div class="row mt-3">
              <div class="col-xl mb-3">
                <label for="editChecklistKecamatan" class="form-label">Daerah Pemilihan <span style='color:red'>*</span></label>
                <div class="row" style="padding-left: 15px; padding-right: 15px">
                  @php 
                  $tmp_group_id = 'x';
                  $tmp_group_kec_count = 0;

                  foreach($ceklisKecamatan as $key => $check){
                    if($check->kecamatan_id!=$tmp_group_id){
                      if($tmp_group_kec_count==0||$tmp_group_kec_count % 2==0){
                              echo '</div><div class="row" style="padding-left: 15px; padding-right: 15px">';
                          }
                          if ($tmp_group_kec_count > 0) {
                              echo '</div>';
                          }

                          echo '<div class="col mb-6 edit_kecamatan_type">';
                          $tmp_group_id = $check->kecamatan_id;
                          $tmp_group_kec_count++;
                      }

                      echo '
                          <div class="form-check form-check-primary mt-3">
                              <input class="form-check-input checkbox-item-modal-edit-kecamatan checkbox-item" type="checkbox" onClick="_check_edit_kecamatan_checkbox_item(this);" name="edit_kecamatan_type[]" id="edit_kecamatan_type_'.$check->kecamatan_id.'" value="'.$check->kecamatan_id.'" required>
                              <label class="form-check-label" for="edit_kecamatan_type_'.$check->kecamatan_id.'">'.$check->kecamatan_name.'</label>
                          </div>
                      ';
                  }

                  if ($tmp_group_kec_count > 0) {
                      echo '</div>';
                  }
                @endphp
               </div>
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
                <div class="col mb-3">
                  <label class="form-label" for="det_nik">NIK</label>
                  <h6 id="det_nik"></h6>
                </div>
                <div class="col mb-3">
                  <label class="form-label" for="det_nama">Nama</label>
                  <h6 id="det_nama"></h6>
                </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label for="det_jenkel" class="form-label">Jenis Kelamin</label>
                <h6 id="det_jenkel"></h6>
              </div>
              <div class="col mb-3">
                <label for="det_tps" class="form-label">TPS</label>
                <h6 id="det_tps"></h6>
              </div>
            </div>
            <div class="row g-2">
                <div class="col mb-3">
                  <label class="form-label" for="det_kecamatan">Kecamatan</label>
                  <h6 id="det_kecamatan"></h6>
                </div>

                <div class="col mb-3">
                  <label class="form-label" for="det_status">Status</label>
                  <h6 id="det_status"></h6>
                </div>
            </div>
        
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
<!--/ Modal Detail DPT -->
@endsection