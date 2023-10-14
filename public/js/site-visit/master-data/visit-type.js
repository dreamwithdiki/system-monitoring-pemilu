/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table = $('.datatables-ajax');
  var modal_add_visit_type = $('#modalAddVisitType');
  var modal_class_loader = $('.modal-block-loader');
  var typingTimer;
  
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  });

  // Data Table
  if (dt_ajax_table.length) {
    var dt_ajax = dt_ajax_table.DataTable({
      processing: true,
      serverSide: true,
      initComplete: onInit,
      ajax: {
        url: baseUrl + 'site-visit/master-data/visit-type/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'site-visit/master-data/visit-type/get');
        },
        complete: function () {
          $.unblockUI();
        },
        error: function (jqXHR, textStatus, errorThrown) {
          // Check if the error is related to AJAX request
          if (jqXHR.status !== 200) {
            Swal.fire({
              title: 'Info!',
              showClass: {
                popup: 'animate__animated animate__tada'
              },
              text: 'Sesi anda sudah habis. silahkan login ulang !!!.',
              icon: 'info',
              customClass: {
                confirmButton: 'btn btn-primary'
              }
            });
          }
      }
      },
      columns: [
        { data: 'no', orderable: false },
        { data: 'visit_type_code' }, 
        { data: 'visit_type_name' },
        {
            data: 'visit_type_desc',
            orderable: false,
            render: function(data, type, row) {
              if (data) {
                var expanded = row.expanded ? row.expanded : false;
          
                if (!expanded) {
                  var shortDesc = data.length > 30 ? data.substr(0, 30) + '...' : data;
                  var showMoreHtml = data.length > 30 ? '<a href="javascript:void(0);" class="show-more">Show More</a>' : '';
                  return '<div style="white-space: pre-wrap;" class="short-desc">' + shortDesc + '</div>' + showMoreHtml;
                } else {
                  return '<div style="white-space: pre-wrap;" class="full-desc">' + data + '</div><a href="javascript:void(0);" class="show-less">Show Less</a>';
                }
              } else {
                return '-';
              }
            }
          },
        { data: 'visit_type_status', orderable: false },
      ],
      columnDefs: [
        {
          targets: 4,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
            if (data == 2) {
              return '<span class="badge bg-label-success me-1">Active</span>';
            } else {
              return '<span class="badge bg-label-danger me-1">Deactive</span>';
            }
          }
        },
        {
          targets: 5,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
              return '' +
              '<div class="d-inline-block text-nowrap">' +
                  '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                  '<div class="dropdown-menu">' +
                      '<a id="dropdownMenuEdit" data-id="' + row.visit_type_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>' +
                      '<div class="dropdown-divider"></div>' +
                      '<a id="dropdownMenuChecklistGroup" data-id="' + row.visit_type_id + '" data-code="' + row.visit_type_code + '" data-name="' + row.visit_type_name + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-list-check me-1"></i> Checklist Group</a>' +
                      '<a id="dropdownMenuVisualType" data-id="' + row.visit_type_id + '" data-code="' + row.visit_type_code + '" data-name="' + row.visit_type_name + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-current-location me-1"></i> Visual Type</a>' +
                      '<div class="dropdown-divider"></div>' +
                      '<a id="dropdownMenuActivate" data-id="' + row.visit_type_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
                      '<a id="dropdownMenuDeactivate" data-id="' + row.visit_type_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Deactivate</a>' +
                      '<a id="dropdownMenuDelete" data-id="' + row.visit_type_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
                  '</div>' +
              '</div>';
          }
        }
      ],
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
    });
  }

  function onInit() {
    $(document).on('keyup', '.search', function(e) {
      var $this = $(this);
      clearTimeout(typingTimer);
      typingTimer = setTimeout(function() {
        dt_ajax_table.DataTable().search($this.val()).draw();
      }, 1200);
    });    
  }

  $(document).on('click', '.show-more', function(e) {
    e.preventDefault();
    var $this = $(this);
    var $shortDesc = $this.prev('.short-desc');
    var $fullDesc = $shortDesc.next('.full-desc');
    $shortDesc.hide();
    $fullDesc.show();
    $this.text('Show Less');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expanded = true; // Menandai bahwa deskripsi telah di-expand
    dt_ajax.row($this.closest('tr')).data(row);
  });
  
  $(document).on('click', '.show-less', function(e) {
    e.preventDefault();
    var $this = $(this);
    var $fullDesc = $this.prev('.full-desc');
    var $shortDesc = $fullDesc.prev('.short-desc');
    $fullDesc.hide();
    $shortDesc.show();
    $this.text('Show More');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expanded = false; // Menandai bahwa deskripsi telah di-collapse
    dt_ajax.row($this.closest('tr')).data(row);
  });
  
  // Add Form
  var add_visit_type_form = document.getElementById('formAddVisitType');

  // Site Form Validation
  var fv = FormValidation.formValidation(add_visit_type_form, {
    fields: {
        visit_type_code: {
        validators: {
          notEmpty: {
            message: 'Please enter visit type code'
          }
        }
      },
      visit_type_name: {
        validators: {
          notEmpty: {
            message: 'Please enter visit type name'
          }
        }
      },
      visit_type_desc: {
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
      // Submit the form when all fields are valid
      // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  }).on('core.form.valid', function () {
    // Adding or Updating visit-type when form successfully validate
    if ($('#formAddVisitType').data('method') == 'add') {
      var url = "site-visit/master-data/visit-type/store";
    } else if ($('#formAddVisitType').data('method') == 'edit') {
      var url = "site-visit/master-data/visit-type/update/" + $('#formAddVisitType').attr('data-id');
    } else {
      var url = "";
    }

    $.ajax({
      data: $('#formAddVisitType').serialize(),
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax.draw();
        modal_add_visit_type.modal('hide');

        if (response.status) {
          Swal.fire({
            icon: 'success',
            title: response.message.title,
            text: response.message.text,
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: response.message.title,
            text: response.message.text,
            customClass: {
              confirmButton: 'btn btn-primary'
            }
          });
        }
      },
      error: function error(err) {
        modal_add_visit_type.modal('hide');
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
  });
  // End Add Form
  
  // Edit button handler
  $(document).on('click', '#dropdownMenuEdit', function () {
    var visit_type_id = $(this).data('id');

    // get data
    $.ajax({
      url: baseUrl + "site-visit/master-data/visit-type/show/" + visit_type_id,
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {
        if (response.data.visit_type_status == 2) {
          $('#addStatus').prop('checked', true);
        } else {
          $('#addStatus').prop('checked', false);
        }

        $('#addCode').val(response.data.visit_type_code);
        $('#addName').val(response.data.visit_type_name);
        $('#addDesc').val(response.data.visit_type_desc);
        modal_class_loader.unblock();
      }
    });

    $('#addFormLabel > p').html('Edit visit type.');
    $('#formAddVisitType').attr('data-method', 'edit');
    $('#formAddVisitType').data('method', 'edit');
    $('#formAddVisitType').attr('data-id', visit_type_id);
    modal_add_visit_type.modal('show');
  });
  
  // Active / Deactive status button handler
  $(document).on('click', '.dropdownMenuStatusUpdate', function () {
    var visit_type_id = $(this).data('id'),
      visit_type_status = $(this).data('status');

    if (visit_type_status == 2) {
      var confirmText = 'Yes, active it!',
        confirmStyle = 'btn btn-success me-3';
    } else {
      var confirmText = 'Yes, deactive it!',
        confirmStyle = 'btn btn-danger me-3';
    }

    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: confirmText,
      customClass: {
        confirmButton: confirmStyle,
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          data: {visit_type_id: visit_type_id, visit_type_status: visit_type_status},
          type: 'POST',
          url: baseUrl + 'site-visit/master-data/visit-type/update-status',
          success: function success(response) {
            dt_ajax.draw();
            Swal.fire({
              icon: 'success',
              title: response.message.title,
              text: response.message.text,
              customClass: {
                confirmButton: 'btn btn-success'
              }
            });
          },
          error: function error(_error) {
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
      }
    });
  });

  // Delete button handler
  $(document).on('click', '#dropdownMenuDelete', function () {
    var visit_type_id = $(this).data('id');

    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-danger me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          data: {visit_type_id: visit_type_id},
          type: 'POST',
          url: baseUrl + 'site-visit/master-data/visit-type/delete',
          success: function success(response) {
            dt_ajax.draw();
            Swal.fire({
              icon: 'success',
              title: response.message.title,
              text: response.message.text,
              customClass: {
                confirmButton: 'btn btn-success'
              }
            });
          },
          error: function error(_error) {
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
      }
    });
  });
  
  // Clearing form data when modal hidden
  modal_add_visit_type.on('hidden.bs.modal', function () {
    $('#addFormLabel > p').html('Add new visit type.');
    $('#formAddVisitType').attr('data-method', 'add');
    $('#formAddVisitType').data('method', 'add');
    fv.resetForm(true);
  });

   /**
   * 
   * Checklist Group
   * 
   */

   var modal_manage_checklist_group = $('#modalManageChecklistGroup');
   var visit_type_id_param = 0;
 
   // Manage Checklist Group type button handler
   $(document).on('click', '#dropdownMenuChecklistGroup', function () {
       var visit_type_id = $(this).data('id');
       var visit_type_code = $(this).data('code');
       var visit_type_name = $(this).data('name');
       var tmp_id;
 
       // Reinit Checklist Group with new param visit_type_id
       visit_type_id_param = visit_type_id;
 
   $('#addFormChecklistGrouplabel > h3').html('<b>' + visit_type_code + '<br><sup>'+ visit_type_name +'</sup></b><br>' + ' Checklist Group');
       modal_manage_checklist_group.modal('show');
 
       // Get data and update checkboxes
       $.ajax({
           url: baseUrl + "site-visit/master-data/visit-type/visit-type-checklist-group/show/" + visit_type_id,
           type: 'GET',
           beforeSend: function(data) {
               window.Helpers.blockUIModalLoader(modal_class_loader);
           },
           success: function(response) {
               $.each(response.data.detail, function (index, val) {
                 tmp_id = val.checklist_group_id;
                 $('#checklist_group_' + tmp_id).prop('checked', true);
               })
 
               modal_class_loader.unblock();
           }
       });
   });
 
   // Add Checklist Group Form
   var addNewChecklistGroupForm = document.getElementById('formAddChecklistGroup');
   var fv_add_checklit_group = FormValidation.formValidation(addNewChecklistGroupForm, {
       fields: {
           'checklist_group[]': {
               validators: {
                   notEmpty: {
                       message: 'Please select checklist group'
                   }
               }
           }
       },
       plugins: {
           trigger: new FormValidation.plugins.Trigger(),
           bootstrap5: new FormValidation.plugins.Bootstrap5({
               eleValidClass: '',
               rowSelector: function rowSelector(field, ele) {
                   return '.mb-3';
               }
           }),
           submitButton: new FormValidation.plugins.SubmitButton(),
           autoFocus: new FormValidation.plugins.AutoFocus()
       }
   }).on('core.form.valid', function () {
       var form_checklist_group = $('#formAddChecklistGroup');
       var url = "site-visit/master-data/visit-type/visit-type-checklist-group/store";
 
       $.ajax({
           data: form_checklist_group.serialize() + "&visit_type_id=" + visit_type_id_param,
           url: baseUrl + url,
           type: 'POST',
           success: function success(response) {
             modal_manage_checklist_group.modal('hide');
               if (response.status) {
                   Swal.fire({
                       icon: 'success',
                       title: response.message.title,
                       text: response.message.text,
                       customClass: {
                           confirmButton: 'btn btn-success'
                       }
                   });
 
                   var checkedChecklistGroup = response.data.checklist_group_id;
                   console.log(checkedChecklistGroup);
                   $('.checkbox-item-modal-add').prop('checked', false); // Deselect all checkboxes
                   for (var i = 0; i < checkedChecklistGroup.length; i++) {
                       var checkId = checkedChecklistGroup[i];
                       $('#checklist_group_' + checkId).prop('checked', true); // Check checkboxes based on saved data
                   }
               } else {
                   Swal.fire({
                       icon: 'error',
                       title: response.message.title,
                       text: response.message.text,
                       customClass: {
                           confirmButton: 'btn btn-primary'
                       }
                   });
               }
           },
           error: function error(err) {
             modal_manage_checklist_group.modal('hide');
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
   }); 
 
   // Get the checkbox
   var checkbox = document.getElementById("checkbox");
 
   // Get the close button
   var close = document.getElementsByClassName("btn-close")[0];
 
   // When the user clicks on the close button, clear the checkbox
   close.onclick = function() {
     modal_manage_checklist_group.style.display = "none";
     checkbox.checked = false;
   }
 
   // When the user clicks anywhere outside of the modal, close it and clear the checkbox
   window.onclick = function(event) {
     if (event.target == modal_manage_checklist_group) {
       modal_manage_checklist_group.style.display = "none";
       checkbox.checked = false;
     }
   }


    /**
   * 
   * Checklist Visual
   * 
   */

    var modal_manage_checklist_visual = $('#modalManageChecklistVisual');
    var visit_type_id_param = 0;
  
    // Manage Checklist Visual type button handler
    $(document).on('click', '#dropdownMenuVisualType', function () {
        var visit_type_id = $(this).data('id');
        var visit_type_code = $(this).data('code');
        var visit_type_name = $(this).data('name');
        var tmp_id;
  
        // Reinit Checklist Visual with new param visit_type_id
        visit_type_id_param = visit_type_id;
  
    $('#addFormChecklistVisuallabel > h3').html('<b>' + visit_type_code + '<br><sup>'+ visit_type_name +'</sup></b><br>' + ' Checklist Visual Type');
        modal_manage_checklist_visual.modal('show');
  
        // Get data and update checkboxes
        $.ajax({
            url: baseUrl + "site-visit/master-data/visit-type/visit-type-visual-type/show/" + visit_type_id,
            type: 'GET',
            beforeSend: function(data) {
                window.Helpers.blockUIModalLoader(modal_class_loader);
            },
            success: function(response) {
                $.each(response.data.detail, function (index, val) {
                  tmp_id = val.visit_visual_type_id;
                  $('#visual_type_' + tmp_id).prop('checked', true);
                })
  
                modal_class_loader.unblock();
            }
        });
    });
  
    // Add Checklist Group Form
    var addNewChecklistVisualForm = document.getElementById('formAddChecklistVisual');
    var fv_add_checklit_visual = FormValidation.formValidation(addNewChecklistVisualForm, {
        fields: {
            'visual_type[]': {
                validators: {
                    notEmpty: {
                        message: 'Please select checklist visual'
                    }
                }
            }
        },
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5({
                eleValidClass: '',
                rowSelector: function rowSelector(field, ele) {
                    return '.mb-3';
                }
            }),
            submitButton: new FormValidation.plugins.SubmitButton(),
            autoFocus: new FormValidation.plugins.AutoFocus()
        }
    }).on('core.form.valid', function () {
        var form_checklist_visual = $('#formAddChecklistVisual');
        var url = "site-visit/master-data/visit-type/visit-type-visual-type/store";
  
        $.ajax({
            data: form_checklist_visual.serialize() + "&visit_type_id=" + visit_type_id_param,
            url: baseUrl + url,
            type: 'POST',
            success: function success(response) {
              modal_manage_checklist_visual.modal('hide');
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message.title,
                        text: response.message.text,
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
  
                    var checkedVisualType = response.data.visit_visual_type_id;
                    console.log(checkedVisualType);
                    $('.checkbox-item-modal-add-visual').prop('checked', false); // Deselect all checkboxes
                    for (var i = 0; i < checkedVisualType.length; i++) {
                        var checkId = checkedVisualType[i];
                        $('#visual_type_' + checkId).prop('checked', true); // Check checkboxes based on saved data
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.message.title,
                        text: response.message.text,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                }
            },
            error: function error(err) {
              modal_manage_checklist_visual.modal('hide');
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
    }); 
  
    // Get the checkbox
    var checkboxVisual = document.getElementById("checkbox");
  
    // Get the close button
    var closeVisual = document.getElementsByClassName("btn-close")[0];
  
    // When the user clicks on the close button, clear the checkbox
    closeVisual.onclick = function() {
      modal_manage_checklist_visual.style.display = "none";
      checkboxVisual.checked = false;
    }
  
    // When the user clicks anywhere outside of the modal, close it and clear the checkbox
    window.onclick = function(event) {
      if (event.target == modal_manage_checklist_visual) {
        modal_manage_checklist_visual.style.display = "none";
        checkboxVisual.checked = false;
      }
    }

});
