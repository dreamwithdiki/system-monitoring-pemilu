@extends('layouts/contentNavbarLayout')

@section('title', 'Users - Settings')
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
<script src="{{asset('js/settings/users.js')}}"></script>
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
          <th>Name</th>
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
                  <label for="user_uniq_name" class="form-label">Full Name <span style='color:red'>*</span></label>
                  <input type="text" id="user_uniq_name" name="user_uniq_name" class="form-control" placeholder="Enter Full Name" @required(true)>
                </div>
            </div>
            <div class="row g-2">
                <div class="col mb-3">
                  <label class="form-label" for="role_id">Role <span style='color:red'>*</span></label>
                  <select id="role_id" name="role_id" class="ac_role form-select">
                    <option value="">Select Role</option>
                  </select>
                </div>
                <div class="col mb-3" id="emailField">
                  <label for="user_email" class="form-label">Email <span style='color:red'>*</span></label>
                  <input type="email" id="user_email" name="user_email" class="form-control" placeholder="Enter Email">
                </div>
                <div class="col mb-3" id="partnerNameField" style="display: none;">
                  <label for="partner_name" class="form-label">Partner Name <span style='color:red'>*</span></label>
                  <select id="partner_name" name="user_ref_id" class="form-control ac_partner_name">
                    <option value="">Select Partner Name</option>
                  </select>
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
                  <label for="edit_user_uniq_name" class="form-label">Full Name <span style='color:red'>*</span></label>
                  <input type="text" id="edit_user_uniq_name" name="user_uniq_name" class="form-control" placeholder="Enter Full Name" @required(true)>
                </div>
            </div>
            <div class="row g-2">
                <div class="col mb-3">
                  <label class="form-label" for="edit_role">Role <span style='color:red'>*</span></label>
                  <select id="edit_role" name="role_id" class="ac_edit_role form-select" @required(true)>
                    <option value="">Select Role</option>
                  </select>
                </div>
                <div class="col mb-3" id="editemailField">
                  <label for="edit_user_email" class="form-label">Email <span style='color:red'>*</span></label>
                  <input type="email" id="edit_user_email" name="user_email" class="form-control" placeholder="Enter Email">
                </div>
                <div class="col mb-3" id="editpartnerNameField" style="display: none;">
                  <label for="edit_partner_name" class="form-label">Partner Name <span style='color:red'>*</span></label>
                  <select id="edit_partner_name" name="user_ref_id" class="form-control ac_edit_partner_name" @required(true)>
                    <option value="">Select Partner Name</option>
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

@endsection
