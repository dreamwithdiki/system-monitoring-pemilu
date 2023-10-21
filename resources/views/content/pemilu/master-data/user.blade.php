@extends('layouts/contentNavbarLayout')

@section('title', 'Users - Master Data')
@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/shepherd/shepherd.css')}}" />
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
<script src="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<script src="{{asset('assets/vendor/libs/shepherd/shepherd.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/ui-popover.js')}}"></script>
<script src="{{asset('js/pemilu/master-data/users.js')}}"></script>

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

<!-- Data Table Users -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-user-circle bx-sm me-sm-2"></i>Users Table</h5>
    </div>
    <div class="text-end pt-3 pt-md-0">
      <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#modalAddUser">
        <span><i class="tf-icons bx bx-plus-circle me-sm-2"></i> <span class="d-none d-sm-inline-block">Add User</span></span>
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
          <th>Name</th>
          <th>No Telepon</th>
          <th>Email</th>
          <th>Role</th>
          <th>Last Login</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Data Table Users -->

<hr class="my-5">

<!-- Modal Add Users -->
<div class="modal fade" id="modalAddUser" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddUser" data-method="add" enctype="multipart/form-data">
        <div class="modal-body">
          <div id="addFormLabel" class="text-center mb-4">
            <h3><span>User Form<span></h3>
            <p>Add new User.</p>
          </div>
          <div class="m-4">
            <div class="row">
                <div class="col mb-3">
                  <label class="form-label" for="addStatus">User Status <span style='color:red'>*</span></label>
                  <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                    <label class="switch switch-primary">
                      <input id="addStatus" name="user_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
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
                  <label for="user_nik" class="form-label">NIK <span style='color:red'>*</span></label>
                  <input type="text" id="user_nik" name="user_nik" class="form-control" placeholder="Enter NIK" onkeypress="return hanyaAngka(event)" max="16" @required(true)>
                </div>
            </div>
            <div class="row g-2">
                <div class="col mb-3">
                  <label for="user_uniq_name" class="form-label">Nama <span style='color:red'>*</span></label>
                  <input type="text" id="user_uniq_name" name="user_uniq_name" class="form-control" placeholder="Enter Name" @required(true)>
                </div>
                <div class="col mb-3">
                  <label for="user_no_hp" class="form-label">Telepon <span style='color:red'>*</span></label>
                  <input type="text" id="user_no_hp" name="user_no_hp" class="form-control" placeholder="Enter Phone" onkeypress="return hanyaAngka(event)" max="16" @required(true)>
                </div>
            </div>
            <div class="row g-2">
                <div class="col mb-3">
                  <label class="form-label" for="role_id">Role / Penugasan <span style='color:red'>*</span></label>
                  <select id="role_id" name="role_id" class="ac_role form-select">
                    <option value="">Select Role</option>
                  </select>
                </div>
                <div class="col mb-3" id="emailField">
                  <label for="user_email" class="form-label">Email <span style='color:red'>*</span></label>
                  <input type="email" id="user_email" name="user_email" class="form-control" placeholder="Enter Email">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 form-password-toggle">
                  <label class="form-label" for="user_password">Password <span style='color:red'>*</span></label>
                  <div class="input-group input-group-merge">
                    <input type="password" id="user_password" name="user_password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" @required(true) />
                    <span class="input-group-text cursor-pointer" id="basic-default-password4"><i class="bx bx-hide"></i></span>
                  </div>
                </div>

                <div class="mb-3 form-password-toggle">
                  <label class="form-label" for="confirm_password">Confirm Password</label>
                  <div class="input-group input-group-merge">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" @required(true) />
                    <span class="input-group-text cursor-pointer" id="basic-default-password4"><i class="bx bx-hide"></i></span>
                  </div>
                </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="addProvince">Provinsi <span style='color:red'>*</span></label>
                <select id="addProvince" name="user_province" class="ac_province form-select">
                  <option value="">Select Province Name</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="addRegency">Kabupaten <span style='color:red'>*</span></label>
                <select id="addRegency" name="user_regency" class="ac_regency form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="addDistrict">Kecamatan <span style='color:red'>*</span></label>
                <select id="addDistrict" name="user_district" class="ac_district form-select">
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="addVillage">Kelurahan <span style='color:red'>*</span></label>
                <select id="addVillage" name="user_village" class="ac_village form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="bs-validation-upload-file">Profile pic</label>
                <input type="file" class="form-control" name="user_photo" id="bs-validation-upload-file"/>
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
<!--/ Modal Add User -->

