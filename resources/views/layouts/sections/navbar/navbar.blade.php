@php
$containerNav = $containerNav ?? 'container-fluid';
$navbarDetached = ($navbarDetached ?? '');

@endphp

<!-- Navbar -->
@if(isset($navbarDetached) && $navbarDetached == 'navbar-detached')
<nav class="layout-navbar {{$containerNav}} navbar navbar-expand-xl {{$navbarDetached}} align-items-center bg-navbar-theme" id="layout-navbar">
  @endif
  @if(isset($navbarDetached) && $navbarDetached == '')
  <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="{{$containerNav}}">
      @endif

      <!--  Brand demo (display only for navbar-full and hide on below xl) -->
      @if(isset($navbarFull))
      <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
        <a href="{{url('/')}}" class="app-brand-link gap-2">
          <span class="app-brand-logo demo">
            @include('_partials.macros',["width"=>25,"withbg"=>'#696cff'])
          </span>
          <span class="app-brand-text demo menu-text fw-bolder">{{config('variables.templateName')}}</span>
        </a>
      </div>
      @endif

      <!-- ! Not required for layout-without-menu -->
      @if(!isset($navbarHideToggle))
      <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ?' d-xl-none ' : '' }}">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
          <i class="bx bx-menu bx-sm"></i>
        </a>
      </div>
      @endif

      <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        {{-- @if(!isset($menuHorizontal))
        <!-- Search -->
        <div class="navbar-nav align-items-center">
          <div class="nav-item navbar-search-wrapper mb-0">
            <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
              <i class="bx bx-search bx-sm"></i>
              <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
            </a>
          </div>
        </div>
        <!-- /Search -->
        @endif --}}

        <ul class="navbar-nav flex-row align-items-center ms-auto">
          @if(!isset($menuHorizontal))
          <!-- Language -->
          {{-- <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <i class='fi fi-us fis rounded-circle fs-3 me-1'></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item" href="{{url('lang/en')}}" data-language="en">
                  <i class="fi fi-us fis rounded-circle fs-4 me-1"></i>
                  <span class="align-middle">English</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{url('lang/fr')}}" data-language="fr">
                  <i class="fi fi-fr fis rounded-circle fs-4 me-1"></i>
                  <span class="align-middle">French</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{url('lang/de')}}" data-language="de">
                  <i class="fi fi-de fis rounded-circle fs-4 me-1"></i>
                  <span class="align-middle">German</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{url('lang/pt')}}" data-language="pt">
                  <i class="fi fi-pt fis rounded-circle fs-4 me-1"></i>
                  <span class="align-middle">Portuguese</span>
                </a>
              </li>
            </ul>
          </li> --}}
          <!--/ Language -->
          @endif

          {{-- @if(isset($menuHorizontal))
          <!-- Search -->
          <li class="nav-item navbar-search-wrapper me-2 me-xl-0">
            <a class="nav-item nav-link search-toggler" href="javascript:void(0);">
              <i class="bx bx-search bx-sm"></i>
            </a>
          </li>
          <!-- /Search -->
          @endif --}}

          <!-- Style Switcher -->
          <li class="nav-item me-2 me-xl-0">
            <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
              <i class='bx bx-sm'></i>
            </a>
          </li>
          <!--/ Style Switcher -->

          <!-- Quick links  -->
          {{-- <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
              <i class='bx bx-grid-alt bx-sm'></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end py-0">
              <div class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                  <h5 class="text-body mb-0 me-auto">Shortcuts</h5>
                  <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Add shortcuts"><i class="bx bx-sm bx-plus-circle"></i></a>
                </div>
              </div>
              <div class="dropdown-shortcuts-list scrollable-container">
                <div class="row row-bordered overflow-visible g-0">
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                      <i class="bx bx-calendar fs-4"></i>
                    </span>
                    <a href="{{url('app/calendar')}}" class="stretched-link">Calendar</a>
                    <small class="text-muted mb-0">Appointments</small>
                  </div>
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                      <i class="bx bx-food-menu fs-4"></i>
                    </span>
                    <a href="{{url('app/invoice/list')}}" class="stretched-link">Invoice App</a>
                    <small class="text-muted mb-0">Manage Accounts</small>
                  </div>
                </div>
                <div class="row row-bordered overflow-visible g-0">
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                      <i class="bx bx-user fs-4"></i>
                    </span>
                    <a href="{{url('app/user/list')}}" class="stretched-link">User App</a>
                    <small class="text-muted mb-0">Manage Users</small>
                  </div>
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                      <i class="bx bx-check-shield fs-4"></i>
                    </span>
                    <a href="{{url('app/access-roles')}}" class="stretched-link">Role Management</a>
                    <small class="text-muted mb-0">Permission</small>
                  </div>
                </div>
                <div class="row row-bordered overflow-visible g-0">
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                      <i class="bx bx-pie-chart-alt-2 fs-4"></i>
                    </span>
                    <a href="{{url('/')}}" class="stretched-link">Dashboard</a>
                    <small class="text-muted mb-0">User Profile</small>
                  </div>
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                      <i class="bx bx-cog fs-4"></i>
                    </span>
                    <a href="{{url('pages/account-settings-account')}}" class="stretched-link">Setting</a>
                    <small class="text-muted mb-0">Account Settings</small>
                  </div>
                </div>
                <div class="row row-bordered overflow-visible g-0">
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                      <i class="bx bx-help-circle fs-4"></i>
                    </span>
                    <a href="{{url('pages/help-center-landing')}}" class="stretched-link">Help Center</a>
                    <small class="text-muted mb-0">FAQs & Articles</small>
                  </div>
                  <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                      <i class="bx bx-window-open fs-4"></i>
                    </span>
                    <a href="{{url('modal-examples')}}" class="stretched-link">Modals</a>
                    <small class="text-muted mb-0">Useful Popups</small>
                  </div>
                </div>
              </div>
            </div>
          </li> --}}
          <!-- Quick links -->

          <!-- Notification -->
          {{-- <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
              <i class="bx bx-bell bx-sm"></i>
              <span class="badge bg-danger rounded-pill badge-notifications">5</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end py-0">
              <li class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                  <h5 class="text-body mb-0 me-auto">Notification</h5>
                  <a href="javascript:void(0)" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read"><i class="bx fs-4 bx-envelope-open"></i></a>
                </div>
              </li>
              <li class="dropdown-notifications-list scrollable-container">
                <ul class="list-group list-group-flush">
                  <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">Congratulation Lettie 🎉</h6>
                        <p class="mb-0">Won the monthly best seller gold badge</p>
                        <small class="text-muted">1h ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="bx bx-x"></span></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-danger">CF</span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">Charles Franklin</h6>
                        <p class="mb-0">Accepted your connection</p>
                        <small class="text-muted">12hr ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="bx bx-x"></span></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <img src="{{ asset('assets/img/avatars/2.png') }}" alt class="w-px-40 h-auto rounded-circle">
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">New Message ✉️</h6>
                        <p class="mb-0">You have new message from Natalie</p>
                        <small class="text-muted">1h ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="bx bx-x"></span></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-cart"></i></span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">Whoo! You have new order 🛒 </h6>
                        <p class="mb-0">ACME Inc. made new order $1,154</p>
                        <small class="text-muted">1 day ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="bx bx-x"></span></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <img src="{{ asset('assets/img/avatars/9.png') }}" alt class="w-px-40 h-auto rounded-circle">
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">Application has been approved 🚀 </h6>
                        <p class="mb-0">Your ABC project application has been approved.</p>
                        <small class="text-muted">2 days ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="bx bx-x"></span></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-pie-chart-alt"></i></span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">Monthly report is generated</h6>
                        <p class="mb-0">July monthly financial report is generated </p>
                        <small class="text-muted">3 days ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="bx bx-x"></span></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <img src="{{ asset('assets/img/avatars/5.png') }}" alt class="w-px-40 h-auto rounded-circle">
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">Send connection request</h6>
                        <p class="mb-0">Peter sent you connection request</p>
                        <small class="text-muted">4 days ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="bx bx-x"></span></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <img src="{{ asset('assets/img/avatars/6.png') }}" alt class="w-px-40 h-auto rounded-circle">
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">New message from Jane</h6>
                        <p class="mb-0">Your have new message from Jane</p>
                        <small class="text-muted">5 days ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="bx bx-x"></span></a>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-warning"><i class="bx bx-error"></i></span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">CPU is running high</h6>
                        <p class="mb-0">CPU Utilization Percent is currently at 88.63%,</p>
                        <small class="text-muted">5 days ago</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                        <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="bx bx-x"></span></a>
                      </div>
                    </div>
                  </li>
                </ul>
              </li>
              <li class="dropdown-menu-footer border-top">
                <a href="javascript:void(0);" class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40">
                  View all notifications
                </a>
              </li>
            </ul>
          </li> --}}
          <!--/ Notification -->

          <!-- User -->
          <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <div class="avatar avatar-online">
                @php
                  $userPhoto = session('user_photo');
                  $userPhotoPath = '/storage/users_uploads/' . $userPhoto;
                  $defaultPhotoPath = asset('assets/upload/user/default.jpeg');
                @endphp
              
                <img id="userPhoto" src="{{ file_exists(public_path($userPhotoPath)) ? $userPhotoPath : $defaultPhotoPath }}" alt="user image" class="w-px-40 h-auto rounded-circle">
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item pe-none" href="{{ Route::has('profile.show') ? route('profile.show') : url('pages/profile-user') }}">
                  <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                      <div class="avatar avatar-online">
                        <img id="userPhotoNav" src="{{ file_exists(public_path($userPhotoPath)) ? $userPhotoPath : $defaultPhotoPath }}" alt="user image" class="w-px-40 h-auto rounded-circle">
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <span class="fw-semibold d-block">
                        {{ session('user_uniq_name') }}
                      </span>
                      <small class="text-muted">
                        {{-- {{ session('role_name') }} --}}

                        @if (session('role_id') == 1 || session('role_id') == 2)
                          {{ session('role_name') }}
                        @else
                          {{ session('role_name') }}
                        @endif
                      </small>
                    </div>
                  </div>
                </a>
              </li>
              <li>
                <div class="dropdown-divider"></div>
              </li>
              <li>
                <a class="dropdown-item cursor-pointer" data-bs-toggle="modal" data-bs-target="#modalChangePassword">
                  <i class="bx bx-lock-open me-2"></i>
                  <span class="align-middle">Change Password</span>
                </a>
              </li>
              <li>
                <div class="dropdown-divider"></div>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('auth-logout') }}">
                  <i class='bx bx-power-off me-2'></i>
                  <span class="align-middle">Logout</span>
                </a>
              </li>
            </ul>
          </li>
          <!--/ User -->
        </ul>
      </div>

      <!-- Search Small Screens -->
      {{-- <div class="navbar-search-wrapper search-input-wrapper {{ isset($menuHorizontal) ? $containerNav : '' }} d-none">
        <input type="text" class="form-control search-input {{ isset($menuHorizontal) ? '' : $containerNav }} border-0" placeholder="Search..." aria-label="Search...">
        <i class="bx bx-x bx-sm search-toggler cursor-pointer"></i>
      </div> --}}

      @if(!isset($navbarDetached))
    </div>
    @endif
  </nav>
  <!-- / Navbar -->

  <!-- Modal Change Password -->
  <div class="modal fade" id="modalChangePassword" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xs" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="formChangePassword">
          <div class="modal-body">
            <div class="text-center mb-4">
              <h3><span><i class="tf-icons bx bxs-lock-open bx-sm me-sm-2"></i>Change Password<span><i class="tf-icons bx bxs-lock-open bx-sm ms-sm-2"></i></h3>
              <p>change your password with a new one.</p>
            </div>
            <div class="m-4">
              <div class="row form-password-toggle">
                <div class="col mb-3">
                  <label for="currentPassword" class="form-label">Current Password <span style='color:red'>*</span></label>
                  <div class="input-group input-group-merge">
                    <input type="password" id="currentPassword" name="current_password" class="form-control current-pass-maxlength" maxlength="255" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" @required(true)>
                    <span class="input-group-text cursor-pointer" id="currentPassword"><i class="bx bx-hide"></i></span>
                  </div>
                </div>
              </div>
              <div class="row form-password-toggle">
                <div class="col mb-3">
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

  <script type="text/javascript">
    window.onload = (event) => {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      var changePasswordForm = document.getElementById('formChangePassword');

      // Change Password Form Validation
      var fv = FormValidation.formValidation(changePasswordForm, {
        fields: {
          current_password: {
            validators: {
              notEmpty: {
                message: 'Please enter your current password'
              },
              stringLength: {
                min: 8,
                message: 'Password must be more than 8 characters'
              }
            }
          },
          change_password: {
            validators: {
              notEmpty: {
                message: 'Please enter your new password'
              },
              stringLength: {
                min: 8,
                message: 'Password must be more than 8 characters'
              }
            }
          },
          change_password_confirmation: {
            validators: {
              notEmpty: {
                message: 'Please confirm your new password'
              },
              identical: {
                compare: function () {
                  return changePasswordForm.querySelector('[name="change_password"]').value;
                },
                message: 'The password and its confirm are not the same'
              },
              stringLength: {
                min: 6,
                message: 'Password must be more than 6 characters'
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            // Use this for enabling/changing valid/invalid class
            eleValidClass: '',
            rowSelector: function rowSelector(field, ele) {
              // field is the field name & ele is the field element
              return '.mb-3';
            }
          }),
          submitButton: new FormValidation.plugins.SubmitButton(),
          autoFocus: new FormValidation.plugins.AutoFocus()
        }
      }).on('core.form.valid', function () {
        $.ajax({
          data: $('#formChangePassword').serialize(),
          url: baseUrl + 'change-password',
          type: 'POST',
          success: function success(response) {
            if (!response.status) {
              Swal.fire({
                icon: 'error',
                title: response.message.title,
                text: response.message.text,
                customClass: {
                  confirmButton: 'btn btn-primary'
                }
              });
            } else {
              Swal.fire({
                icon: 'success',
                title: response.message.title,
                text: response.message.text,
                customClass: {
                  confirmButton: 'btn btn-success'
                }
              });
            }
          },
          error: function error(err) {
            Swal.fire({
              title: 'Error!',
              text: 'Internal server error.',
              icon: 'error',
              customClass: {
                confirmButton: 'btn btn-primary'
              }
            });
          }
        });
        $('#modalChangePassword').modal('hide');
        $('#formChangePassword')[0].reset();
      });


      // // pass-maxlength & repeater (jquery)
      // $(function () {
      //   var maxlengthInput = $('.pass-maxlength');
      //   var currentPassword = $('.current-pass-maxlength');

      //   // Password Max Length
      //   // --------------------------------------------------------------------
      //   if (maxlengthInput.length) {
      //     maxlengthInput.each(function () {
      //       $(this).maxlength({
      //         warningClass: 'label label-success bg-success text-white',
      //         limitReachedClass: 'label label-danger',
      //         separator: ' out of ',
      //         preText: 'You typed ',
      //         postText: ' chars available.',
      //         validate: true,
      //         threshold: +this.getAttribute('maxlength')
      //       });
      //     });
      //   }

      //   if (currentPassword.length) {
      //     currentPassword.each(function () {
      //       $(this).maxlength({
      //         warningClass: 'label label-success bg-success text-white',
      //         limitReachedClass: 'label label-danger',
      //         separator: ' out of ',
      //         preText: 'Enter your current password. You typed ',
      //         postText: ' chars available.',
      //         validate: true,
      //         threshold: +this.getAttribute('maxlength')
      //       });
      //     });
      //   }
      // });
    }
  </script>