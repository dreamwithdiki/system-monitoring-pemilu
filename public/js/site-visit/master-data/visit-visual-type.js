/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table = $('.datatables-ajax');
  var modal_add_visit_visual_type = $('#modalAddVisitVisualType');
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
        url: baseUrl + 'site-visit/master-data/visit-visual-type/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'site-visit/master-data/visit-visual-type/get');
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
        { 
            data: 'visit_visual_type_sort', orderable: false ,
            render: function(data, type, row) {
                return '<span class="badge badge-center rounded-pill bg-secondary">' + data + '</span>';
            }
        },
        { data: 'visit_visual_type_name' },
        {
            data: 'visit_visual_type_desc',
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
        { data: 'visit_visual_type_status', orderable: false },
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
                      '<a id="dropdownMenuEdit" data-id="' + row.visit_visual_type_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>' +
                      '<a id="dropdownMenuActivate" data-id="' + row.visit_visual_type_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
                      '<a id="dropdownMenuDeactivate" data-id="' + row.visit_visual_type_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Deactivate</a>' +
                      '<a id="dropdownMenuDelete" data-id="' + row.visit_visual_type_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
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
  var add_visit_visual_type_form = document.getElementById('formAddVisitVisualType');

  // Site Form Validation
  var fv = FormValidation.formValidation(add_visit_visual_type_form, {
    fields: {
        visit_visual_type_sort: {
        validators: {
          notEmpty: {
            message: 'Please enter visit visual type sort'
          }
        }
      },
      visit_visual_type_name: {
        validators: {
          notEmpty: {
            message: 'Please enter visit visual type name'
          }
        }
      },
      visit_visual_type_reference: {
        validators: {
          notEmpty: {
            message: 'Please enter visit visual reference'
          }
        }
      },
      visit_visual_type_desc: {
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
    // Adding or Updating visit-visual-type when form successfully validate
    if ($('#formAddVisitVisualType').data('method') == 'add') {
      var url = "site-visit/master-data/visit-visual-type/store";
    } else if ($('#formAddVisitVisualType').data('method') == 'edit') {
      var url = "site-visit/master-data/visit-visual-type/update/" + $('#formAddVisitVisualType').attr('data-id');
    } else {
      var url = "";
    }

    $.ajax({
      data: $('#formAddVisitVisualType').serialize(),
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax.draw();
        modal_add_visit_visual_type.modal('hide');

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
        modal_add_visit_visual_type.modal('hide');
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
    var visit_visual_type_id = $(this).data('id');

    // get data
    $.ajax({
      url: baseUrl + "site-visit/master-data/visit-visual-type/show/" + visit_visual_type_id,
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {
        if (response.data.visit_visual_type_status == 2) {
          $('#addStatus').prop('checked', true);
        } else {
          $('#addStatus').prop('checked', false);
        }

        $('#addSort').val(response.data.visit_visual_type_sort);
        $('#addName').val(response.data.visit_visual_type_name);
        $('#addReference').val(response.data.visit_visual_type_reference);
        $('#addDesc').val(response.data.visit_visual_type_desc);
        modal_class_loader.unblock();
      }
    });

    $('#addFormLabel > p').html('Edit visit visual type.');
    $('#formAddVisitVisualType').attr('data-method', 'edit');
    $('#formAddVisitVisualType').data('method', 'edit');
    $('#formAddVisitVisualType').attr('data-id', visit_visual_type_id);
    modal_add_visit_visual_type.modal('show');
  });
  
  // Active / Deactive status button handler
  $(document).on('click', '.dropdownMenuStatusUpdate', function () {
    var visit_visual_type_id = $(this).data('id'),
      visit_visual_type_status = $(this).data('status');

    if (visit_visual_type_status == 2) {
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
          data: {visit_visual_type_id: visit_visual_type_id, visit_visual_type_status: visit_visual_type_status},
          type: 'POST',
          url: baseUrl + 'site-visit/master-data/visit-visual-type/update-status',
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
    var visit_visual_type_id = $(this).data('id');

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
          data: {visit_visual_type_id: visit_visual_type_id},
          type: 'POST',
          url: baseUrl + 'site-visit/master-data/visit-visual-type/delete',
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
  modal_add_visit_visual_type.on('hidden.bs.modal', function () {
    $('#addFormLabel > p').html('Add new visit visual type.');
    $('#formAddVisitVisualType').attr('data-method', 'add');
    $('#formAddVisitVisualType').data('method', 'add');
    fv.resetForm(true);
  });

});
