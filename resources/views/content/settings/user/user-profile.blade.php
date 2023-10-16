@extends('layouts/contentNavbarLayout')

@section('title', 'User Profile - Profile')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection


@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-profile.js')}}"></script>
<script src="{{asset('js/settings/user/user-profile.js')}}"></script>
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
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User Profile /</span> Profile {{ session('user_uniq_name') }}
</h4>

<!-- Header -->
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="user-profile-header-banner">
        <img src="{{asset('assets/img/pages/profile-banner.png')}}" alt="Banner image" class="rounded-top">
      </div>
      <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          @php
            $userPhoto = session('user_photo');
            $userPhotoPath = '/storage/users_uploads/' . $userPhoto;
            $defaultPhotoPath = asset('assets/upload/user/default.jpeg');
          @endphp
        
          <img src="{{ file_exists(public_path($userPhotoPath)) ? $userPhotoPath : $defaultPhotoPath }}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
        </div>        
        <div class="flex-grow-1 mt-3 mt-sm-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4>{{ session('user_uniq_name') }}</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                <li class="list-inline-item fw-semibold">
                  <i class='bx bx-pen'></i> {{ session('role_name') }}
                </li>
                <li class="list-inline-item fw-semibold">
                  <i class='bx bx-map'></i> Indonesia
                </li>
                <li class="list-inline-item fw-semibold">
                  <i class='bx bx-calendar-alt'></i> Joined {{ $user_created_date }}
                </li>
              </ul>
            </div>
            <button id="btn_user_edit" data-id="{{ session('user_id') }}" class="btn btn-primary text-nowrap">
                <i class="bx bx-user-plus"></i> Edit Profile
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Header -->

<!-- User Profile Content -->
<div class="row">
  <div class="col-xl-12 col-lg-5 col-md-5">
    <!-- About User -->
    <div class="card mb-4">
      <div class="card-body">
        <small class="text-muted text-uppercase">About</small>
        <ul class="list-unstyled mb-4 mt-3">
          <li class="d-flex align-items-center mb-3"><i class="bx bx-terminal"></i><span class="fw-semibold mx-2">NIK:</span> <span>{{ session('user_nik') }}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="bx bx-user"></i><span class="fw-semibold mx-2">Full Name:</span> <span>{{ session('user_uniq_name') }}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="bx bx-envelope"></i><span class="fw-semibold mx-2">Email:</span> <span>{{ session('user_email') }}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="bx bx-phone"></i><span class="fw-semibold mx-2">Phone:</span> <span>{{ session('user_no_hp') }}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="bx bx-check"></i><span class="fw-semibold mx-2">Status:</span> <span class="badge bg-label-success">{{ session('user_status') == 2 ? 'Active' : 'Not Active' }}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="bx bx-star"></i><span class="fw-semibold mx-2">Role:</span> <span class="badge bg-primary">{{ session('role_name') }}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="bx bx-flag"></i><span class="fw-semibold mx-2">Country:</span> <span>Indonesia</span></li>
          <li class="d-flex align-items-center mb-3"><i class="bx bx-detail"></i><span class="fw-semibold mx-2">Languages:</span> <span>Indonesia</span></li>
        </ul>
        <small class="text-muted text-uppercase">Login</small>
        <ul class="list-unstyled mb-4 mt-3">
          <li class="d-flex align-items-center mb-3"><i class="bx bx-log-in-circle"></i><span class="fw-semibold mx-2">Last Login:</span> <span> {{ $user_last_login }}</span></li>
        </ul>
      </div>
    </div>
    <!--/ About User -->
  </div>
</div>
<!--/ User Profile Content -->

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
              <h3><span>{{ session('user_uniq_name') }} Form<span></h3>
              <p>Edit data {{ session('user_uniq_name') }}.</p>
            </div>
            <div class="m-4">
              <div class="row g-2">
                  <input type="hidden" id="edit_role" name="role_id">
                  <div class="col mb-3">
                    <label for="edit_user_uniq_name" class="form-label">Full Name <span style='color:red'>*</span></label>
                    <input type="text" id="edit_user_uniq_name" name="user_uniq_name" class="form-control" placeholder="Enter Full Name" @required(true)>
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
