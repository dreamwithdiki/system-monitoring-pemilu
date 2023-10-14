/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table = $('.datatables-ajax');
  var ac_product_type = $('.ac_product_type');
  var modal_add_product = $('#modalAddProduct');
  var modal_class_loader = $('.modal-block-loader');
  var typingTimer;

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  });

  // Select2 product type name handler
  if (ac_product_type.length) {
    var $this = ac_product_type;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select product type name',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/product-type/find',
        dataType: 'json',
        data: function (params) {
          return {
            _token: CSRF_TOKEN,
            search: params.term // search term
          };
        },
        processResults: function (response) {
          return {
            results: response
          };
        },
      },
      dropdownParent: $this.parent()
    });
  }

  // Data Table
  if (dt_ajax_table.length) {
    var dt_ajax = dt_ajax_table.DataTable({
      processing: true,
      serverSide: true,
      initComplete: onInit,
      ajax: {
        url: baseUrl + 'job-order/master-data/product/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'job-order/master-data/product/get');
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
        { data: 'product_code' },
        { data: 'product_name' },
        { data: 'product_type_name' },
        {
          data: 'product_desc',
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
        { data: 'product_status', orderable: false },
      ],
      columnDefs: [
        {
          targets: 5,
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
          targets: 6,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
            return '' +
              '<div class="d-inline-block text-nowrap">' +
              '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
              '<div class="dropdown-menu">' +
              '<a id="dropdownMenuEdit" data-id="' + row.product_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>' +
              '<a id="dropdownMenuActivate" data-id="' + row.product_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
              '<a id="dropdownMenuDeactivate" data-id="' + row.product_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Deactivate</a>' +
              '<a id="dropdownMenuDelete" data-id="' + row.product_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
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
  var add_product_form = document.getElementById('formAddProduct');

  // Site Form Validation
  var fv = FormValidation.formValidation(add_product_form, {
    fields: {
      product_type_id: {
        validators: {
          notEmpty: {
            message: 'Please select product type name'
          }
        }
      },
      product_name: {
        validators: {
          notEmpty: {
            message: 'Please enter product name'
          }
        }
      },
      product_code: {
        validators: {
          notEmpty: {
            message: 'Please enter product code'
          }
        }
      },
      product_desc: {
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
    // Adding or Updating checklist when form successfully validate
    if ($('#formAddProduct').data('method') == 'add') {
      var url = "job-order/master-data/product/store";
    } else if ($('#formAddProduct').data('method') == 'edit') {
      var url = "job-order/master-data/product/update/" + $('#formAddProduct').attr('data-id');
    } else {
      var url = "";
    }

    $.ajax({
      data: $('#formAddProduct').serialize(),
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax.draw();
        modal_add_product.modal('hide');

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
        modal_add_product.modal('hide');
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
    var product_id = $(this).data('id');

    // get data
    $.ajax({
      url: baseUrl + "job-order/master-data/product/show/" + product_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {
        if (response.data.product_status == 2) {
          $('#addStatus').prop('checked', true);
        } else {
          $('#addStatus').prop('checked', false);
        }

        if (response.data.product_type) {
          var option = new Option(response.data.product_type.product_type_name, response.data.product_type.product_type_id, true, true);
          $('#addProductType').append(option).trigger('change');
        }

        $('#addName').val(response.data.product_name);
        $('#addCode').val(response.data.product_code);
        $('#addDesc').val(response.data.product_desc);
        modal_class_loader.unblock();
      }
    });

    $('#addFormLabel > p').html('Edit product.');
    $('#formAddProduct').attr('data-method', 'edit');
    $('#formAddProduct').data('method', 'edit');
    $('#formAddProduct').attr('data-id', product_id);
    modal_add_product.modal('show');
  });

  // Active / Deactive status button handler
  $(document).on('click', '.dropdownMenuStatusUpdate', function () {
    var product_id = $(this).data('id'),
      product_status = $(this).data('status');

    if (product_status == 2) {
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
          data: { product_id: product_id, product_status: product_status },
          type: 'POST',
          url: baseUrl + 'job-order/master-data/product/update-status',
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
    var product_id = $(this).data('id');

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
          data: { product_id: product_id },
          type: 'POST',
          url: baseUrl + 'job-order/master-data/product/delete',
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
  modal_add_product.on('hidden.bs.modal', function () {
    $('#addFormLabel > p').html('Add new product.');
    $('#formAddProduct').attr('data-method', 'add');
    $('#formAddProduct').data('method', 'add');
    $('#addProductType').val('').trigger('change');
    fv.resetForm(true);
  });

});
