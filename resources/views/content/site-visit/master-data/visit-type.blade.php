@extends('layouts/contentNavbarLayout')

@section('title', 'Visit Type - Master Data')
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
<script src="{{asset('js/site-visit/master-data/visit-type.js')}}"></script>
@endsection

@section('content')
<!-- Data Table Visit Type -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-current-location bx-sm me-sm-2"></i>Visit Type Table</h5>
    </div>
    <div class="text-end pt-3 pt-md-0">
      <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#modalAddVisitType">
        <span><i class="tf-icons bx bx-plus-medical me-sm-2"></i> <span class="d-none d-sm-inline-block">Add Visit Type</span></span>
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
          <th>Code</th>
          <th>Name</th>
          <th>Desc</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Data Table Visit Type -->

<hr class="my-5">

<!-- Modal Add Visit Type -->
<div class="modal fade" id="modalAddVisitType" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddVisitType" data-method="add">
        <div class="modal-body">
          <div id="addFormLabel" class="text-center mb-4">
            <h3><span>Visit Type Form<span></h3>
            <p>Add new Visit Type.</p>
          </div>
          <div class="m-4">
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="addStatus">Status <span style='color:red'>*</span></label>
                <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                  <label class="switch switch-primary">
                    <input id="addStatus" name="visit_type_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
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
                <label for="addCode" class="form-label">Code <span style='color:red'>*</span></label>
                <input type="text" id="addCode" name="visit_type_code" class="form-control" placeholder="Enter Code" @required(true)>
              </div>
              <div class="col mb-3">
                <label for="addName" class="form-label">Name <span style='color:red'>*</span></label>
                <input type="text" id="addName" name="visit_type_name" class="form-control" placeholder="Enter Name" @required(true)>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addDesc" class="form-label">Description</label>
                <textarea id="addDesc" name="visit_type_desc" class="form-control" placeholder="Explanation about the new desc" rows="5" style="max-height: 100px;resize: none;"></textarea>
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
<!--/ Modal Add Visit Type -->

<!-- Modal add / edit checklist group -->
<div class="modal fade" id="modalManageChecklistGroup" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div>
          <form id="formAddChecklistGroup" data-method="add">
            <div id="addFormChecklistGrouplabel" class="text-center mb-4">
              <h3>Checklist Group Form</h3>
              <p>Add / edit checklist group data.</p>
            </div>
            <div class="m-4">
              <div class="row">
                <div class="col mb-3">
                  <label for="addChecklistGroup" class="form-label">Checklist Group <span style='color:red'>*</span></label>
                    @php 
                        $tmp_group_id='x';
                        $tmp_group_count=0;
                        foreach($checklistGroup as $key => $check){
                            if($check->checklist_group_id!=$tmp_group_id){
                                $tmp_group_id=$check->checklist_group_id;
                                if($tmp_group_count==0||$tmp_group_count % 2==0){
                                    if($tmp_group_count==0){
                                        echo '<div class="row" style="padding-left:15px; padding-right:15px">';
                                    }else{
                                        echo '</div><div class="row" style="padding-left:15px; padding-right:15px">';
                                    }
                                }
                                if($key==count($checklistGroup)-1 || $key!=0){
                                    echo '</div>';
                                }
                                echo '<div class="col mb-6 checklist_group">
                                        <div class="form-check form-check-primary mt-3">
                                           <input class="form-check-input checkbox-item-modal-add checkbox-item" type="checkbox" onClick="_check_group_checkbox_item(this);" name="checklist_group[]" id="checklist_group_'.$check->checklist_group_id.'" value="'.$check->checklist_group_id.'" required>
                                            <label class="form-check-label" for="checklist_group_'.$check->checklist_group_id.'">'.$check->checklist_group_name.'</label>
                                        </div>
                                    ';
                                $tmp_group_count++;
                            }else{
                                echo '
                                   <input class="form-check-input checkbox-item-modal-add checkbox-item" type="checkbox" onClick="_check_group_checkbox_item(this);" name="checklist_group[]" id="checklist_group_'.$check->checklist_group_id.'" value="'.$check->checklist_group_id.'" required>
                                    <label class="form-check-label" for="checklist_group_'.$check->checklist_group_id.'">'.$check->checklist_group_name.'</label>
                                ';
                            }
                        } 
                    @endphp
                    </div>
                  </div>
                </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
            </div>
          </form>
        </div>
    </div>
  </div>
</div>
<!--/ end Modal add / edit product checklist group -->

<!-- Modal add / edit checklist visual -->
<div class="modal fade" id="modalManageChecklistVisual" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div>
          <form id="formAddChecklistVisual" data-method="add">
            <div id="addFormChecklistVisuallabel" class="text-center mb-4">
              <h3>Checklist Visual Form</h3>
              <p>Add / edit checklist visual data.</p>
            </div>
            <div class="m-4">
              <div class="row">
                <div class="col mb-3">
                  <label for="addChecklistVisual" class="form-label">Checklist Visual Type <span style='color:red'>*</span></label>
                    @php 
                        $tmp_visual_id='x';
                        $tmp_group_vis_count=0;
                        foreach($checklistVisual as $key => $check){
                            if($check->visit_visual_type_id!=$tmp_visual_id){
                                $tmp_visual_id=$check->visit_visual_type_id;
                                if($tmp_group_vis_count==0||$tmp_group_vis_count % 2==0){
                                    if($tmp_group_vis_count==0){
                                        echo '<div class="row" style="padding-left:15px; padding-right:15px">';
                                    }else{
                                        echo '</div><div class="row" style="padding-left:15px; padding-right:15px">';
                                    }
                                }
                                if($key==count($checklistVisual)-1 || $key!=0){
                                    echo '</div>';
                                }
                                echo '<div class="col mb-6 visual_type">
                                        <div class="form-check form-check-primary mt-3">
                                           <input class="form-check-input checkbox-item-modal-add-visual checkbox-item" type="checkbox" onClick="_check_visual_checkbox_item(this);" name="visual_type[]" id="visual_type_'.$check->visit_visual_type_id.'" value="'.$check->visit_visual_type_id.'" required>
                                            <label class="form-check-label" for="visual_type_'.$check->visit_visual_type_id.'">'.$check->visit_visual_type_name.'</label>
                                        </div>
                                    ';
                                $tmp_group_vis_count++;
                            }else{
                                echo '
                                   <input class="form-check-input checkbox-item-modal-add-visual checkbox-item" type="checkbox" onClick="_check_visual_checkbox_item(this);" name="visual_type[]" id="visual_type_'.$check->visit_visual_type_id.'" value="'.$check->visit_visual_type_id.'" required>
                                    <label class="form-check-label" for="visual_type_'.$check->visit_visual_type_id.'">'.$check->visit_visual_type_name.'</label>
                                ';
                            }
                        } 
                    @endphp
                    </div>
                  </div>
                </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
            </div>
          </form>
        </div>
    </div>
  </div>
</div>
<!--/ end Modal add / edit product checklist visual -->

<script>
  //  untuk Checklist Group
  function _check_group_checkbox_header(el, all = false) {
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

 function _check_group_checkbox_item(el) {
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

//  untuk Checklist Visual
function _check_visual_checkbox_header(el, all = false) {
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

 function _check_visual_checkbox_item(el) {
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