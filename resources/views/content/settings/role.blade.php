@extends('layouts/contentNavbarLayout')

@section('title', 'Role - Settings')
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
<script src="{{asset('js/settings/role.js')}}"></script>
@endsection

@section('content')
<!-- Data Role -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-user-check bx-sm me-sm-2"></i>Role Table</h5>
    </div>
    <div class="text-end pt-3 pt-md-0">
      <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#modalAddRole">
        <span><i class="tf-icons bx bx-plus-medical me-sm-2"></i> <span class="d-none d-sm-inline-block">Add Role</span></span>
      </button>
    </div>
  </div>
  <div class="card-datatable text-nowrap">
    <table class="datatables-ajax table table-hover">
      <thead>
        <tr>
          <th>No</th>
          <th>Name</th>
          <th>Desc</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Data Role -->

<hr class="my-5">

<!-- Modal Add Role -->
<div class="modal fade" id="modalAddRole" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddRole" data-method="add">
        <div class="modal-body">
          <div id="addFormLabel" class="text-center mb-4">
            <h3><span>Role Form<span></h3>
            <p>Add new Role.</p>
          </div>
          <div class="m-4">
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="addStatus">Role Status <span style='color:red'>*</span></label>
                <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                  <label class="switch switch-primary">
                    <input id="addStatus" name="role_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
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
            <div class="row">
                <div class="col mb-3">
                    <label for="addName" class="form-label">Role Name <span style='color:red'>*</span></label>
                    <input type="text" id="addName" name="role_name" class="form-control" placeholder="Enter Role Name" @required(true)>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                  <label for="addDesc" class="form-label">Description</label>
                  <textarea id="addDesc" name="role_desc" class="form-control" placeholder="Explanation about the new role" rows="5" style="max-height: 100px;resize: none;"></textarea>
                </div>
              </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addAssignModule" class="form-label">Assign Module <span style='color:red'>*</span></label>
                <div class="divider text-start">
                    <div class="divider-text"><i class="bx bx-spreadsheet"></i></div>
                </div>
                @if(!empty($data_module))
                    @foreach($data_module as $module)
                        @php

                        $role_id = (!empty($data_role->role_id)) ? $data_role->role_id : old('role_id');

                        if (!empty($data_role->role_id)) {
                            $data_role_module = DB::table('sys_role_module')
                                ->where('role_module_status', '!=', '5')
                                ->where('role_id', $role_id)
                                ->where('module_id', $module->module_id)
                                ->first();
                        }
                        @endphp
                    
                        <div class="col mb-6 modules">
                            <ul>
                                <li style='list-style: none;' data-id='{{$module->module_id}}' data-module-id='{{$module->module_id}}'>
                                    <div class="form-check form-check-primary mt-3">
                                        <input {{(!empty($data_role_module)) ? 'checked="checked"' : ''}} onchange="$(_module(this,{{$module->module_id}}))" class="form-check-input checkbox-item" type="checkbox" name="modules[]" value='{{$module->module_id}}' @required(true)>
                                        <label class="form-check-label">{{$module->module_name}}</label>
                                    </div>
                                    {!! \App\Helpers\Helpers::hasModule('', $role_id, $module->module_id) !!}
                                </li>
                            </ul>
                        </div>
                    @endforeach
                @else
                <div class="col mt-3">
                    <strong>Empty module!</strong>
                </div>
                @endif
                </div>
              </div>
             </div>
            </div>
            <div class="modal-footer">
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
         </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!--/ Modal Add Role -->

<!-- Modal Edit Role -->
<div class="modal fade" id="modalEditRole" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditRole" data-method="add">
        <div class="modal-body">
          <div id="editFormLabel" class="text-center mb-4">
            <h3><span>Role Form<span></h3>
            <p>Edit new Role.</p>
          </div>
          <div class="m-4">
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="editStatus">Role Status <span style='color:red'>*</span></label>
                <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                  <label class="switch switch-primary">
                    <input id="editStatus" name="role_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
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
            <div class="row">
              <div class="col mb-3">
                <label for="editName" class="form-label">Role Name <span style='color:red'>*</span></label>
                <input type="text" id="editName" name="role_name" class="form-control" placeholder="Enter Role Name" @required(true)>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="editDesc" class="form-label">Description</label>
                <textarea id="editDesc" name="role_desc" class="form-control" placeholder="Explanation about the new role" rows="5" style="max-height: 100px;resize: none;"></textarea>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addAssignModule" class="form-label">Assign Module <span style='color:red'>*</span></label>
                <div class="divider text-start">
                    <div class="divider-text"><i class="bx bx-spreadsheet"></i></div>
                </div>
                @if(!empty($data_module))
                    @foreach($data_module as $module)
                        @php
                        $role_id = (!empty($data_role->role_id)) ? $data_role->role_id : old('role_id');

                        $module_exists = DB::table('sys_role_module')
                            ->join('sys_module', 'sys_role_module.module_id', '=', 'sys_module.module_id')
                            ->where('sys_role_module.role_module_status', '!=', '5')
                            ->where('sys_role_module.role_id', $role_id)
                            ->where('sys_module.module_id', $module->module_id)
                            ->exists();

                        $checked = ($module_exists) ? 'checked' : '';
                        @endphp

                        <div class="col mb-6 modules">
                            <ul>
                                <li style="list-style: none;" data-id="{{$module->module_id}}">
                                    <div class="form-check form-check-primary mt-3">
                                        <input {{$checked}} onchange="$(_module(this, {{$module->module_id}}))" class="form-check-input checkbox-item" type="checkbox" name="modules[]" value="{{$module->module_id}}" @required(true)>
                                        <label class="form-check-label checkbox">{{$module->module_name}}</label>
                                    </div>
                                    {!! \App\Helpers\Helpers::hasModule('', $role_id, $module->module_id) !!}
                                </li>
                            </ul>
                        </div>
                    @endforeach
                @else
                <div class="col mt-3">
                    <strong>Empty module!</strong>
                </div>
                @endif
              </div>
            </div>
           </div>
          </div>
          <div class="modal-footer">
              <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
       </div>
      </div>
    </form>
  </div>
</div>
</div>
<!--/ Modal Edit Role -->

<script>
  function _module(obj, id) {
    var closestLi = $('.case_' + id).closest('li');
    $('.case_' + id).prop('checked', obj.checked);
    
    if (obj.checked) {
      closestLi.show('slow');
    } else {
      closestLi.hide('slow');
    }
    
    $.each($('.case_' + id), function() {
      var id2 = $(this).closest('li').attr('data-id');
      $('.case_' + id2).prop('checked', obj.checked);
    });
  }

  // Menambahkan event listener untuk tombol "Add"
  document.addEventListener('DOMContentLoaded', function() {
      var addButton = document.querySelector('[data-bs-target="#modalAddRole"]');
      addButton.addEventListener('click', function() {
          // Sembunyikan semua elemen anak yang terletak di dalam elemen <li>
          var liElements = document.querySelectorAll('li[data-module-id]');
          liElements.forEach(function(li) {
              var children = li.querySelectorAll('li');
              children.forEach(function(child) {
                  child.style.display = 'none';
              });
          });

          // Reset semua checkbox
          var checkboxes = document.querySelectorAll('.checkbox-item');
          checkboxes.forEach(function(checkbox) {
            checkbox.checked = false;
          });
      });
  });

</script>

@endsection