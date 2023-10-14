/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table = $('.datatables-ajax');
  var modal_add_job_type = $('#modalAddJobType');
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
        url: baseUrl + 'job-order/master-data/job-type/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'job-order/master-data/job-type/get');
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
        { data: 'job_type_code' },
        { data: 'job_type_name' },
        {
          data: 'job_type_desc',
          orderable: false,
          render: function (data, type, row) {
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
        { data: 'job_type_status', orderable: false },
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
              '<a id="dropdownMenuEdit" data-id="' + row.job_type_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>' +
              '<div class="dropdown-divider"></div>' +
              '<a id="dropdownMenuChecklistGroup" data-id="' + row.job_type_id + '" data-code="' + row.job_type_code + '" data-name="' + row.job_type_name + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-list-check me-1"></i> Checklist Group</a>' +
              '<div class="dropdown-divider"></div>' +
              '<a id="dropdownMenuActivate" data-id="' + row.job_type_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
              '<a id="dropdownMenuDeactivate" data-id="' + row.job_type_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Deactivate</a>' +
              '<a id="dropdownMenuDelete" data-id="' + row.job_type_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
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
    $(document).on('keyup', '.search', function (e) {
      var $this = $(this);
      clearTimeout(typingTimer);
      typingTimer = setTimeout(function () {
        dt_ajax_table.DataTable().search($this.val()).draw();
      }, 1200);
    });
  }

  $(document).on('click', '.show-more', function (e) {
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

  $(document).on('click', '.show-less', function (e) {
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
  var add_job_type_form = document.getElementById('formAddJobType');

  // Site Form Validation
  var fv = FormValidation.formValidation(add_job_type_form, {
    fields: {
      job_type_code: {
        validators: {
          notEmpty: {
            message: 'Please enter job type code'
          }
        }
      },
      job_type_name: {
        validators: {
          notEmpty: {
            message: 'Please enter job type name'
          }
        }
      },
      job_type_desc: {
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
    // Adding or Updating job-type when form successfully validate
    if ($('#formAddJobType').data('method') == 'add') {
      var url = "job-order/master-data/job-type/store";
    } else if ($('#formAddJobType').data('method') == 'edit') {
      var url = "job-order/master-data/job-type/update/" + $('#formAddJobType').attr('data-id');
    } else {
      var url = "";
    }

    $.ajax({
      data: $('#formAddJobType').serialize(),
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax.draw();
        modal_add_job_type.modal('hide');

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
        modal_add_job_type.modal('hide');
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
    var job_type_id = $(this).data('id');

    // get data
    $.ajax({
      url: baseUrl + "job-order/master-data/job-type/show/" + job_type_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {
        if (response.data.job_type_status == 2) {
          $('#addStatus').prop('checked', true);
        } else {
          $('#addStatus').prop('checked', false);
        }

        $('#addCode').val(response.data.job_type_code);
        $('#addName').val(response.data.job_type_name);
        $('#addDesc').val(response.data.job_type_desc);
        modal_class_loader.unblock();
      }
    });

    $('#addFormLabel > p').html('Edit job type.');
    $('#formAddJobType').attr('data-method', 'edit');
    $('#formAddJobType').data('method', 'edit');
    $('#formAddJobType').attr('data-id', job_type_id);
    modal_add_job_type.modal('show');
  });

  // Active / Deactive status button handler
  $(document).on('click', '.dropdownMenuStatusUpdate', function () {
    var job_type_id = $(this).data('id'),
      job_type_status = $(this).data('status');

    if (job_type_status == 2) {
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
          data: { job_type_id: job_type_id, job_type_status: job_type_status },
          type: 'POST',
          url: baseUrl + 'job-order/master-data/job-type/update-status',
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
    var job_type_id = $(this).data('id');

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
          data: { job_type_id: job_type_id },
          type: 'POST',
          url: baseUrl + 'job-order/master-data/job-type/delete',
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
  modal_add_job_type.on('hidden.bs.modal', function () {
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
  var job_type_id_param = 0;

  // Manage Checklist Group type button handler
  $(document).on('click', '#dropdownMenuChecklistGroup', function () {
    var job_type_id = $(this).data('id');
    var job_type_code = $(this).data('code');
    var job_type_name = $(this).data('name');
    var tmp_id;

    // Reinit Checklist Group with new param job_type_id
    job_type_id_param = job_type_id;

    $('#addFormChecklistGrouplabel > h3').html('<b>' + job_type_code + '<br><sup>' + job_type_name + '</sup></b><br>' + ' Checklist Group');
    modal_manage_checklist_group.modal('show');

    // Get data and update checkboxes
    $.ajax({
      url: baseUrl + "job-order/master-data/job-type/job-type-checklist-group/show/" + job_type_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {
        $.each(response.data.detail, function (index, val) {
          tmp_id = val.job_checklist_group_id;
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
    var url = "job-order/master-data/job-type/job-type-checklist-group/store";

    $.ajax({
      data: form_checklist_group.serialize() + "&job_type_id=" + job_type_id_param,
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
  close.onclick = function () {
    modal_manage_checklist_group.style.display = "none";
    checkbox.checked = false;
  }

  // When the user clicks anywhere outside of the modal, close it and clear the checkbox
  window.onclick = function (event) {
    if (event.target == modal_manage_checklist_group) {
      modal_manage_checklist_group.style.display = "none";
      checkbox.checked = false;
    }
  }

  // Clearing form data when modal hidden
  modal_manage_checklist_group.on('hidden.bs.modal', function () {
    fv_add_checklit_group.resetForm(true);
  });

});
