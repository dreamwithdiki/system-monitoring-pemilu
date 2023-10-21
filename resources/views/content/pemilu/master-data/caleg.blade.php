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
<script>

//  untuk Checklist Kecamatan
function _check_kecamatan_checkbox_header(el, all = false) {
   if (all) {
     $(el).find('.checkbox-header').each(function (index, val) {
       var tmp_checkall = true;
       $(val).parents('div').find('.checkbox-item').each(function (index2, val2) {
         if (!$(val2).is(":checked")) {
           tmp_checkall = false;
         }
       })
       if (tmp_checkall == true) {
         $(val).prop('checked', true)
       } else {
         $(val).prop('checked', false)
       }
     })
   } else {
     var check = $(el).is(":checked");
     if (check) {
       $(el).parents('div').find('input[type="checkbox"]').prop('checked', true);
     } else {
       $(el).parents('div').find('input[type="checkbox"]').prop('checked', false);
     }
   }
 }

 function _check_kecamatan_checkbox_item(el) {
   var tmp_checkall = true;
   $(el).parents('div').find('.checkbox-item').each(function (index, val) {
       if (!$(val).is(":checked")) {
           tmp_checkall = false;
       }
       if (tmp_checkall == true) {
           $(val).prop('checked', true);
       }
   });
   if (tmp_checkall == true) {
       $(el).parents('div').find('.checkbox-header').prop('checked', true);
   } else {
       $(el).parents('div').find('.checkbox-header').prop('checked', false);
   }
}

function _check_edit_kecamatan_checkbox_header(el, all = false) {
   if (all) {
     $(el).find('.checkbox-header').each(function (index, val) {
       var tmp_checkall = true;
       $(val).parents('div').find('.checkbox-item').each(function (index2, val2) {
         if (!$(val2).is(":checked")) {
           tmp_checkall = false;
         }
       })
       if (tmp_checkall == true) {
         $(val).prop('checked', true)
       } else {
         $(val).prop('checked', false)
       }
     })
   } else {
     var check = $(el).is(":checked");
     if (check) {
       $(el).parents('div').find('input[type="checkbox"]').prop('checked', true);
     } else {
       $(el).parents('div').find('input[type="checkbox"]').prop('checked', false);
     }
   }
 }

