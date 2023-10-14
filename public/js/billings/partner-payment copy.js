/**
 * Billings | Partner Payment (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN                 = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table              = $('.datatables-ajax');
  var modal_add_partner_payment  = $('#modalAddPartnerPayment'); 
  var modal_edit_partner_payment = $('#modalEditPartnerPayment');
  var modal_detail_partner_payment = $('#modalDetailPartnerPayment');
  var modal_paid_partner_payment = $('#modalPaidPartnerPayment');
  var modal_class_loader         = $('.modal-block-loader');
  var dt_add_visit_order_table   = $('.dt-add-visit-order');
  var dt_edit_visit_order_table  = $('.dt-edit-visit-order');
  var dt_paid_visit_order_table  = $('.dt-paid-visit-order');
  var partner_payment_url        = baseUrl + 'billings/partner-payment/';
  var typingTimer;
  
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  });

  // Data Table Parttner Payment
  if (dt_ajax_table.length) {
    var dt_ajax = dt_ajax_table.DataTable({
      processing: true,
      serverSide: true,
      initComplete: onInit,
      ajax: {
        url: partner_payment_url + 'get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(partner_payment_url + 'get');
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
        { data: 'partner_payment_number' }, 
        { data: 'partner_payment_name' },
        { data: 'partner_payment_month' },
        { data: 'partner_payment_year' },
        { data: 'partner_payment_status', orderable: false }
      ],
      columnDefs: [
        {
          targets: 5,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
            if (row.partner_payment_status == 1) {
              return '<span class="badge bg-label-primary me-1">Open</span>';
            } else if (row.partner_payment_status == 2) {
              return '<span class="badge bg-label-success me-1">Submitted</span>';
            } else if (row.partner_payment_status == 3) {
              return '<span class="badge bg-label-info me-1">Paid</span>';
            } else if (row.partner_payment_status == 5) {
              return '<span class="badge bg-label-danger me-1">Deleted</span>';
            }else {
              return '<span class="badge bg-label-warning me-1">Unknown</span>';
            }
          }
        },
        {
          targets: 6,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
              var text = '';
                text += '' +
                '<div class="d-inline-block text-nowrap">' +
                    '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                    '<div class="dropdown-menu">' +
              
                        '<a id="dropdownMenuEdit" data-id="' + row.partner_payment_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>' +
                        '<div class="dropdown-divider"></div>' +
                        '<a id="dropdownMenuDetail" data-id="' + row.partner_payment_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-detail me-1"></i> Detail</a>' +
                        '<div class="dropdown-divider"></div>' +
                        '<a id="dropdownMenuDelete" data-id="' + row.partner_payment_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>';

                        // check partner payment status
                        if (row.partner_payment_status === 1) {
                          text += '<div class="dropdown-divider"></div>' +
                          '<a id="dropdownMenuSubmit" data-id="' + row.partner_payment_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-paper-plane me-1"></i> Submit</a>';
                        }

                        if (row.partner_payment_status === 2) {
                          text += '<div class="dropdown-divider"></div>' +
                          '<a id="dropdownMenuPaid" data-id="' + row.partner_payment_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-credit-card-front me-1"></i> Paid</a>';
                        }

                        text += '</div>' +
                  '</div>';
                return text;
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

    // fixedColumns visit order
    // --------------------------------------------------------------------

    if (dt_add_visit_order_table.length) {
      var dt_add_visit_order = dt_add_visit_order_table.DataTable({
        ajax: {
          url: baseUrl + 'billings/partner-payment/visit-order/get-visit-order',
          beforeSend: function () {
            window.Helpers.blockUIPageLoader(baseUrl + 'billings/partner-payment/visit-order/get-visit-order');
          },
          complete: function () {
            $.unblockUI();
          },
         },
        columns: [
          { data: 'visit_order_id' },
          { data: 'visit_order_number'},
          { data: 'visit_order_date' },
          { data: 'visit_order_due_date' },
          { data: 'client_name' },
          { data: 'site_name' },
          {
            data: 'visit_order_location',
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
          {
            data: 'partner_name',
            render: function(data) {
              if (data) {
                return data;
              } else {
                return '-';
              }
            }
          },
          {
            data: 'download_status',
            searchable: false,
            orderable: false,
            render: function(data) {
              if (data == "Downloaded") {
                return '<span class="badge bg-label-primary me-1">' + data + '</span>';
              } else if (data == "Not Downloaded") {
                return '<span class="badge bg-label-secondary me-1">' + data + '</span>';
              } else {
                return '<span class="badge bg-label-danger me-1">-</span>';
              }
            }
          },
          { data: 'visit_order_status' },
        ],
        columnDefs: [
          {
            // For Checkboxes
            targets: 0,
            searchable: false,
            orderable: false,
            render: function () {
              return '<input type="checkbox" class="dt-checkboxes form-check-input">';
            },
            checkboxes: {
              selectRow: true,
              selectAllRender: '<input type="checkbox" class="form-check-input">'
            }
          },
          {
            // Label
            targets: -1,
            searchable: false,
            render: function (data, type, row, meta) {
              if (data == 6) {
                  return '<span class="badge bg-label-primary me-1">Validated</span>';
              } else {
                return '<span class="badge bg-label-danger me-1">Unknown</span>';
              }
            }
          },
        ],
        dom: '<"d-flex justify-content-between align-items-center row"<"col-sm-12 col-md-2 d-flex"f><"col-sm-12 col-md-10 d-none"i>>t',
        select: {
          // Select style
          style: 'multi'
        },
        scrollY: '300px',
        scrollX: true,
        scrollCollapse: true,
        paging: false,
        info: false,
        // Fixed column option
        fixedColumns: true,
        // fixedColumns: {
        //   leftColumns: 4, // Number of columns to keep fixed to the left
        //   heightMatch: 'auto', // Match the fixed columns' height to the content
        //   // Customize the CSS styles for the fixed columns
        //   fixedColumns: {
        //     left: {
        //       width: '500px', // Set the desired width for the fixed columns
        //     },
        //   },
        // }

      });
    }

    setTimeout(() => {
      $('.dataTables_filter .form-control').removeClass('form-control-sm');
      $('.dataTables_length .form-select').removeClass('form-select-sm');
    }, 200);

    $(document).on('click', '.show-more', function(e) {
      e.preventDefault();
      var $this = $(this);
      var $shortDesc = $this.prev('.short-desc');
      var $fullDesc = $shortDesc.next('.full-desc');
      $shortDesc.hide();
      $fullDesc.show();
      $this.text('Show Less');
      var row = dt_add_visit_order.row($this.closest('tr')).data();
      row.expanded = true; // Menandai bahwa deskripsi telah di-expand
      dt_add_visit_order.row($this.closest('tr')).data(row);
    });
    
    $(document).on('click', '.show-less', function(e) {
      e.preventDefault();
      var $this = $(this);
      var $fullDesc = $this.prev('.full-desc');
      var $shortDesc = $fullDesc.prev('.short-desc');
      $fullDesc.hide();
      $shortDesc.show();
      $this.text('Show More');
      var row = dt_add_visit_order.row($this.closest('tr')).data();
      row.expanded = false; // Menandai bahwa deskripsi telah di-collapse
      dt_add_visit_order.row($this.closest('tr')).data(row);
    });
    
  // attachment config
  const previewTemplateAttachment = `
    <div class="dz-preview dz-file-preview">
      <div class="dz-details">
        <div class="dz-thumbnail">
          <img data-dz-thumbnail>
          <span class="dz-nopreview">No preview</span>
          <div class="dz-success-mark"></div>
          <div class="dz-error-mark"></div>
          <div class="dz-error-message"><span data-dz-errormessage></span></div>
          <div class="progress">
            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
          </div>
        </div>
        <div class="dz-filename" data-dz-name></div>
        <div class="dz-size" data-dz-size></div>
      </div>
    </div>`;


  const attachmentDropzoneMulti = new Dropzone('#add-file-payment', {
    url: '#',
    paramName: 'partner_payment_files',
    previewTemplate: previewTemplateAttachment,
    autoProcessQueue: false,
    acceptedFiles: 'image/jpg, image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-powerpoint, application/vnd.openxmlformats-officedocument.presentationml.presentation',
    parallelUploads: 20,
    maxFilesize: 1, // max size 1 MB (1024 KB)
    addRemoveLinks: true,
  });
  
  // Add Form
  var add_partner_payment_form = document.getElementById('formAddPartnerPayment');

  // Partner Payment Form Validation
  var fv = FormValidation.formValidation(add_partner_payment_form, {
    fields: {
      partner_payment_name: {
        validators: {
          notEmpty: {
            message: 'Please enter partner payment name'
          }
        }
      },
      partner_payment_month: {
        validators: {
          notEmpty: {
            message: 'Please enter partner payment month'
          }
        }
      },
      partner_payment_year: {
        validators: {
          notEmpty: {
            message: 'Please enter partner payment year'
          }
        }
      },
      partner_payment_desc: {
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
    var saveButton = document.querySelector('[data-save-button]');
    var saveAndSubmitButton = document.querySelector('[data-save-and-submit-button]');

    saveButton.addEventListener('click', function(event) {
      event.preventDefault();
      submitForm(false); // Pass 'false' to indicate "Save" action
    });

    saveAndSubmitButton.addEventListener('click', function(event){
      event.preventDefault();
      submitForm(true); // Pass 'true' to indicate "Save and Submit" action
    });

    function submitForm(submitFlag){
      // Adding partner payment when form successfully validate
      var url_attachment = partner_payment_url + "store";
      var rejected_files_attachment = attachmentDropzoneMulti.getRejectedFiles();

      if (Array.isArray(rejected_files_attachment) && rejected_files_attachment.length) {
        Swal.fire({
          title: 'Check your file attachment!',
          text: 'Remove any wrong file attachment.',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-primary'
          }
        });
      } else {
        var form_data  = new FormData(add_partner_payment_form);

        // checkbox
        var selectedRows = dt_add_visit_order.rows({ selected: true }).data();
        var selectedVisitOrderIds = selectedRows.map(row => row.visit_order_id);

        // Append array of IDs without JSON encoding
        for (var i = 0; i < selectedVisitOrderIds.length; i++) {
          form_data.append('selected_visit_orders[]', selectedVisitOrderIds[i]);
        }

        var product_attachment = attachmentDropzoneMulti.getAcceptedFiles();
        product_attachment.forEach((val, index) => {
          form_data.append('partner_payment_files[]', val); // append attachment file to form data
        });

        // set the appropriate action value based on the submitFlag
        var actionValue = submitFlag ? 'save_and_submit' : 'save';
        form_data.append('action', actionValue);

        $.ajax({
          data: form_data,
          url: url_attachment,
          type: 'POST',
          processData: false,
          contentType: false,
          success: function success(response) {
            // console.log(response);
            dt_ajax.draw();
            modal_add_partner_payment.modal('hide');
            if (response.status) {
              attachmentDropzoneMulti.removeAllFiles(true);
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
            modal_add_partner_payment.modal('hide');
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

    }
    
  });
  // End Add Form

  // Clearing form data when modal hidden
  modal_add_partner_payment.on('hidden.bs.modal', function () {
    $('#addFormLabel > p').html('Add new partner payment data.');
    $('#formAddPartnerPayment').attr('data-method', 'add');
    $('#formAddPartnerPayment').data('method', 'add');
    $('#partner_payment_name').val(null);
    $('#partner_payment_desc').val(null);
    // fv.resetForm(true);
  });

  // Edit Form
  var edit_partner_payment_form = document.getElementById('formEditPartnerPayment');

  // partner payment Form Validation
  var fv_edit = FormValidation.formValidation(edit_partner_payment_form, {
    fields: {
        partner_payment_name: {
          validators: {
            notEmpty: {
              message: 'Please enter partner payment name'
            }
          }
        },
        partner_payment_month: {
          validators: {
            notEmpty: {
              message: 'Please enter partner payment month'
            }
          }
        },
        partner_payment_year: {
          validators: {
            notEmpty: {
              message: 'Please enter partner payment year'
            }
          }
        },
        partner_payment_file: {
        },
        partner_payment_desc: {
        },
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
    // Updating partner payment when form successfully validate
    var url_attachment = partner_payment_url + "update/" + $('#formEditPartnerPayment').attr('data-id');
    var rejected_files_attachment = attachmentEditDropzoneMulti.getRejectedFiles();

    if (Array.isArray(rejected_files_attachment) && rejected_files_attachment.length) {
      Swal.fire({
        title: 'Check your file attachment!',
        text: 'Remove any wrong file attachment.',
        icon: 'error',
        customClass: {
          confirmButton: 'btn btn-primary'
        }
      });
    } else {
      var form_data = new FormData(edit_partner_payment_form);

      // checkbox
      var selectedRows = dt_edit_visit_order.rows({ selected: true }).data();
      var selectedVisitOrderIds = selectedRows.map(row => row.visit_order_id);

      // Append array of IDs without JSON encoding
      for (var i = 0; i < selectedVisitOrderIds.length; i++) {
        form_data.append('selected_visit_orders[]', selectedVisitOrderIds[i]);
      }

      $.ajax({
        data: form_data,
        url: url_attachment,
        type: 'POST',
        processData: false,
        contentType: false,
        success: function success(response) {
          // console.log(response);
          dt_ajax.draw();
          modal_edit_partner_payment.modal('hide');
          if (response.status) {
            attachmentEditDropzoneMulti.removeAllFiles(true);
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
          modal_edit_partner_payment.modal('hide');
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
  // End Edit Form

  // Edit button handler
  var selectedVisitOrders = []; // Declare the array for selected visit orders
  var selectedRows = []; // Array to track selected rows

  var partner_payment_id; // variabel global untuk menampung nilai partner_payment_id
  $(document).on('click', '#dropdownMenuEdit', function () {
    partner_payment_id = $(this).data('id');
    partner_payment_id_tab = partner_payment_id; // ditangkap di Tab Attachment

    // Clear the selectedVisitOrders and selectedRows arrays
    selectedVisitOrders = [];
    selectedRows = [];

    // get data
    $.ajax({
      url: partner_payment_url + "show/" + partner_payment_id,
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {
        selectedVisitOrders = response.data.partner_detail.map(order => order.visit_order_id);

        $('#EditNumber').val(response.data.partner_payment_number);
        $('#EditName').val(response.data.partner_payment_name);
        if (response.data.partner_payment_month) {
          $('#EditMonth').val(response.data.partner_payment_month);
        }

        if (response.data.partner_payment_year) {
          $('#EditYear').val(response.data.partner_payment_year);
        }
        $('#EditDesc').val(response.data.partner_payment_desc);

        // Highlight selected visit orders in DataTable
        dt_edit_visit_order.rows().every(function (rowIdx, tableLoop, rowLoop) {
          var rowNode = this.node();
          var data = this.data();

          // Check if the current visit_order's visit_order_id matches the selectedVisitOrders
          var isRowSelected = selectedRows.includes(data.visit_order_id);

          if (selectedVisitOrders.includes(data.visit_order_id)) {
            $(rowNode).addClass('selected');
            $('#edit-checkbox', rowNode).prop('checked', true);
            if (!isRowSelected) {
              selectedRows.push(data.visit_order_id); // Add to selected rows
            }
          } else {
            $(rowNode).removeClass('selected');
            $('#edit-checkbox', rowNode).prop('checked', false);
            if (isRowSelected) {
              selectedRows = selectedRows.filter(id => id !== data.visit_order_id); // Remove from selected rows
            }
          }
        });

        // Redraw the DataTable to update the visual appearance of checkboxes and highlighted rows
        dt_edit_visit_order.draw();

        modal_class_loader.unblock();
      }
    });

    $('#editFormLabel > p').html('Edit partner payment data.');
    $('#formEditPartnerPayment').attr('data-method', 'edit');
    $('#formEditPartnerPayment').data('method', 'edit');
    $('#formEditPartnerPayment').attr('data-id', partner_payment_id);

    dt_ajax_attachment.draw(); // refresh tab attachment

    modal_edit_partner_payment.modal('show');
  });

  // Add event listener for "Select All" checkbox
  $('#select-all-checkbox').on('change', function () {
    var isChecked = $(this).prop('checked');
    dt_edit_visit_order.rows().every(function () {
      var rowNode = this.node();
      var data = this.data();
      
      $(rowNode).toggleClass('selected', isChecked);
      $('#edit-checkbox', rowNode).prop('checked', isChecked);

      var visitOrderId = data.visit_order_id;

      if (isChecked && !selectedRows.includes(visitOrderId)) {
        selectedRows.push(visitOrderId);
      } else if (!isChecked && selectedRows.includes(visitOrderId)) {
        selectedRows = selectedRows.filter(id => id !== visitOrderId);
      }
    });
    dt_edit_visit_order.draw(false); // Redraw without changing page
  });

  // fixedColumns edit visit order
  // --------------------------------------------------------------------
  if (dt_edit_visit_order_table.length) {
    var dt_edit_visit_order = dt_edit_visit_order_table.DataTable({
      ajax: {
        url: baseUrl + 'billings/partner-payment/visit-order/get-visit-order',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'billings/partner-payment/visit-order/get-visit-order');
        },
        complete: function () {
          $.unblockUI();
        },
        },
      columns: [
        { data: 'visit_order_id' },
        { data: 'visit_order_number'},
        { data: 'visit_order_date' },
        { data: 'visit_order_due_date' },
        { data: 'client_name' },
        { data: 'site_name' },
        {
          data: 'visit_order_location',
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
        {
          data: 'partner_name',
          render: function(data) {
            if (data) {
              return data;
            } else {
              return '-';
            }
          }
        },
        {
          data: 'download_status',
          searchable: false,
          orderable: false,
          render: function(data) {
            if (data == "Downloaded") {
              return '<span class="badge bg-label-primary me-1">' + data + '</span>';
            } else if (data == "Not Downloaded") {
              return '<span class="badge bg-label-secondary me-1">' + data + '</span>';
            } else {
              return '<span class="badge bg-label-danger me-1">-</span>';
            }
          }
        },
        { data: 'visit_order_status' },
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          render: function (data, type, row) {
              var isExist = false;
              for (let i = 0; i < row.partner_detail.length; i++) {
                  if (row.partner_detail[i].partner_payment_id == partner_payment_id_tab) {
                      isExist = true;
                  }
              }
              return '<input type="checkbox" class="dt-checkboxes form-check-input" id="edit-checkbox" name="visit_order[' + data + ']" '+ (isExist ? 'checked' : '') + '>';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input" id="select-all-checkbox">'
          }
        },
        {
          // Label
          targets: -1,
          searchable: false,
          render: function (data, type, row, meta) {
            if (data == 6) {
                return '<span class="badge bg-label-primary me-1">Validated</span>';
            } else {
              return '<span class="badge bg-label-danger me-1">Unknown</span>';
            }
          }
        },
      ],
      dom: '<"d-flex justify-content-between align-items-center row"<"col-sm-12 col-md-2 d-flex"f><"col-sm-12 col-md-10 d-none"i>>t',
      select: {
        // Select style
        style: 'multi'
      },
      scrollY: '300px',
      scrollX: true,
      scrollCollapse: true,
      paging: false,
      info: false,
      // Fixed column option
      fixedColumns: true,
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
    var row = dt_edit_visit_order.row($this.closest('tr')).data();
    row.expanded = true; // Menandai bahwa deskripsi telah di-expand
    dt_edit_visit_order.row($this.closest('tr')).data(row);
  });
  
  $(document).on('click', '.show-less', function(e) {
    e.preventDefault();
    var $this = $(this);
    var $fullDesc = $this.prev('.full-desc');
    var $shortDesc = $fullDesc.prev('.short-desc');
    $fullDesc.hide();
    $shortDesc.show();
    $this.text('Show More');
    var row = dt_edit_visit_order.row($this.closest('tr')).data();
    row.expanded = false; // Menandai bahwa deskripsi telah di-collapse
    dt_edit_visit_order.row($this.closest('tr')).data(row);
  });

  // Detail
  $(document).on('click', '#dropdownMenuDetail', function () {
    var partner_payment_id = $(this).data('id');

    // Fungsi untuk Detail partner payment
    // get data
    $.ajax({
      url: partner_payment_url + "show/" + partner_payment_id,
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {

        $('#detNumber').text(response.data.partner_payment_number);
        $('#detName').text(response.data.partner_payment_name);

        // Fungsi untuk mengubah angka bulan menjadi nama bulan
        function getNamaBulan(bulan) {
          const namaBulan = [
              "Januari", "Februari", "Maret", "April", "Mei", "Juni",
              "Juli", "Agustus", "September", "Oktober", "November", "Desember"
          ];
          return namaBulan[bulan - 1] || ""; // Mengembalikan nama bulan atau string kosong jika angka bulan tidak valid
        }

        // Mengambil angka bulan dari response.data.partner_payment_month
        if (response.data.partner_payment_month) {
          const angkaBulan = parseInt(response.data.partner_payment_month);
          const namaBulan = getNamaBulan(angkaBulan);
          $('#detMonth').text(namaBulan);
        }
 
        if (response.data.partner_payment_year) {
          $('#detYear').text(response.data.partner_payment_year);
        }
        $('#detDesc').text(response.data.partner_payment_desc);

        getPaymentFileData(response.data.partner_payment_file);
        getHistoryData(response.data.history);

        modal_class_loader.unblock();
      }
    });

    $('#detFormLabel > p').html('Detail partner payment data.');
    $('#formDetPartnerPayment').attr('data-method', 'detail');
    $('#formDetPartnerPayment').data('method', 'detail');
    $('#formDetPartnerPayment').attr('data-id', partner_payment_id);
    modal_detail_partner_payment.modal('show');
  });

  // Delete button handler
  $(document).on('click', '#dropdownMenuDelete', function () {
    var partner_payment_id = $(this).data('id');

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
          data: {partner_payment_id: partner_payment_id},
          type: 'POST',
          url: partner_payment_url + 'delete',
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

  // Submitted status partner payment
  $(document).on('click', '#dropdownMenuSubmit', function () {
    var partner_payment_id = $(this).data('id');
    var url_submitted = partner_payment_url + "submitted";

    Swal.fire({
      title: 'Are you sure?',
      text: "You will send this status!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, Submitted it!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          data: {partner_payment_id: partner_payment_id, partner_payment_status: 2},
          type: 'POST',
          url: url_submitted,
          success: function success(response) {
            dt_ajax.draw();
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
          error: function error(_error) {
            Swal.fire({
              title: 'Error!',
              text: "Internal Server Error",
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


  var paidPartnerForm = document.getElementById('formPaidPartnerPayment');
  // paid partner payment form validation
  var fvPaid = FormValidation.formValidation(paidPartnerForm, {
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
      var form_data = new FormData(paidPartnerForm);
      var url = "billings/partner-payment/paid/" + $('#formPaidPartnerPayment').attr('data-id');

      $.ajax({
          data: form_data,
          url: baseUrl + url,
          type: 'POST',
          processData: false,
          contentType: false,
          success: function success(response) {
              if (response.status) {
                  modal_paid_partner_payment.modal('hide');
                  dt_ajax_table.draw();
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
              Swal.fire({
                  title: 'Error!',
                  text: "Internal Server Error",
                  icon: 'error',
                  customClass: {
                      confirmButton: 'btn btn-primary'
                  }
              });
          }
      });
  });

  // Edit button handler
  var selectedVisitOrders = []; // Declare the array for selected visit orders
  var selectedRows = []; // Array to track selected rows

  var partner_payment_id; // variabel global untuk menampung nilai partner_payment_id
  $(document).on('click', '#dropdownMenuPaid', function () {
    partner_payment_id = $(this).data('id');
    partner_payment_id_tab = partner_payment_id; // ditangkap di Tab Attachment

    // Clear the selectedVisitOrders and selectedRows arrays
    selectedVisitOrders = [];
    selectedRows = [];

    // get data
    $.ajax({
      url: partner_payment_url + "show/" + partner_payment_id,
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {
        selectedVisitOrders = response.data.partner_detail.map(order => order.visit_order_id);

        $('#PaidNumber').val(response.data.partner_payment_number);
        $('#PaidName').val(response.data.partner_payment_name);
        if (response.data.partner_payment_month) {
          $('#PaidMonth').val(response.data.partner_payment_month);
        }

        if (response.data.partner_payment_year) {
          $('#PaidYear').val(response.data.partner_payment_year);
        }
        $('#PaidDesc').val(response.data.partner_payment_desc);
        
        getPaymentFilePaidData(response.data.partner_payment_file);

        // Highlight selected visit orders in DataTable
        dt_paid_visit_order.rows().every(function (rowIdx, tableLoop, rowLoop) {
          var rowNode = this.node();
          var data = this.data();

          // Check if the current visit_order's visit_order_id matches the selectedVisitOrders
          var isRowSelected = selectedRows.includes(data.visit_order_id);

          if (selectedVisitOrders.includes(data.visit_order_id)) {
            $(rowNode).addClass('selected');
            $('#edit-checkbox', rowNode).prop('checked', true);
            if (!isRowSelected) {
              selectedRows.push(data.visit_order_id); // Add to selected rows
            }
          } else {
            $(rowNode).removeClass('selected');
            $('#edit-checkbox', rowNode).prop('checked', false);
            if (isRowSelected) {
              selectedRows = selectedRows.filter(id => id !== data.visit_order_id); // Remove from selected rows
            }
          }
        });

        // Redraw the DataTable to update the visual appearance of checkboxes and highlighted rows
        dt_paid_visit_order.draw();

        modal_class_loader.unblock();
      }
    });

    $('#paidFormLabel > p').html('Paid partner payment data.');
    $('#formPaidPartnerPayment').attr('data-method', 'edit');
    $('#formPaidPartnerPayment').data('method', 'edit');
    $('#formPaidPartnerPayment').attr('data-id', partner_payment_id);

    modal_paid_partner_payment.modal('show');
  });

  // fixedColumns paid visit order
  // --------------------------------------------------------------------
  if (dt_paid_visit_order_table.length) {
    var dt_paid_visit_order = dt_paid_visit_order_table.DataTable({
      ajax: {
        url: baseUrl + 'billings/partner-payment/visit-order/get-visit-order',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'billings/partner-payment/visit-order/get-visit-order');
        },
        complete: function () {
          $.unblockUI();
        },
        },
      columns: [
        { data: 'visit_order_id' },
        { data: 'visit_order_number'},
        { data: 'visit_order_date' },
        { data: 'visit_order_due_date' },
        { data: 'client_name' },
        { data: 'site_name' },
        {
          data: 'visit_order_location',
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
        {
          data: 'partner_name',
          render: function(data) {
            if (data) {
              return data;
            } else {
              return '-';
            }
          }
        },
        {
          data: 'download_status',
          searchable: false,
          orderable: false,
          render: function(data) {
            if (data == "Downloaded") {
              return '<span class="badge bg-label-primary me-1">' + data + '</span>';
            } else if (data == "Not Downloaded") {
              return '<span class="badge bg-label-secondary me-1">' + data + '</span>';
            } else {
              return '<span class="badge bg-label-danger me-1">-</span>';
            }
          }
        },
        { data: 'visit_order_status' },
      ],
      columnDefs: [
        {
          // For Checkboxes
          targets: 0,
          searchable: false,
          orderable: false,
          render: function (data, type, row) {
              var isExist = false;
              for (let i = 0; i < row.partner_detail.length; i++) {
                  if (row.partner_detail[i].partner_payment_id == partner_payment_id_tab) {
                      isExist = true;
                  }
              }
              return '<input type="checkbox" class="dt-checkboxes form-check-input" id="edit-checkbox" name="visit_order[' + data + ']" '+ (isExist ? 'checked' : '') + '>';
          },
          checkboxes: {
            selectRow: true,
            selectAllRender: '<input type="checkbox" class="form-check-input" id="select-all-checkbox">'
          }
        },
        {
          // Label
          targets: -1,
          searchable: false,
          render: function (data, type, row, meta) {
            if (data == 6) {
                return '<span class="badge bg-label-primary me-1">Validated</span>';
            } else {
              return '<span class="badge bg-label-danger me-1">Unknown</span>';
            }
          }
        },
      ],
      dom: '<"d-flex justify-content-between align-items-center row"<"col-sm-12 col-md-2 d-flex"f><"col-sm-12 col-md-10 d-none"i>>t',
      select: {
        // Select style
        style: 'multi'
      },
      scrollY: '300px',
      scrollX: true,
      scrollCollapse: true,
      paging: false,
      info: false,
      // Fixed column option
      fixedColumns: true,
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
    var row = dt_paid_visit_order.row($this.closest('tr')).data();
    row.expanded = true; // Menandai bahwa deskripsi telah di-expand
    dt_paid_visit_order.row($this.closest('tr')).data(row);
  });
  
  $(document).on('click', '.show-less', function(e) {
    e.preventDefault();
    var $this = $(this);
    var $fullDesc = $this.prev('.full-desc');
    var $shortDesc = $fullDesc.prev('.short-desc');
    $fullDesc.hide();
    $shortDesc.show();
    $this.text('Show More');
    var row = dt_paid_visit_order.row($this.closest('tr')).data();
    row.expanded = false; // Menandai bahwa deskripsi telah di-collapse
    dt_paid_visit_order.row($this.closest('tr')).data(row);
  });

    /**
   * 
   * Tab Attachment
   * 
   */

  var dt_ajax_table_attachment = $('.datatables-ajax-attachment');
  var partner_payment_id_tab = partner_payment_id;

  // Data table attachment
  if (dt_ajax_table_attachment.length) {
    var dt_ajax_attachment = dt_ajax_table_attachment.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: baseUrl + 'billings/partner-payment/attachment/get',
        beforeSend: function(data) {
          window.Helpers.blockUIModalLoader(modal_class_loader);
        },
        data: function(d) {
          d.partner_payment_id = partner_payment_id_tab;
        }
      },
      columns: [
        { data: 'no', orderable: false }, 
        {
          data: 'partner_payment_file',
          render: function(data, type, row, meta) {
            var attachment = row.partner_payment_file.split(',');
            var files = '';
            for (var i = 0; i < attachment.length; i++) {
              var fileExt = attachment[i].split('.').pop().toLowerCase();
              var url = baseUrl + 'billings/partner-payment/attachment/uploads/' + row.partner_payment_file_id;
              var pdf  = baseUrl + 'assets/img/icons/misc/custom-pdf.png';
              var word = baseUrl + 'assets/img/icons/misc/custom-ms-word.png';
              var excel= baseUrl + 'assets/img/icons/misc/custom-ms-excel.png';
              var ppt  = baseUrl + 'assets/img/icons/misc/custom-ms-ppt.png';
              
              url = url.replace(':filename', attachment[i]);
              if (fileExt === 'jpg' || fileExt === 'jpeg' || fileExt === 'png') {
                files += '<a href="' + url + '" target="_blank"><img src="' + url + '" width="50px" height="50px" title="'+ row.partner_payment_file +'"/></a>';
              } else if (fileExt === 'pdf') {
                files += '<a href="' + url + '" download="' + attachment[i] + '" target="_blank"><img src="' + pdf + '" width="50px" height="50px" title="'+ row.partner_payment_file +'"/></a>';
              } else if (fileExt === 'doc' || fileExt === 'docx') {
                files += '<a href="' + url + '" download="' + attachment[i] + '" target="_blank"><img src="' + word + '" width="50px" height="50px" title="'+ row.partner_payment_file +'" /></a>';
              } else if (fileExt === 'xls' || fileExt === 'xlsx') {
                files += '<a href="' + url + '" download="' + attachment[i] + '" target="_blank"><img src="' + excel + '" width="50px" height="50px" title="'+ row.partner_payment_file +'" /></a>';
              } else if (fileExt === 'ppt' || fileExt === 'pptx') {
                files += '<a href="' + url + '" download="' + attachment[i] + '" target="_blank"><img src="' + ppt + '" width="50px" height="50px" title="'+ row.partner_payment_file +'" /></a>';
              } else {
                files += '<span class="badge bg-danger">Unknown</span>';
              }
            }
            return files;
          },
          orderable: false
        },
        {
          data: 'partner_payment_file_desc',
          orderable: false,
          render: function(data) {
            if (data) {
              return data;
            } else {
              return '-';
            }
          }
        },
        { data: 'partner_payment_file_actions', orderable: false }      
      ],
      columnDefs: [
        {
          targets: 3,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
              return '' +
              '<div class="d-inline-block text-nowrap">' +
                  '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                  '<div class="dropdown-menu">' +
                      '<a id="dropdownMenuDeleteAttachment" data-id="' + row.partner_payment_file_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
                  '</div>' +
              '</div>';
          }
        }
      ],
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      drawCallback: function(settings) {
        modal_class_loader.unblock(); // Menghentikan blokir loader setelah DataTable di-"draw"
      }
    });
  }

  // attachment config
  const previewEditTemplateAttachment = `
    <div class="dz-preview dz-file-preview">
      <div class="dz-details">
        <div class="dz-thumbnail">
          <img data-dz-thumbnail>
          <span class="dz-nopreview">No preview</span>
          <div class="dz-success-mark"></div>
          <div class="dz-error-mark"></div>
          <div class="dz-error-message"><span data-dz-errormessage></span></div>
          <div class="progress">
            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
          </div>
        </div>
        <div class="dz-filename" data-dz-name></div>
        <div class="dz-size" data-dz-size></div>
      </div>
    </div>`;


    const attachmentEditDropzoneMulti = new Dropzone('#add-file-attachment', {
      url: '#',
      paramName: 'partner_payment_files',
      previewTemplate: previewEditTemplateAttachment,
      autoProcessQueue: false,
      acceptedFiles: 'image/jpg, image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-powerpoint, application/vnd.openxmlformats-officedocument.presentationml.presentation',
      parallelUploads: 20,
      maxFilesize: 1, // max size 1 MB (1024 KB)
      addRemoveLinks: true,
    });
    
    
  // End attachment config

  $(document).on('click', '#file_attachment', function () {
  // Reinit datatable with new param attachment
    partner_payment_id_tab = partner_payment_id;
    dt_ajax_attachment.draw();
  });

  // Add Attachment Form
  var addNewAttachmentForm = document.getElementById('form_add_attchment');
  // Product Attachment Form Validation
  var fv_attachment = FormValidation.formValidation(addNewAttachmentForm, {
  fields: {
    partner_payment_file: {
    },
    partner_payment_file_desc: {
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
    var url_attachment = "billings/partner-payment/attachment/store";
    var rejected_files_attachment = attachmentEditDropzoneMulti.getRejectedFiles();

    if (Array.isArray(rejected_files_attachment) && rejected_files_attachment.length) {
      Swal.fire({
        title: 'Check your file attachment!',
        text: 'Remove any wrong file attachment.',
        icon: 'error',
        customClass: {
          confirmButton: 'btn btn-primary'
        }
      });
    } else {
      var form_data = new FormData(addNewAttachmentForm);
      form_data.append('partner_payment_id', partner_payment_id_tab);

      var product_attachment = attachmentEditDropzoneMulti.getAcceptedFiles();
      product_attachment.forEach((val, index) => {
        form_data.append('partner_payment_files[]', val); // append attachment file to form data
      });

      $.ajax({
        data: form_data,
        url: baseUrl + url_attachment,
        type: 'POST',
        processData: false,
        contentType: false,
        success: function success(response) {
          // console.log(response);
          dt_ajax_attachment.draw();

          if (response.status) {
            $('#partner_payment_file_desc').val(null);
            attachmentEditDropzoneMulti.removeAllFiles(true);
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
  // End Add File Attachment Form

  function getPaymentFileData(response) {
    var payment_files = response; // Array of payment file objects
  
    var fileContainer = document.getElementById('payment-file');
    fileContainer.innerHTML = '';
  
    if (payment_files.length === 0) {
      var textElement = document.createElement('h5');
      textElement.textContent = "Silahkan upload payment file terlebih dahulu pada button add / tab attachment di button edit";
      textElement.style.marginTop = '10px';
      fileContainer.appendChild(textElement);
    } else {
      payment_files.forEach(function(file) {
        var cardDiv = document.createElement('div');
        cardDiv.classList.add('col-xl-4', 'col-lg-6', 'col-md-6'); // Adjust the column size as needed
  
        var card = document.createElement('div');
        card.className = 'card';
  
        var cardBody = document.createElement('div');
        cardBody.className = 'card-body text-center';
  
        var innerDiv = document.createElement('div');
        innerDiv.className = 'mx-auto mb-3';
  
        var fileLink = document.createElement('a');
        var url = baseUrl + 'billings/partner-payment/attachment/uploads/' + file.payment_file_id;
        var pdf  = baseUrl + 'assets/img/icons/misc/custom-pdf.png';
        var word = baseUrl + 'assets/img/icons/misc/custom-ms-word.png';
        var excel= baseUrl + 'assets/img/icons/misc/custom-ms-excel.png';
        var ppt  = baseUrl + 'assets/img/icons/misc/custom-ms-ppt.png';

        fileLink.href = url;
        fileLink.target = '_blank'; // Menambahkan atribut target untuk membuka tautan dalam tab baru
  
        var files = ''; // Ini adalah variabel string untuk menyimpan semua tautan gambar atau file
  
        // Check if the file is an image
        if (file.partner_payment_file.match(/\.(jpeg|jpg|png|gif)$/i)) {
          // Display image
          files += '<a href="' + url + '" target="_blank"><img src="' + url + '" width="100px" height="100px" title="' + file.partner_payment_file + '"/></a>';
        } else {
          var fileExt = file.partner_payment_file.split('.').pop().toLowerCase();
          var iconClass = 'fa-file'; // Default file icon
          if (fileExt === 'pdf') {
            iconClass = pdf;
          } else if (['xlsx', 'xls'].includes(fileExt)) {
            iconClass = excel;
          } else if (['pptx', 'ppt'].includes(fileExt)) {
            iconClass = ppt;
          } else if (['docx', 'doc'].includes(fileExt)) {
            iconClass = word;
          }
  
          files += '<a href="' + url + '" target="_blank"><img src="' + iconClass + '" width="100px" height="100px" title="'+ file.partner_payment_file +'"/></a>';
        }
  
        innerDiv.innerHTML = files;
        cardBody.appendChild(innerDiv);
  
        var cardTitle = document.createElement('h5');
        cardTitle.className = 'mb-1 card-title';
        cardTitle.textContent = file.partner_payment_file_desc;
  
        cardBody.appendChild(cardTitle);
        card.appendChild(cardBody);
        cardDiv.appendChild(card);
  
        fileContainer.appendChild(cardDiv);
      });
    }
  }  
  
  function getPaymentFilePaidData(response) {
    var payment_files = response; // Array of payment file objects
  
    var fileContainer = document.getElementById('payment-file-paid');
    fileContainer.innerHTML = '';
  
    if (payment_files.length === 0) {
      var textElement = document.createElement('h5');
      textElement.textContent = "Silahkan upload payment file terlebih dahulu pada button add / tab attachment di button edit";
      textElement.style.marginTop = '10px';
      fileContainer.appendChild(textElement);
    } else {
      payment_files.forEach(function(file) {
        var cardDiv = document.createElement('div');
        cardDiv.classList.add('col-xl-4', 'col-lg-6', 'col-md-6'); // Adjust the column size as needed
  
        var card = document.createElement('div');
        card.className = 'card';
  
        var cardBody = document.createElement('div');
        cardBody.className = 'card-body text-center';
  
        var innerDiv = document.createElement('div');
        innerDiv.className = 'mx-auto mb-3';
  
        var fileLink = document.createElement('a');
        var url = baseUrl + 'billings/partner-payment/attachment/uploads/' + file.payment_file_id;
        var pdf  = baseUrl + 'assets/img/icons/misc/custom-pdf.png';
        var word = baseUrl + 'assets/img/icons/misc/custom-ms-word.png';
        var excel= baseUrl + 'assets/img/icons/misc/custom-ms-excel.png';
        var ppt  = baseUrl + 'assets/img/icons/misc/custom-ms-ppt.png';

        fileLink.href = url;
        fileLink.target = '_blank'; // Menambahkan atribut target untuk membuka tautan dalam tab baru
  
        var files = ''; // Ini adalah variabel string untuk menyimpan semua tautan gambar atau file
  
        // Check if the file is an image
        if (file.partner_payment_file.match(/\.(jpeg|jpg|png|gif)$/i)) {
          // Display image
          files += '<a href="' + url + '" target="_blank"><img src="' + url + '" width="100px" height="100px" title="' + file.partner_payment_file + '"/></a>';
        } else {
          var fileExt = file.partner_payment_file.split('.').pop().toLowerCase();
          var iconClass = 'fa-file'; // Default file icon
          if (fileExt === 'pdf') {
            iconClass = pdf;
          } else if (['xlsx', 'xls'].includes(fileExt)) {
            iconClass = excel;
          } else if (['pptx', 'ppt'].includes(fileExt)) {
            iconClass = ppt;
          } else if (['docx', 'doc'].includes(fileExt)) {
            iconClass = word;
          }
  
          files += '<a href="' + url + '" target="_blank"><img src="' + iconClass + '" width="100px" height="100px" title="'+ file.partner_payment_file +'"/></a>';
        }
  
        innerDiv.innerHTML = files;
        cardBody.appendChild(innerDiv);
  
        var cardTitle = document.createElement('h5');
        cardTitle.className = 'mb-1 card-title';
        cardTitle.textContent = file.partner_payment_file_desc;
  
        cardBody.appendChild(cardTitle);
        card.appendChild(cardBody);
        cardDiv.appendChild(card);
  
        fileContainer.appendChild(cardDiv);
      });
    }
  }  

  // Mengambil data untuk history
  function getHistoryData(response) {
    var timeline_data = response;

    // Bersihkan konten yang ada sebelumnya
    var historyContainer = document.getElementById('history_partner_payment');
    historyContainer.innerHTML = '';

    timeline_data.forEach(function(timeline){
      var stats = getStatusDot(timeline.partner_payment_history_status);

      // Mengecek apakah timeline.partner_payment_history_desc memiliki nilai, jika tidak, ganti dengan "-"
      var statsDesc = timeline.partner_payment_history_desc ? timeline.partner_payment_history_desc : "-";
      var dot = `
        <li class="timeline-item timeline-item-transparent">
        <span class="timeline-point ${stats[0]}"></span>
        <div class="timeline-event">
          <div class="timeline-header mb-sm-0 mb-3">
            <h6 class="mb-0">${stats[1]}</h6>
            <small class="text-muted">${timeline.date_created_format} WIB</small>
          </div>
          <p>
            ${statsDesc}
          </p>
        </div>
      </li>
      `;
      historyContainer.innerHTML += dot;
    });
    var last_dot = `
      <li class="timeline-end-indicator">
        <i class="bx bx-check-circle"></i>
      </li>
    `;
    historyContainer.innerHTML += last_dot;
  }

  // Function to get the corresponding status dot based on the status number
  function getStatusDot(status) {
    switch (status) {
      case 1:
        return ['timeline-point-primary', 'Open'];
      case 2:
        return ['timeline-point-success', 'Submitted'];
      case 3:
          return ['timeline-point-info', 'Paid'];
      default:
        return ['timeline-point-danger', 'Unknown'];
    }
  }

});
