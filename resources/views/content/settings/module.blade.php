@extends('layouts/contentNavbarLayout')

@section('title', 'Module - Settings')
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
<script src="{{asset('js/settings/module.js')}}"></script>
@endsection

@section('content')
<!-- Data Table Module -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-check-shield bx-sm me-sm-2"></i>Module Table</h5>
    </div>
    <div class="text-end pt-3 pt-md-0">
      <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#modalAddModule">
        <span><i class="tf-icons bx bx-plus-medical me-sm-2"></i> <span class="d-none d-sm-inline-block">Add Module</span></span>
      </button>
    </div>
  </div>
  <div class="card-datatable text-nowrap">
    <table class="datatables-ajax table table-hover">
      <thead>
        <tr>
          <th>No</th>
          <th>Module Name</th>
          <th>Module Title</th>
          <th>Module Icon</th>
          <th>Module Class</th>
          <th>Desc</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Data Table Module -->

<hr class="my-5">

<!-- Modal Add Module -->
<div class="modal fade" id="modalAddModule" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddModule" data-method="add">
        <div class="modal-body">
          <div id="addFormLabel" class="text-center mb-4">
            <h3><span>Module Form<span></h3>
            <p>Add new Module.</p>
          </div>
          <div class="m-4">
            <div class="row">
                <div class="col mb-3">
                  <label class="form-label" for="addStatus">Module Status <span style='color:red'>*</span></label>
                  <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                    <label class="switch switch-primary">
                      <input id="addStatus" name="module_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
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
                    <label class="form-label" for="addParent">Parent</label>
                    <select id="addParent" class="form-select ac_module" name='module_parent_id'>
                          <option value="0">This is top module</option>
                          @if(!empty($module_all)) 
                            @foreach($module_all as $module)
                              <option value="{{$module->module_id}}" {{($module->module_id == $module->module_parent_id)? 'selected':''}}>{{$module->module_name}}</option>
                              {!! \App\Helpers\Helpers::hasChild('', $module->module_parent_id, $module->module_id, 0) !!}
                            @endforeach
                          @endif
                    </select>           
                
                </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label for="addSort" class="form-label">Module Sort <span style='color:red'>*</span></label>
                <input type="number" id="addSort" name="module_sort" class="form-control" min="1" max="100" placeholder="Enter Module Sort" @required(true)>
              </div>
              <div class="col mb-3">
                <label for="addName" class="form-label">Module Name <span style='color:red'>*</span></label>
                <input type="text" id="addName" name="module_name" class="form-control" placeholder="Enter Module Name" @required(true)>
              </div>
            </div>
            <div class="row g-2">
                <div class="col mb-3">
                  <label for="addTitle" class="form-label">Module Title <span style='color:red'>*</span></label>
                  <input type="text" id="addTitle" name="module_title" class="form-control" placeholder="Enter Module Title" @required(true)>
                </div>
                <div class="col mb-3">
                  <label for="addIcon" class="form-label">Module <a href="https://boxicons.com/" target="_blank"> Icon</a> <span style='color:red'>*</span></label>
                  <input type="text" id="addIcon" name="module_icon" class="form-control" placeholder="Enter Module Icon" @required(true)>
                </div>
            </div>
            <div class="row g-2">
                <div class="col mb-3">
                  <label for="addClass" class="form-label">Module Class <span style='color:red'>*</span></label>
                  <input type="text" id="addClass" name="module_class" class="form-control" placeholder="Enter Module Class" @required(true)>
                </div>
                <div class="col mb-3">
                  <label for="addMethod" class="form-label">Module Method <span style='color:red'>*</span></label>
                  <input type="text" id="addMethod" name="module_method" class="form-control" placeholder="Enter Module Method" @required(true)>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="addParam" class="form-label">Module Param</label>
                    <input type="text" id="addParam" name="module_param" class="form-control" placeholder="Enter Module Param">
                </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addDesc" class="form-label">Description</label>
                <textarea id="addDesc" name="module_description" class="form-control" placeholder="Explanation about the new module" rows="5" style="max-height: 100px;resize: none;"></textarea>
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
<!--/ Modal Add Module -->

@endsection