<!-- Modal Edit -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditUser" data-method="edit" enctype="multipart/form-data">
        <div class="modal-body">
          <div id="editFormLabel" class="text-center mb-4">
            <h3><span>User Form<span></h3>
            <p>Edit User.</p>
          </div>
          <div class="m-4">
            <div class="row">
                <div class="col mb-3">
                  <label class="form-label" for="editStatus">User Status <span style='color:red'>*</span></label>
                  <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                    <label class="switch switch-primary">
                      <input id="editStatus" name="user_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
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
                  <label for="edit_user_nik" class="form-label">NIK <span style='color:red'>*</span></label>
                  <input type="text" id="edit_user_nik" name="user_nik" class="form-control" placeholder="Enter NIK" onkeypress="return hanyaAngka(event)" max="16" @required(true)>
                </div>
            </div>
            <div class="row g-2">
                <div class="col mb-3">
                  <label for="edit_user_uniq_name" class="form-label">Nama <span style='color:red'>*</span></label>
                  <input type="text" id="edit_user_uniq_name" name="user_uniq_name" class="form-control" placeholder="Enter Full Name" @required(true)>
                </div>
                <div class="col mb-3">
                  <label for="edit_user_no_hp" class="form-label">Telepon <span style='color:red'>*</span></label>
                  <input type="text" id="edit_user_no_hp" name="user_no_hp" class="form-control" placeholder="Enter Phone" onkeypress="return hanyaAngka(event)" max="16" @required(true)>
                </div>
            </div>
            <div class="row g-2">
                <div class="col mb-3">
                  <label class="form-label" for="edit_role">Role / Penugasan <span style='color:red'>*</span></label>
                  <select id="edit_role" name="role_id" class="ac_edit_role form-select" @required(true)>
                    <option value="">Select Role</option>
                  </select>
                </div>
                <div class="col mb-3" id="editemailField">
                  <label for="edit_user_email" class="form-label">Email <span style='color:red'>*</span></label>
                  <input type="email" id="edit_user_email" name="user_email" class="form-control" placeholder="Enter Email">
                </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label class="form-label" for="editProvince">Provinsi <span style='color:red'>*</span></label>
                <select id="editProvince" name="user_province" class="form-select" @required(true)>
                  <option value="">Select Province Name</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="editRegency">Kabupaten <span style='color:red'>*</span></label>
                <select id="editRegency" name="user_regency" class="form-select" @required(true)>
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label class="form-label" for="editDistrict">Kecamatan <span style='color:red'>*</span></label>
                <select id="editDistrict" name="user_district" class="form-select">
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="editVillage">Kelurahan <span style='color:red'>*</span></label>
                <select id="editVillage" name="user_village" class="form-select">
                  <option value="">Choice</option>
                </select>
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
                <label class="form-label" for="user_photo">Change Profile</label>
                <input type="file" class="form-control" id="file" name="user_photo" id="user_photo" />
                <br>
                <div id="imagePreview"></div>
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

<!-- Modal Change Password -->
<div class="modal fade" id="modalChangePasswordByAdmin" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xs" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formChangePasswordByAdmin" data-method="change">
        <div class="modal-body">
          <div id="addFormChangePassLabel" class="text-center mb-4">
            <h3><span><i class="tf-icons bx bxs-lock-open bx-sm me-sm-2"></i>Change Password<span><i class="tf-icons bx bxs-lock-open bx-sm ms-sm-2"></i></h3>
            <p>change your password with a new one.</p>
          </div>
          <div class="m-4">
            <div class="row form-password-toggle">
              <div class="col mb-3">
                <input type="hidden" id="change_user_id" name="user_id">
                <label for="changePassword" class="form-label">New Password <span style='color:red'>*</span></label>
                <div class="input-group input-group-merge">
                  <input type="password" id="changePassword" name="change_password" class="form-control pass-maxlength" maxlength="255" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" @required(true)>
                  <span class="input-group-text cursor-pointer" id="changePassword"><i class="bx bx-hide"></i></span>
                </div>
              </div>
            </div>
            <div class="row form-password-toggle">
              <div class="col mb-3">
                <label for="changePasswordConfirmation" class="form-label">Confirm Password <span style='color:red'>*</span></label>
                <div class="input-group input-group-merge">
                  <input type="password" id="changePasswordConfirmation" name="change_password_confirmation" class="form-control pass-maxlength" maxlength="255" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" @required(true)>
                  <span class="input-group-text cursor-pointer" id="changePasswordConfirmation"><i class="bx bx-hide"></i></span>
                </div>
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
<!--/ Modal Change Password -->
@endsection
