/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table = $('.datatables-ajax');
  var ac_module       = $('.ac_module');
  var modal_add_module = $('#modalAddModule');
  var modal_class_loader = $('.modal-block-loader');
  
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  });

   // Select2 parent handler
   if (ac_module.length) {
    var $this = ac_module;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select module parent',
      minimumInputLength: 0,
      dropdownParent: $this.parent()
    });
  }

  // Data Table
  if (dt_ajax_table.length) {
    var dt_ajax = dt_ajax_table.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: baseUrl + 'settings/module/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'settings/module/get');
        },
        complete: function () {
          $.unblockUI();
        }
      },
      columns: [
        { data: 'no', orderable: false },
        { data: 'module_name' }, 
        { data: 'module_title' },
        {
          data: 'module_icon',
          render: function(data, type, row) {
            if (data) {
              return '<i class="' + data + '"></i> <code>' + data + '</code>';
            } else {
              return '-';
            }
          }
        },
        { data: 'module_class' },
        {
          data: 'module_description', orderable: false,
          render: function(data, type, row) {
            if (data) {
              return data;
            } else {
              return '-';
            }
          }
        },
        { data: 'module_status', orderable: false }
      ],
      columnDefs: [
        {
          targets: 6,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
            if (data == 2) {
              return '<span class="badge bg-label-success me-1">Appear</span>';
            } else {
              return '<span class="badge bg-label-danger me-1">Hide</span>';
            }
          }
        },
        {
          targets: 7,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
              return '' +
              '<div class="d-inline-block text-nowrap">' +
                  '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                  '<div class="dropdown-menu">' +
                      '<a id="dropdownMenuEdit" data-id="' + row.module_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-1"></i> Edit</a>' +
                      '<a id="dropdownMenuActivate" data-id="' + row.module_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Appear</a>' +
                      '<a id="dropdownMenuDeactivate" data-id="' + row.module_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Hide</a>' +
                      '<a id="dropdownMenuDelete" data-id="' + row.module_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
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
  var add_module_form = document.getElementById('formAddModule');

  // Module Form Validation
  var fv = FormValidation.formValidation(add_module_form, {
    fields: {
        module_parent_id: {
        validators: {
          notEmpty: {
            message: 'Please select module parent id'
          }
        }
      },
      module_sort: {
        validators: {
          notEmpty: {
            message: 'Please enter module sort'
          }
        }
      },
      module_name: {
        validators: {
          notEmpty: {
            message: 'Please enter module name'
          }
        }
      },
      module_title: {
        validators: {
          notEmpty: {
            message: 'Please enter module title'
          }
        }
      },
      module_icon: {
        validators: {
          notEmpty: {
            message: 'Please enter module icon'
          }
        }
      },
      module_class: {
        validators: {
          notEmpty: {
            message: 'Please enter module class'
          }
        }
      },
      module_method: {
        validators: {
          notEmpty: {
            message: 'Please enter module method'
          }
        }
      },
      module_description: {
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
    // Adding or Updating module when form successfully validate
    if ($('#formAddModule').data('method') == 'add') {
      var url = "settings/module/store";
    } else if ($('#formAddModule').data('method') == 'edit') {
      var url = "settings/module/update/" + $('#formAddModule').attr('data-id');
    } else {
      var url = "";
    }

    $.ajax({
      data: $('#formAddModule').serialize(),
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax.draw();

        if (response.status) {
          location.reload(); // Memuat ulang halaman
        }

        modal_add_module.modal('hide');

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
        modal_add_module.modal('hide');
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
    var module_id = $(this).data('id');

    // get data
    $.ajax({
      url: baseUrl + "settings/module/show/" + module_id,
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {
        if (response.data.module_status == 2) {
          $('#addStatus').prop('checked', true);
        } else {
          $('#addStatus').prop('checked', false);
        }

        // Cek module_parent_id
        if (response.data.module_parent_id == 0) {
          // Jika module_parent_id = 0, pilih opsi "This is top module"
          $('#addParent').val(0).trigger('change');
        } else if (response.data.module_parent_id > 0) {
          // Jika module_parent_id lebih dari 0, pilih opsi dengan module_id yang sesuai
          $('#addParent').val(response.data.module_parent_id).trigger('change');
        }

        $('#addSort').val(response.data.module_sort);
        $('#addName').val(response.data.module_name);
        $('#addTitle').val(response.data.module_title);
        $('#addIcon').val(response.data.module_icon);
        $('#addClass').val(response.data.module_class);
        $('#addMethod').val(response.data.module_method);
        $('#addParam').val(response.data.module_param);
        $('#addDesc').val(response.data.module_description);
        modal_class_loader.unblock();
      }
    });

    $('#addFormLabel > p').html('Edit module.');
    $('#formAddModule').attr('data-method', 'edit');
    $('#formAddModule').data('method', 'edit');
    $('#formAddModule').attr('data-id', module_id);
    modal_add_module.modal('show');
  });
  
  // Active / Deactive status button handler
  $(document).on('click', '.dropdownMenuStatusUpdate', function () {
    var module_id = $(this).data('id'),
      module_status = $(this).data('status');

    if (module_status == 2) {
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
          data: {module_id: module_id, module_status: module_status},
          type: 'POST',
          url: baseUrl + 'settings/module/update-status',
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
    var module_id = $(this).data('id');

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
          data: {module_id: module_id},
          type: 'POST',
          url: baseUrl + 'settings/module/delete',
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
  modal_add_module.on('hidden.bs.modal', function () {
    $('#addFormLabel > p').html('Add new module.');
    $('#formAddModule').attr('data-method', 'add');
    $('#formAddModule').data('method', 'add');
    $('#addParent').val('').trigger('change');
    fv.resetForm(true);
  });

});