function _check_edit_kecamatan_checkbox_item(el) {
   var tmp_checkall = true;
   $(el).parents('div').find('.checkbox-item').each(function (index, val) {
       if (!$(val).is(":checked")) {
           tmp_checkall = false;
       }
       if (tmp_checkall == true) {
           $(val).prop('checked', true);
       }
   });
   if (tmp_checkall == true) {
       $(el).parents('div').find('.checkbox-header').prop('checked', true);
   } else {
       $(el).parents('div').find('.checkbox-header').prop('checked', false);
   }
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
  #imagePreviewPartai {
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
          <th>NIK</th>
          <th>Nama</th>
          <th>Visi & Misi</th>
          <th>No Urut Partai</th>
          <th>Nama Partai</th>
          <th>No Urut Caleg</th>
          <th>Foto Caleg</th>
          <th>Foto Partai</th>
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
              <div class="col-xl mb-3">
                <label for="addNik" class="form-label">NIK <span style='color:red'>*</span></label>
                <input type="text" id="addNik" name="caleg_nik" class="form-control" placeholder="NIK" onkeypress="return hanyaAngka(event)" max="16">
              </div>
              <div class="col-xl mb-3">
                <label for="addName" class="form-label">Nama <span style='color:red'>*</span></label>
                <input type="text" id="addName" name="caleg_name" class="form-control" placeholder="Enter Name" >
              </div>
            </div>
            <div class="row g2">
                <div class="col-xl mb-3">
                  <label for="addNomorUrutPartai" class="form-label">No Urut Partai <span style='color:red'>*</span></label>
                  <input type="number" id="addNomorUrutPartai" name="caleg_no_urut_partai" class="form-control" placeholder="Enter No Urut Partai" min="1">
                </div>
                <div class="col-xl mb-3">
                  <label for="addNomorUrutCaleg" class="form-label">No Urut Caleg <span style='color:red'>*</span></label>
                  <input type="number" id="addNomorUrutCaleg" name="caleg_no_urut_caleg" class="form-control" placeholder="Enter No Urut Caleg" min="1">
                </div>
            </div>
            <div class="row">
              <div class="col-xl mb-3">
                <label for="addNamaPartai" class="form-label">Nama Partai <span style='color:red'>*</span></label>
                <input type="text" id="addNamaPartai" name="caleg_nama_partai" class="form-control" placeholder="Enter Nama Partai" >
              </div>
            </div>
            <div class="row g2">
              <div class="col-xl mb-3">
                <label class="form-label" for="bs-validation-upload-file">Photo Caleg</label>
                <input type="file" class="form-control" name="caleg_photo" id="bs-validation-upload-file"/>
              </div>
            </div>
            <div class="row g2">
              <div class="col-xl mb-3">
                <label class="form-label" for="bs-validation-upload-file">Photo Partai <span style='color:red'>*</span></label>
                <input type="file" class="form-control" name="caleg_photo_partai" id="bs-validation-upload-file"/>
              </div>
            </div>

            <div class="row">
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
              <div class="col-xl mb-3">
                <label for="addVisiMisi" class="form-label">Visi & Misi <span style='color:red'>*</span></label>
                <textarea id="addVisiMisi" name="caleg_visi_misi" class="form-control" placeholder="Explanation about the new visi & misi" rows="5" style="max-height: 100px;resize: none;"></textarea>
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
      <form id="formEditCaleg" data-method="edit">
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
              <div class="col-xl mb-3">
                <label for="editNIK" class="form-label">NIK <span style='color:red'>*</span></label>
                <input type="text" id="editNIK" name="caleg_nik" class="form-control" placeholder="Enter NIK" onkeypress="return hanyaAngka(event)" max="16" @required(true)>
              </div>
              <div class="col-xl mb-3">
                <label for="editName" class="form-label">Nama <span style='color:red'>*</span></label>
                <input type="text" id="editName" name="caleg_name" class="form-control" placeholder="Enter Name" @required(true)>
              </div>
            </div>
            <div class="row g2">
              <div class="col-xl mb-3">
                <label for="editNomorUrutPartai" class="form-label">No Urut Partai <span style='color:red'>*</span></label>
                <input type="number" id="editNomorUrutPartai" name="caleg_no_urut_partai" class="form-control" placeholder="Enter No Urut Partai" min="1">
              </div>
              <div class="col-xl mb-3">
                <label for="editNomorUrutCaleg" class="form-label">No Urut Caleg <span style='color:red'>*</span></label>
                <input type="number" id="editNomorUrutCaleg" name="caleg_no_urut_caleg" class="form-control" placeholder="Enter No Urut Caleg" min="1">
              </div>
          </div>
            <div class="row">
              <div class="col-xl mb-3">
                <input type="hidden" id="oldImage" name="oldImage" value="">
                <label class="form-label">Current Photo Caleg</label>
                <div class="col-md-3">
                    <img src="#" class="rounded current-photo" style="max-width: 100%; height: auto;">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xl mb-3">
                <label class="form-label" for="caleg_photo">Change Photo Caleg</label>
                <input type="file" class="form-control" id="caleg_photo" name="caleg_photo"/>
                <br>
                <div id="imagePreview"></div>
              </div>
            </div>

            <div class="row">
              <div class="col-xl mb-3">
                <input type="hidden" id="oldImagePartai" name="oldImagePartai" value="">
                <label class="form-label">Current Photo Partai</label>
                <div class="col-md-3">
                    <img src="#" class="rounded current-photo-partai" style="max-width: 100%; height: auto;">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xl mb-3">
                <label class="form-label" for="caleg_photo_partai">Change Photo Partai</label>
                <input type="file" class="form-control" id="caleg_photo_partai" name="caleg_photo_partai"/>
                <br>
                <div id="imagePreviewPartai"></div>
              </div>
            </div>

            <div class="row">
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
                <div class="col-xl mb-3">
                    <label for="editVisiMisi" class="form-label">Visi & Misi <span style='color:red'>*</span></label>
                    <textarea id="editVisiMisi" name="caleg_visi_misi" class="form-control" placeholder="Explanation about the new visi & misi" rows="5" style="max-height: 100px;resize: none;"></textarea>
                </div>
            </div>
            <div class="row g2">
                <div class="col-xl mb-3">
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