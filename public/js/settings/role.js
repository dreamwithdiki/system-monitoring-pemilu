/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table = $('.datatables-ajax');
  var modal_add_role = $('#modalAddRole');
  var modal_edit_role = $('#modalEditRole');
  var modal_class_loader = $('.modal-block-loader');
  
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
      ajax: {
        url: baseUrl + 'settings/role/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'settings/role/get');
        },
        complete: function () {
          $.unblockUI();
        }
      },
      columns: [
        { data: 'no', orderable: false },
        { data: 'role_name' },
        {
          data: 'role_description', orderable: false,
          render: function(data, type, row) {
            if (data) {
              return data;
            } else {
              return '-';
            }
          }
        },
        { data: 'role_status', orderable: false }
      ],
      columnDefs: [
        {
          targets: 3,
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
          targets: 4,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
              return '' +
              '<div class="d-inline-block text-nowrap">' +
                  '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                  '<div class="dropdown-menu">' +
                      '<a id="dropdownMenuEdit" data-id="' + row.role_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-1"></i> Edit</a>' +
                      '<a id="dropdownMenuActivate" data-id="' + row.role_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
                      '<a id="dropdownMenuDeactivate" data-id="' + row.role_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Deactivate</a>' +
                      '<a id="dropdownMenuDelete" data-id="' + row.role_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
                  '</div>' +
              '</div>';
          }
        }
      ],
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
    });
  }
  // Add Form
  var add_role_form = document.getElementById('formAddRole');

  // Role Form Validation
  var fv = FormValidation.formValidation(add_role_form, {
    fields: {
      role_name: {
        validators: {
          notEmpty: {
            message: 'Please enter role name'
          }
        }
      },
      'modules[]': {
        validators: {
          choice: {
            min: 1,
            message: 'Please select at least one module'
          }
        }
      },
      role_desc: {
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        // eleInvalidClass: 'is-invalid', 
        eleValidClass: '',
        rowSelector: function rowSelector(field, ele) {
          // field is the field name & ele is the field element
          switch (field) {
            case 'role_name':
              return '.mb-3';
            case 'modules':
              return '.mb-6';
            default:
              return '.row';
          }
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      // Submit the form when all fields are valid
      // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  }).on('core.form.valid', function () {
    // Adding role when form successfully validate
    if ($('#formAddRole').data('method') == 'add') {
      var url = "settings/role/store";
    } else {
      var url = "";
    }

    $.ajax({
      data: $('#formAddRole').serialize(),
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax.draw();
        modal_add_role.modal('hide');

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
        modal_add_role.modal('hide');
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

  // Edit Form
  var edit_role_form = document.getElementById('formEditRole');

  // Role Form Validation
  var fv_edit = FormValidation.formValidation(edit_role_form, {
    fields: {
      role_name: {
        validators: {
          notEmpty: {
            message: 'Please enter role name'
          }
        }
      },
      'modules[]': {
        validators: {
          choice: {
            min: 1,
            message: 'Please select at least one module'
          }
        }
      },
      role_desc: {
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        eleValidClass: '',
        rowSelector: function rowSelector(field, ele) {
          // field is the field name & ele is the field element
          switch (field) {
            case 'role_name':
              return '.mb-3';
            case 'modules':
              return '.mb-6';
            default:
              return '.row';
          }
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      // Submit the form when all fields are valid
      // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  }).on('core.form.valid', function () {
    // Updating product when form successfully validate
    if ($('#formEditRole').data('method') == 'edit') {
      var url = "settings/role/update/" + $('#formEditRole').attr('data-id');
    } else {
      var url = "";
    }

    $.ajax({
      data: $('#formEditRole').serialize(),
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax.draw();
        modal_edit_role.modal('hide');

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
        modal_edit_role.modal('hide');
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
  // End Edit Form
  

  // Edit button handler
  $(document).on('click', '#dropdownMenuEdit', function () {
    var role_id = $(this).data('id');

    // get data
    $.ajax({
      url: baseUrl + "settings/role/show/" + role_id,
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {
        if (response.data.role_status == 2) {
          $('#editStatus').prop('checked', true);
        } else {
          $('#editStatus').prop('checked', false);
        }

        $('#editName').val(response.data.role_name)
        $('#editDesc').val(response.data.role_description);

         // Mengupdate pengecekan pada checkbox
        updateModuleCheckboxes(response.data.assign_module);
    
        modal_class_loader.unblock();
      }
    });

    $('#editFormLabel > p').html('Edit role.');
    $('#formEditRole').attr('data-method', 'edit');
    $('#formEditRole').data('method', 'edit');
    $('#formEditRole').attr('data-id', role_id);

    modal_edit_role.modal('show');
  });

  // Fungsi untuk mengupdate pengecekan pada checkbox
  function updateModuleCheckboxes(assign_module) {
    // Menghapus semua pengecekan pada checkbox modul
    $('.form-check-input.checkbox-item').prop('checked', false);

    // Mengiterasi modul yang diberikan
    $.each(assign_module, function(index, val) {
      var module_id = val.module_id;

      // Memeriksa dan mencentang checkbox modul
      var checkbox = $('input[type="checkbox"][value="' + module_id + '"]');
      checkbox.prop('checked', true);

      // Menampilkan elemen parent jika checkbox modul dicentang
      if (checkbox.is(':checked')) {
        checkbox.closest('li').show('slow');
      } else {
        checkbox.closest('li').hide('slow');
      }
    });

    // Menampilkan checkbox yang tidak tercentang
    $('input[type="checkbox"].checkbox-item:not(:checked)').each(function() {
      $(this).closest('li').show('slow');
    });
    
  }
  
  // Active / Deactive status button handler
  $(document).on('click', '.dropdownMenuStatusUpdate', function () {
    var role_id = $(this).data('id'),
    role_status = $(this).data('status');

    if (role_status == 2) {
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
          data: {role_id: role_id, role_status: role_status},
          type: 'POST',
          url: baseUrl + 'settings/role/update-status',
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
    var role_id = $(this).data('id');

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
          data: {role_id: role_id},
          type: 'POST',
          url: baseUrl + 'settings/role/delete',
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
  modal_add_role.on('hidden.bs.modal', function () {
    $('#addFormLabel > p').html('Add new role.');
    $('#formAddRole').attr('data-method', 'add');
    $('#formAddRole').data('method', 'add');
    fv.resetForm(true);
  });

});
