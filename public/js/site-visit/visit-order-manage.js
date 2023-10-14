/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table = $('.datatables-ajax');
  var modal_change_status = $('#modalChangeStatus');
  var modal_detail_visit_order = $('#modalDetailVisitOrder');
  var modal_edit_visit_order = $('#modalEditVisitOrder');
  var modal_checklist_visit_order = $('#modalChecklistVisitOrder');
  var modal_visual_visit_order = $('#modalVisualVisitOrder');
  var modal_set_visited_visit_order = $('#modalSetVisitedVisitOrder');
  var modal_class_loader = $('.modal-block-loader');

  var ac_client = $('.ac_client');
  var ac_site = $('.ac_site');
  var ac_visit_type = $('.ac_visit_type');
  var ac_partner = $('.ac_partner');
  var ac_partner_2 = $('.ac_partner_re_visit');
  var ac_partner_3 = $('.ac_partner_set_visited');

  var visit_order_id_global;
  var typingTimer;

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  });

  // datepicker
  $(".dt").flatpickr({
    monthSelectorType: 'static'
  });

  // textarea
  const textarea = document.querySelector('#visit_order_note-autosize');
  const location = document.querySelector('#autosize-location');

  // Autosize
  // --------------------------------------------------------------------
  if (textarea) {
    autosize(textarea);
  }

  if (location) {
    autosize(location);
  }

  // Function to clear Site Contact field
  function clearSiteContactField() {
    $('#site_contact_id').val(null);
  }

  function clearSiteField() {
    var $siteField = ac_site;
    $siteField.val('').trigger('change');
  }

  $("#site_contact_id").prop("disabled", true);
  $("#save_to_site_contact").prop("disabled", true);

  // Select2 client handler
  if (ac_client.length) {
    var $clientNameField = ac_client;
    $clientNameField.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select client',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/client/find',
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
      dropdownParent: $clientNameField.parent()
    }).on('change', function () {
      var $thisSite = ac_site;
      var clientId = $('#client_id').val();
      $thisSite.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select site',
        disabled: false,
        minimumInputLength: 0,
        ajax: {
          // url: baseUrl + 'autocomplete/site/find',
          url: baseUrl + 'autocomplete/site/find-by-id/' + clientId,
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
        dropdownParent: $thisSite.parent()
      });
      clearSiteField();
    });
  }

  // Select2 Site handler
  var selectedSiteId; // Declare a global variable to store the selected site_id

  if (ac_site.length) {
    clearSiteContactField();
    var $thisSite = ac_site;
    var clientId = $('#client_id').val();
    $thisSite.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select site',
      minimumInputLength: 0,
      ajax: {
        // url: baseUrl + 'autocomplete/site/find',
        url: baseUrl + 'autocomplete/site/find-by-id/' + clientId,
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
      dropdownParent: $thisSite.parent()
    }).on('change', function () {
      // Clear the site_contact_id field
      clearSiteContactField();

      // Enable the input field and the button
      $("#site_contact_id").prop("disabled", false);
      $("#save_to_site_contact").prop("disabled", false);

      selectedSiteId = $('#site_id').val();

      getAllSiteContactData(selectedSiteId);
    });
  }

  // Select2 visit type name handler
  if (ac_visit_type.length) {
    var $this = ac_visit_type;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select visit type name',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/visit-type/find',
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

  // Select2 partner name handler
  if (ac_partner.length) {
    var $this = ac_partner;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select partner name',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/partner/find',
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

  if (ac_partner_2.length) {
    var $this = ac_partner_2;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select partner name',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/partner/find',
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

  if (ac_partner_3.length) {
    var $this = ac_partner_3;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select partner name',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/partner/find',
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
      stateSave: true,
      initComplete: onInit,
      ajax: {
        url: baseUrl + 'site-visit/visit-order-manage/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'site-visit/visit-order-manage/get');
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
        { data: 'visit_order_number', orderable: false },
        { data: 'visit_order_date' },
        { data: 'visit_order_due_date' },
        {
          data: 'debtor_name',
          render: function (data, type, row) {
            if (data) {
              var expandedDebtorName = row.expandedDebtorName ? row.expandedDebtorName : false;

              if (!expandedDebtorName) {
                var shortDescDebtorName = data.length > 13 ? data.substr(0, 13) + '...' : data;
                var showMoreHtml = data.length > 13 ? '<a href="javascript:void(0);" class="show-more-debtor-name">Show More</a>' : '';
                return '<div style="white-space: pre-wrap;" class="short-desc-debtor-name">' + shortDescDebtorName + '</div>' + showMoreHtml;
              } else {
                return '<div style="white-space: pre-wrap;" class="full-desc-debtor-name">' + data + '</div><a href="javascript:void(0);" class="show-less-debtor-name">Show Less</a>';
              }
            } else {
              return '-';
            }
          }
        },
        {
          data: 'client_name',
          render: function (data, type, row) {
            if (data) {
              var expandedClientName = row.expandedClientName ? row.expandedClientName : false;

              if (!expandedClientName) {
                var shortDescClientName = data.length > 13 ? data.substr(0, 13) + '...' : data;
                var showMoreHtml = data.length > 13 ? '<a href="javascript:void(0);" class="show-more-client-name">Show More</a>' : '';
                return '<div style="white-space: pre-wrap;" class="short-desc-client-name">' + shortDescClientName + '</div>' + showMoreHtml;
              } else {
                return '<div style="white-space: pre-wrap;" class="full-desc-client-name">' + data + '</div><a href="javascript:void(0);" class="show-less-client-name">Show Less</a>';
              }
            } else {
              return '-';
            }
          }
        },
        {
          data: 'site_name',
          render: function (data, type, row) {
            if (data) {
              var expandedSiteName = row.expandedSiteName ? row.expandedSiteName : false;

              if (!expandedSiteName) {
                var shortDescSiteName = data.length > 15 ? data.substr(0, 15) + '...' : data;
                var showMoreHtml = data.length > 15 ? '<a href="javascript:void(0);" class="show-more-site-name">Show More</a>' : '';
                return '<div style="white-space: pre-wrap;" class="short-desc-site-name">' + shortDescSiteName + '</div>' + showMoreHtml;
              } else {
                return '<div style="white-space: pre-wrap;" class="full-desc-site-name">' + data + '</div><a href="javascript:void(0);" class="show-less-site-name">Show Less</a>';
              }
            } else {
              return '-';
            }
          }
        },
        {
          data: 'visit_order_location',
          render: function (data, type, row) {
            if (data) {
              var expandedLoc = row.expandedLoc ? row.expandedLoc : false;

              if (!expandedLoc) {
                var shortDescLoc = data.length > 25 ? data.substr(0, 25) + '...' : data;
                var showMoreHtml = data.length > 25 ? '<a href="javascript:void(0);" class="show-more-loc">Show More</a>' : '';
                return '<div style="white-space: pre-wrap;" class="short-desc-loc">' + shortDescLoc + '</div>' + showMoreHtml;
              } else {
                return '<div style="white-space: pre-wrap;" class="full-desc-loc">' + data + '</div><a href="javascript:void(0);" class="show-less-loc">Show Less</a>';
              }
            } else {
              return '-';
            }
          }
        },
        {
          data: 'partner_name',
          render: function (data, type, row) {
            if (data) {
              var expandedPartnerName = row.expandedPartnerName ? row.expandedPartnerName : false;

              if (!expandedPartnerName) {
                var shortDescPartnerName = data.length > 15 ? data.substr(0, 15) + '...' : data;
                var showMoreHtml = data.length > 15 ? '<a href="javascript:void(0);" class="show-more-partner-name">Show More</a>' : '';
                return '<div style="white-space: pre-wrap;" class="short-desc-partner-name">' + shortDescPartnerName + '</div>' + showMoreHtml;
              } else {
                return '<div style="white-space: pre-wrap;" class="full-desc-partner-name">' + data + '</div><a href="javascript:void(0);" class="show-less-partner-name">Show Less</a>';
              }
            } else {
              return '-';
            }
          }
        },
        {
          data: 'visit_order_custom_number',
        },
        {
          data: 'download_status',
          searchable: false,
          orderable: false,
          render: function (data) {
            if (data == "Downloaded") {
              return '<span class="badge bg-label-primary me-1">' + data + '</span>';
            } else if (data == "Not Downloaded") {
              return '<span class="badge bg-label-secondary me-1">' + data + '</span>';
            } else {
              return '<span class="badge bg-label-danger me-1">-</span>';
            }
          }
        },
        {
          data: 'visit_order_status',
          render: function (data) {
            return getStatusLabel(data);
          }
        },
      ],
      columnDefs: [
        {
          targets: 11,
          searchable: false,
          render: function (data, type, row, meta) {
            return getStatusLabel(data);
          }
        },
        {
          targets: 12,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {

            try {
              // Original content of the render function
              var text = '';
              text += '' +
                // '<button id="buttonMenuDetail" class="btn btn-sm btn-icon btn-primary mx-1" data-id="' + row.visit_order_id + '" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="Detail ' + row.visit_order_number + '"><i class="bx bx-detail me-1"></i></button>';
                '<div class="d-inline-block text-nowrap">' +
                '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                '<div class="dropdown-menu">' +

                '<a id="dropdownMenuDetail" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-detail me-1"></i> Detail</a>';

              //CHECK STATUS VISIT ORDER
              if (row.visit_order_status === 5 || row.visit_order_status === 6) {
                text += '<div class="dropdown-divider"></div>' +
                  '<a id="dropdownMenuChecklist" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bxs-check-square"></i> Checklist</a>' +
                  '<div class="dropdown-divider"></div>' +
                  '<a id="dropdownMenuVisual" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-images me-1"></i> Visual</a>';
              }
              if (row.visit_order_status >= 5) {
                text += '<div class="dropdown-divider"></div>' +
                  '<a id="dropdownMenuPdf" target="_blank" href="/report/report-visit-order/pdf/' + row.visit_order_encrypt_id + '" class="dropdown-item"><i class="bx bxs-file-pdf"></i> PDF</a>' +
                  '<div class="dropdown-divider"></div>' +
                  '<a id="dropdownMenuSetDownload" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bxs-download"></i> Set Download</a>';
              }
              if (row.visit_order_status === 1 || row.visit_order_status === 2) {
                text += '<div class="dropdown-divider"></div>' +
                  '<a id="dropdownMenuSetVisited" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-buildings"></i> Set Visited</a>';
              }
              if (row.visit_order_status < 6) {
                text += '<div class="dropdown-divider"></div>' +
                  '<a id="dropdownMenuEdit" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>';
              }
              if (row.visit_order_status === 1 || row.visit_order_status === 4) {
                text += '<div class="dropdown-divider"></div>' +
                  '<a id="dropdownMenuAssign" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-paper-plane me-1"></i> Assign</a>';
              }
              if (row.visit_order_status !== 6) {
                text += '<div class="dropdown-divider"></div>' +
                  '<a id="dropdownMenuCancel" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-task-x me-1"></i> Cancel</a>';
              }
              if (row.visit_order_status === 3) {
                text += '<div class="dropdown-divider"></div>' +
                  '<a id="dropdownMenuDelete" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-task me-1"></i> Delete</a>';
              }
              if (row.visit_order_status === 2) {
                text += '<div class="dropdown-divider"></div>' +
                  '<a id="dropdownMenuReVisit" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-analyse me-1"></i> Re Visit</a>';
              }
              if (row.visit_order_status === 5) {
                text += '<div class="dropdown-divider"></div>' +
                  '<a id="dropdownMenuValidate" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-task me-1"></i> Validate</a>';
              }
              text += '</div>' +
                '</div>';
              return text;
            } catch (error) {
              // Handle errors in the render function gracefully
              Swal.fire({
                title: 'Error!',
                text: "Internal Server Error",
                icon: 'error',
                customClass: {
                  confirmButton: 'btn btn-primary'
                }
              });
            }
          }
        }
      ],
      order: [[0, 'asc']],
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

  $(document).on('click', '.show-more-debtor-name', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $shortDescDebtorName = $this.prev('.short-desc-debtor-name');
    var $fullDescDebtorName = $shortDescDebtorName.next('.full-desc-debtor-name');
    $shortDescDebtorName.hide();
    $fullDescDebtorName.show();
    $this.text('Show Less');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedDebtorName = true; // Menandai bahwa deskripsi telah di-expand
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-less-debtor-name', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $fullDescDebtorName = $this.prev('.full-desc-debtor-name');
    var $shortDescDebtorName = $fullDescDebtorName.prev('.short-desc-debtor-name');
    $fullDescDebtorName.hide();
    $shortDescDebtorName.show();
    $this.text('Show More');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedDebtorName = false; // Menandai bahwa deskripsi telah di-collapse
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-more-client-name', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $shortDescClientName = $this.prev('.short-desc-client-name');
    var $fullDescClientName = $shortDescClientName.next('.full-desc-client-name');
    $shortDescClientName.hide();
    $fullDescClientName.show();
    $this.text('Show Less');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedClientName = true; // Menandai bahwa deskripsi telah di-expand
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-less-client-name', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $fullDescClientName = $this.prev('.full-desc-client-name');
    var $shortDescClientName = $fullDescClientName.prev('.short-desc-client-name');
    $fullDescClientName.hide();
    $shortDescClientName.show();
    $this.text('Show More');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedClientName = false; // Menandai bahwa deskripsi telah di-collapse
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-more-site-name', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $shortDescSiteName = $this.prev('.short-desc-site-name');
    var $fullDescSiteName = $shortDescSiteName.next('.full-desc-site-name');
    $shortDescSiteName.hide();
    $fullDescSiteName.show();
    $this.text('Show Less');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedSiteName = true; // Menandai bahwa deskripsi telah di-expand
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-less-site-name', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $fullDescSiteName = $this.prev('.full-desc-site-name');
    var $shortDescSiteName = $fullDescSiteName.prev('.short-desc-site-name');
    $fullDescSiteName.hide();
    $shortDescSiteName.show();
    $this.text('Show More');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedSiteName = false; // Menandai bahwa deskripsi telah di-collapse
    dt_ajax.row($this.closest('tr')).data(row);
  });


  $(document).on('click', '.show-more-loc', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $shortDescLoc = $this.prev('.short-desc-loc');
    var $fullDescLoc = $shortDescLoc.next('.full-desc-loc');
    $shortDescLoc.hide();
    $fullDescLoc.show();
    $this.text('Show Less');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedLoc = true; // Menandai bahwa deskripsi telah di-expand
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-less-loc', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $fullDescLoc = $this.prev('.full-desc-loc');
    var $shortDescLoc = $fullDescLoc.prev('.short-desc-loc');
    $fullDescLoc.hide();
    $shortDescLoc.show();
    $this.text('Show More');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedLoc = false; // Menandai bahwa deskripsi telah di-collapse
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-more-partner-name', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $shortDescPartnerName = $this.prev('.short-desc-partner-name');
    var $fullDescPartnerName = $shortDescPartnerName.next('.full-desc-partner-name');
    $shortDescPartnerName.hide();
    $fullDescPartnerName.show();
    $this.text('Show Less');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedPartnerName = true; // Menandai bahwa deskripsi telah di-expand
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-less-partner-name', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $fullDescPartnerName = $this.prev('.full-desc-partner-name');
    var $shortDescPartnerName = $fullDescPartnerName.prev('.short-desc-partner-name');
    $fullDescPartnerName.hide();
    $shortDescPartnerName.show();
    $this.text('Show More');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedPartnerName = false; // Menandai bahwa deskripsi telah di-collapse
    dt_ajax.row($this.closest('tr')).data(row);
  });

  var setVisitedVisitOrderForm = document.getElementById('formSetVisitedVisitOrder');
  // set visit visit order form validation
  var fvSetVisit = FormValidation.formValidation(setVisitedVisitOrderForm, {
    fields: {
      partner_id: {
        validators: {
          notEmpty: {
            message: 'Please select partner'
          }
        }
      },
      visit_order_date: {
        validators: {
          notEmpty: {
            message: 'Please select partner'
          }
        }
      },
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        eleValidClass: '',
        rowSelector: function (field, ele) {
          // field is the field name & ele is the field element
          switch (field) {
            case 'client_id':
            case 'site_id':
            case 'visit_type_id':
            case 'partner_id':
            case 'visit_order_number':
            case 'visit_order_date':
              return '.col-md-6';
            case 'site_contact_id':
            case 'debtor_id':
              return '.col-md-5';
            case 'visit_order_due_date':
            case 'visit_order_location':
            case 'visit_order_location_map':
              return '.col-md-12';
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
    var url = "site-visit/visit-order-manage/set-visited";
    var form_data = new FormData(setVisitedVisitOrderForm);

    // Ambil semua radio yang dicentang
    // var radioes = document.querySelectorAll('input[name="checklist"]:checked');
    var radioes = document.querySelectorAll('input[name^="checklist_group_sort-"]:checked');

    // Buat array utk menyimpan nilai-nilai yang akan disimpan atau diperbaharui
    var selectedChecklists = [];
    var isValid = true; // Menyimpan status validasi

    // loop melalui radio yang dicentang
    radioes.forEach(function (radio) {
      var checklistId = radio.value;
      var checklistText = '';

      // Jika radio memiliki input teks terkait, ambil nilainya
      if (radio.nextSibling && radio.nextSibling.tagName === 'INPUT') {
        var inputText = radio.nextSibling;
        checklistText = inputText.value;

        // Validasi required jika input text tidak kosong
        if (inputText.required && checklistText.trim() === '') {
          isValid = false;
          inputText.classList.add('is-invalid'); // Tambahkan kelas CSS untuk menandai input tidak valid
          inputText.setCustomValidity('Please enter for lain-lain ?'); // Pesan validasi kustom
        } else {
          inputText.classList.remove('is-invalid'); // Hapus kelas CSS jika input valid
          inputText.setCustomValidity(''); // Hapus pesan validasi kustom
        }

        // Tambahkan event listener invalid untuk menampilkan pesan validasi kustom
        inputText.addEventListener('invalid', function () {
          if (inputText.validity.valueMissing) {
            inputText.setCustomValidity('Please enter for lain-lain ?');
          } else {
            inputText.setCustomValidity('');
          }
        });
      }

      // Buat objek untuk setiap checklist yg dicentang
      var selectedChecklist = {
        checklistId: checklistId,
        checklistText: checklistText
      };

      // Tambahkan objek ke dalam array
      selectedChecklists.push(selectedChecklist);
    });

    if (!isValid) {
      // Jika validasi tidak berhasil, hentikan aksi menyimpan
      return;
    }

    form_data.append('visit_order_id', $('#formSetVisitedVisitOrder').attr('data-id'));
    form_data.append('checklists', JSON.stringify(selectedChecklists));

    $.ajax({
      data: form_data,
      url: baseUrl + url,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function success(response) {
        if (response.status) {
          dt_ajax.draw();
          modal_set_visited_visit_order.modal('hide');
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
  });
  // End Form

  var editVisitOrderForm = document.getElementById('formEditVisitOrder');
  // edit visit order form validation
  var fvEdit = FormValidation.formValidation(editVisitOrderForm, {
    fields: {
      client_id: {
        validators: {
          notEmpty: {
            message: 'Please select client'
          }
        }
      },
      site_id: {
        validators: {
          notEmpty: {
            message: 'Please select site'
          }
        }
      },
      site_contact_id: {
        validators: {
          notEmpty: {
            message: 'Please select site contact'
          }
        }
      },
      debtor_id: {
        validators: {
          notEmpty: {
            message: 'Please enter debtor'
          }
        }
      },
      visit_type_id: {
        validators: {
          notEmpty: {
            message: 'Please select visit type'
          }
        }
      },
      partner_id: {
        validators: {
          notEmpty: {
            message: 'Please select partner'
          }
        }
      },
      visit_order_number: {
        validators: {
          notEmpty: {
            message: 'Please enter order number'
          }
        }
      },
      visit_order_location: {
        validators: {
          notEmpty: {
            message: 'Please enter order location'
          }
        }
      },
      visit_order_location_map: {
      },
      visit_order_date: {
        validators: {
          notEmpty: {
            message: 'Please enter order date'
          }
        }
      },
      visit_order_due_date: {
        validators: {
          notEmpty: {
            message: 'Please enter order due date'
          },
          date: {
            format: 'YYYY-MM-DD',
            message: 'The order due date is not a valid date'
          },
          callback: {
            message: 'The order due date must be after or equal to the order date',
            callback: function (input) {
              var startDate = new Date(editVisitOrderForm.querySelector('[name="visit_order_date"]').value);
              var endDate = new Date(input.value);

              // Mengubah tanggal menjadi bilangan bulan dalam setahun
              var startMonth = startDate.getFullYear() * 12 + startDate.getMonth();
              var endMonth = endDate.getFullYear() * 12 + endDate.getMonth();

              return endDate >= startDate && endMonth >= startMonth;
            }
          }

        }
      },
      visit_order_note: {
      },
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        eleValidClass: '',
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      // Submit the form when all fields are valid
      // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  }).on('core.form.valid', function () {

    var form_data = {
      client_id: $('#client_id').val(),
      site_id: $('#site_id').val(),
      site_contact_id: $('#site_contact_id').data('selected-id'),
      debtor_id: $('#debtor_id').data('selected-id'),
      visit_type_id: $('#visit_type_id').val(),
      partner_id: $('#partner_select').val(),
      visit_order_number: $('#visit_order_number').val(),
      visit_order_custom_number: $('#visit_order_custom_number').val(),
      visit_order_date: $('#visit_order_date').val(),
      visit_order_due_date: $('#visit_order_due_date').val(),
      visit_order_location: $('#autosize-location').val(),
      visit_order_province: $('#visit_order_province').val(),
      visit_order_regency: $('#visit_order_regency').val(),
      visit_order_district: $('#visit_order_district').val(),
      visit_order_location_map: $('#visit_order_location_map').val(),
      visit_order_latitude: $('#visit_order_latitude').val(),
      visit_order_longitude: $('#visit_order_longitude').val(),
      visit_order_note: $('#autosize-note').val(),
      data_id: $('#formEditVisitOrder').attr('data-id'),
    };

    if ($('#formEditVisitOrder').data('method') == 'edit') {
      var url = "site-visit/visit-order-manage/update/" + form_data.data_id;
    } else {
      var url = "";
    }

    $.ajax({
      data: form_data,
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax.draw();
        modal_edit_visit_order.modal('hide');
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
        modal_edit_visit_order.modal('hide');
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
  // End Form

  var visualVisitOrderForm = document.getElementById('formVisualVisitOrder');
  // done visit order form validation
  var fvVisual = FormValidation.formValidation(visualVisitOrderForm, {
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
    var url = "site-visit/visit-order-manage/uploadFile/" + $('#formVisualVisitOrder').attr('data-id');
    var form_data = new FormData(visualVisitOrderForm);

    $.ajax({
      data: form_data,
      url: baseUrl + url,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function success(response) {
        dt_ajax.draw();
        if (response.status) {
          modal_visual_visit_order.modal('hide');
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
  // End Form

  var statusVisitOrderForm = document.getElementById('formChangeStatus');
  // done visit order form validation
  FormValidation.formValidation(statusVisitOrderForm, {
    fields: {
      edit_note: {
        validators: {
          notEmpty: {
            message: 'Please enter description'
          }
        }
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
    var status = $('#formChangeStatus').attr('data-status');
    var numericStatus = parseInt(status); // Convert to number
    var url = '';
    if (numericStatus === 3) {
      url = "site-visit/visit-order-manage/cancel";
    } else if (numericStatus === 4) {
      url = "site-visit/visit-order-manage/re-visit";
    } else if (numericStatus === 6) {
      url = "site-visit/visit-order-manage/validate";
    } else if (numericStatus === 2) {
      url = "site-visit/visit-order-manage/assign";
    }

    var formData = {
      visit_order_id: $('#formChangeStatus').attr('data-id'),
      visit_order_status: numericStatus,
      edit_desc: $('#edit_note').val(),
      partner_id: $('#partner_id').val()
    };

    $.ajax({
      data: formData,
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        var status = parseInt($('#formChangeStatus').attr('data-status'));
        var visitOrderId = $('#formChangeStatus').attr('data-id');

        var updatedStatus = getStatusLabel(status);

        // Locate the row in the DataTable by the visit_order_id
        var rowIndex = dt_ajax.row("#row_" + visitOrderId).index();


        var rowData = dt_ajax.row(rowIndex).data();
        rowData.visit_order_status = status;
        dt_ajax.row(rowIndex).data(rowData);

        var statusCell = dt_ajax.cell(rowIndex, 10); // Adjust the column index accordingly
        statusCell.nodes().to$().html(updatedStatus);

        $('#edit_note').val("");
        $('#partner_id').val(null).trigger('change');
        modal_change_status.modal('hide');

        if (response.status) {
          dt_ajax.draw();
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
        modal_change_status.modal('hide');
        $('#partner_id').val(null).trigger('change');
        $('#edit_note').val("");
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
  // End Form

  // Edit button handler
  $(document).on('click', '#dropdownMenuEdit', function () {
    var visit_order_id = $(this).data('id');

    // get data
    $.ajax({
      url: baseUrl + "site-visit/visit-order-manage/show/" + visit_order_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {

        // $('#visit_order_status').val(response.data.visit_order_status);

        if (response.data.client) {
          var option = new Option(response.data.client.client_name, response.data.client.client_id, true, true);
          $('#client_id').append(option).trigger('change');
        }

        if (response.data.site) {
          var option = new Option(response.data.site.site_name, response.data.site.site_id, true, true);
          $('#site_id').append(option).trigger('change');
        }

        // Set site_contact_id if available
        if (response.data.site_contact) {
          $('#site_contact_id').val(response.data.site_contact.site_contact_fullname);
          $('#site_contact_id').data('selected-id', response.data.site_contact.site_contact_id);
        } else {
          $('#site_contact_id').val('');
          $('#site_contact_id').data('selected-id', null);
        }

        // Set debtor_id if available
        if (response.data.debtor) {
          $('#debtor_id').val(response.data.debtor.debtor_name);
          $('#debtor_id').data('selected-id', response.data.debtor.debtor_id);
        } else {
          $('#debtor_id').val('');
          $('#debtor_id').data('selected-id', null);
        }

        if (response.data.visit_type) {
          var option = new Option(response.data.visit_type.visit_type_name, response.data.visit_type.visit_type_id, true, true);
          $('#visit_type_id').append(option).trigger('change');
        }

        if (response.data.partner) {
          var option = new Option(response.data.partner.partner_name, response.data.partner.partner_id, true, true);
          $('#partner_select').append(option).trigger('change');
        }

        $('#visit_order_number').val(response.data.visit_order_number);
        $('#visit_order_custom_number').val(response.data.visit_order_custom_number);
        $('#autosize-location').val(response.data.visit_order_location);
        $('#visit_order_date').val(response.data.visit_order_date);
        $('#visit_order_due_date').val(response.data.visit_order_due_date);
        $('#visit_order_location_map').val(response.data.visit_order_location_map);

        // Set the latitude and longitude for the marker
        var latitude = parseFloat(response.data.visit_order_latitude);
        var longitude = parseFloat(response.data.visit_order_longitude);

        $('#visit_order_latitude').val(isNaN(latitude) ? '' : latitude);
        $('#visit_order_longitude').val(isNaN(longitude) ? '' : longitude);

        // Call initMap here with the updated values
        initMap();

        $('.visit_order_note').val(response.data.visit_order_note);
        modal_class_loader.unblock();
      }
    });

    $('#editFormLabel > p').html('Edit visit order.');
    $('#formEditVisitOrder').attr('data-method', 'edit');
    $('#formEditVisitOrder').data('method', 'edit');
    $('#formEditVisitOrder').attr('data-id', visit_order_id);
    modal_edit_visit_order.modal('show');
  });

  $(document).ready(function () {
    $('#debtor_id').autocomplete({
      source: function (request, response) {
        $.ajax({
          url: baseUrl + 'autocomplete/get-all-debtor',
          type: 'GET',
          dataType: 'json',
          data: { search: request.term },
          success: function (data) {
            // If there are results, show all debtor names, else show "No results found"
            if (data.length > 0) {
              response(data);
            } else {
              response([{ id: -1, value: 'No results found', label: 'No results found' }]);
            }
          },
          error: function (err) {
            console.error(err);
          },
        });
      },
      minLength: 0, // Allow empty searches
      select: function (event, ui) {
        event.preventDefault();

        // Check if the selected item is the "No results found" item (ID: -1)
        // if (ui.item.id !== -1) {
        //     $(this).val(ui.item.value);
        // }

        if (ui.item.id !== -1) {
          $(this).val(ui.item.label); // Set the input value to the selected site contact's label (name)
          $(this).data('selected-id', ui.item.id); // Store the selected site contact's ID as a data attribute
        }
      },
      open: function () {
        $(this).autocomplete("widget").addClass('dropdown-menu').css('max-height', '200px').css('overflow-y', 'auto');
      },
      focus: function (event, ui) {
        // Prevent the default behavior to stop the value from being set when an item is focused
        event.preventDefault();
      },
      autoFocus: true, // Automatically focus the first item when the menu is shown
    }).on('focus', function () {
      // Trigger the search manually when the input is focused
      $(this).autocomplete("search");
    });
  });

  // Handle the click event on the save button
  $(document).on('click', '#save_to_debtor', function () {
    var debtor_name = $('#debtor_id').val();
    var debtor_id = $('#debtor_id').data('selected-id'); // Retrieve the selected debtor's ID

    if (debtor_name.trim() !== '') {
      // Show SweetAlert confirmation before saving the data
      Swal.fire({
        title: 'Confirmation',
        text: 'Are you sure you want to save debtor with name: ' + debtor_name + ' ?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
      }).then(function (result) {
        if (result.isConfirmed) {
          // If user confirms, proceed with saving data
          $.ajax({
            url: baseUrl + 'site-visit/visit-order-create/store-to-debtor',
            type: 'POST',
            dataType: 'json',
            data: {
              debtor_id: debtor_id,
              debtor_name: debtor_name
            },
            success: function (response) {
              if (response.status === true) {
                Swal.fire({
                  icon: 'success',
                  title: response.message.title,
                  text: response.message.text,
                  customClass: {
                    confirmButton: 'btn btn-success'
                  }
                });
                // Optionally, you can update the input value with the newly created debtor
                $('#debtor_id').val(response.debtor_name);
              } else {
                // jika debtor sudah ada
                Swal.fire({
                  icon: 'info',
                  title: response.message.title,
                  text: response.message.text,
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  }
                });
              }
            },
            error: function (err) {
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
    } else {
      // Show an error message that the debtor name must be filled
      Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'Please input a debtor name before saving.',
        customClass: {
          confirmButton: 'btn btn-primary'
        }
      });
    }
  });

  // Handle the click event on the save button
  $(document).on('click', '#save_to_site_contact', function () {
    var site_contact_fullname = $('#site_contact_id').val();
    var site_contact_id = $('#site_contact_id').data('selected-id'); // Retrieve the selected site contact's ID

    if (site_contact_fullname.trim() !== '') {
      // Show SweetAlert confirmation before saving the data
      Swal.fire({
        title: 'Confirmation',
        text: 'Are you sure you want to save site contact with name: ' + site_contact_fullname + ' ?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
      }).then(function (result) {
        if (result.isConfirmed) {
          // If user confirms, proceed with saving data
          $.ajax({
            url: baseUrl + 'site-visit/visit-order-create/store-to-site-contact',
            type: 'POST',
            dataType: 'json',
            data: {
              site_contact_id: site_contact_id,
              site_id: selectedSiteId, // var global bray
              site_contact_fullname: site_contact_fullname
            },
            success: function (response) {
              if (response.status === true) {
                Swal.fire({
                  icon: 'success',
                  title: response.message.title,
                  text: response.message.text,
                  customClass: {
                    confirmButton: 'btn btn-success'
                  }
                });
                // Optionally, you can update the input value with the newly created site contact name
                $('#site_contact_id').val(response.site_contact_fullname);

              } else {
                // jika site contact name sudah ada
                Swal.fire({
                  icon: 'info',
                  title: response.message.title,
                  text: response.message.text,
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  }
                });
              }
            },
            error: function (err) {
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
    } else {
      // Show an error message that the site contact name must be filled
      Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'Please input a site contact name before saving.',
        customClass: {
          confirmButton: 'btn btn-primary'
        }
      });
    }
  });

  //Checklist Button Handler
  $(document).on('click', '#dropdownMenuChecklist', function () {
    var visit_order_id = $(this).data('id');
    visit_order_id_global = visit_order_id;

    // Fungsi untuk Checklist order visit
    // Ambil data dari server menggunakan AJAX
    var modalContent = $('.modal-content');
    modalContent.addClass('modal-block-loader');
    $.ajax({
      url: baseUrl + "partner/partner-visit/checklist/" + visit_order_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {

        // Tampilkan data checklist
        getChecklist(response, 'checkboxContainer');

        // Hilangkan loader dan hapus kelas CSS
        modalContent.removeClass('modal-block-loader');
        modal_class_loader.unblock();
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

        // Hapus kelas CSS dan hilangkan loader
        modalContent.removeClass('modal-block-loader');
        modal_class_loader.unblock();
      }
    });

    modal_checklist_visit_order.modal('show');
  });

  $(document).on('click', '#saveChangesButton', function () {
    // Ambil semua radio yang dicentang
    // var radioes = document.querySelectorAll('input[name="checklist"]:checked');
    var radioes = document.querySelectorAll('input[name^="checklist_group_sort-"]:checked');

    // Buat array utk menyimpan nilai-nilai yang akan disimpan atau diperbaharui
    var selectedChecklists = [];
    var isValid = true; // Menyimpan status validasi

    // loop melalui radio yang dicentang
    radioes.forEach(function (radio) {
      var checklistId = radio.value;
      var checklistText = '';

      // Jika radio memiliki input teks terkait, ambil nilainya
      if (radio.nextSibling && radio.nextSibling.tagName === 'INPUT') {
        var inputText = radio.nextSibling;
        checklistText = inputText.value;

        // Validasi required jika input text tidak kosong
        if (inputText.required && checklistText.trim() === '') {
          isValid = false;
          inputText.classList.add('is-invalid'); // Tambahkan kelas CSS untuk menandai input tidak valid
          inputText.setCustomValidity('Please enter for lain-lain ?'); // Pesan validasi kustom
        } else {
          inputText.classList.remove('is-invalid'); // Hapus kelas CSS jika input valid
          inputText.setCustomValidity(''); // Hapus pesan validasi kustom
        }

        // Tambahkan event listener invalid untuk menampilkan pesan validasi kustom
        inputText.addEventListener('invalid', function () {
          if (inputText.validity.valueMissing) {
            inputText.setCustomValidity('Please enter for lain-lain ?');
          } else {
            inputText.setCustomValidity('');
          }
        });
      }

      // Buat objek untuk setiap checklist yg dicentang
      var selectedChecklist = {
        checklistId: checklistId,
        checklistText: checklistText
      };

      // Tambahkan objek ke dalam array
      selectedChecklists.push(selectedChecklist);
    });

    if (!isValid) {
      // Jika validasi tidak berhasil, hentikan aksi menyimpan
      return;
    }

    // Kirim perminataan AJAX utk menyimpan atau memperbaharui data checklist
    $.ajax({
      url: baseUrl + "partner/partner-visit/checklist/save",
      type: "POST",
      data: {
        visitOrderId: visit_order_id_global,
        checklists: selectedChecklists
      },
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {
        // tindakan yg diambil setelah berhasil menyimpan / memperbaharui data checklist
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
              confirmButton: 'btn btn-danger'
            }
          });
        }

        modal_checklist_visit_order.modal('hide');
        modal_class_loader.unblock();
      },
      error: function (err) {
        Swal.fire({
          title: 'Error!',
          text: "Internal Server Error",
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-primary'
          }
        });
        modal_view_visit_order.modal('hide');
      }
    });
  });

  //Visual File Button Handler
  $(document).on('click', '#dropdownMenuVisual', function () {
    var visit_order_id = $(this).data('id');

    // get data
    $.ajax({
      url: baseUrl + "site-visit/visit-order-manage/show/" + visit_order_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {
        $('#file_upload').empty();
        var text = '';
        $.each(response.data.visit_type.visit_visual_type, function (index, value) {
          text += '<div class="row">' +
            '<div class="col mb-3">' +
            '<label class="form-label" for="image-' + value.checklist_visual.visit_visual_type_id + '">Change Image ' + value.checklist_visual.visit_visual_type_name + ' <span style="color:red">*</span></label>' +
            '<input type="file" class="form-control" id="file" data-id="' + value.checklist_visual.visit_visual_type_id + '" name="image-' + value.checklist_visual.visit_visual_type_id + '" id="image-' + value.checklist_visual.visit_visual_type_id + '" required/>' +
            // '<div id="imagePreview" class="mt-3"></div>' +
            '<br>' +
            '</div>' +
            '</div>';
        });
        $('#file_upload').append(text);

        modal_class_loader.unblock();
      }
    });

    $('#formVisualVisitOrder').attr('data-method', 'edit');
    $('#formVisualVisitOrder').attr('data-id', visit_order_id);
    modal_visual_visit_order.modal('show');
  });

  // Assign status visit order
  $(document).on('click', '#dropdownMenuAssign', function () {
    var visit_order_id = $(this).data('id');

    $('#partner').removeClass('d-none');
    $('#title-header').text("Assign Visit Order");
    $('#desc-header').text('assign.');
    $('#formChangeStatus').attr('data-id', visit_order_id);
    $('#formChangeStatus').attr('data-status', 2);
    modal_change_status.modal('show');
  });

  // Cancel status visit order
  $(document).on('click', '#dropdownMenuCancel', function () {
    var visit_order_id = $(this).data('id');

    $('#partner').addClass('d-none')
    $('#partner_id').val(null).trigger('change');
    $('#title-header').text("Cancel Visit Order");
    $('#desc-header').text('cancel.');
    $('#formChangeStatus').attr('data-id', visit_order_id);
    $('#formChangeStatus').attr('data-status', 3);
    modal_change_status.modal('show');
  });

  // set download visit order
  $(document).on('click', '#dropdownMenuSetDownload', function () {
    var visit_order_id = $(this).data('id');

    Swal.fire({
      title: 'Apakah anda yakin?',
      text: "Apakah nomor pesanan kunjungan ini ingin ditandai menjadi sudah diunduh ?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, saya yakin!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          data: { visit_order_id: visit_order_id },
          type: 'POST',
          url: baseUrl + 'site-visit/visit-order-manage/set-download',
          success: function success(response) {
            dt_ajax.ajax.reload(null,false);
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

  // set download visit order
  $(document).on('click', '#dropdownMenuSetVisited', function () {
    var visit_order_id = $(this).data('id');
    visit_order_id_global = visit_order_id;

    // Fungsi untuk Checklist order visit
    // Ambil data dari server menggunakan AJAX
    var modalContent = $('.modal-content');
    modalContent.addClass('modal-block-loader');
    $.ajax({
      url: baseUrl + "partner/partner-visit/checklist/" + visit_order_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {

        // Tampilkan data checklist
        getChecklist(response, 'checkboxContainerVisited');

        // Hilangkan loader dan hapus kelas CSS
        modalContent.removeClass('modal-block-loader');
        modal_class_loader.unblock();
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

        // Hapus kelas CSS dan hilangkan loader
        modalContent.removeClass('modal-block-loader');
        modal_class_loader.unblock();
      }
    });

    // get data
    $.ajax({
      url: baseUrl + "site-visit/visit-order-manage/show/" + visit_order_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {
        $('#file_upload_visited').empty();
        var text = '';
        $.each(response.data.visit_type.visit_visual_type, function (index, value) {
          text += '<div class="row">' +
            '<div class="col mb-3">' +
            '<label class="form-label" for="image-' + value.checklist_visual.visit_visual_type_id + '">Change Image ' + value.checklist_visual.visit_visual_type_name + ' <span style="color:red">*</span></label>' +
            '<input type="file" class="form-control" id="file" data-id="' + value.checklist_visual.visit_visual_type_id + '" name="image-' + value.checklist_visual.visit_visual_type_id + '" id="image-' + value.checklist_visual.visit_visual_type_id + '" required/>' +
            '<br>' +
            '</div>' +
            '</div>';
        });
        $('#file_upload_visited').append(text);

        modal_class_loader.unblock();
      }
    });

    $('#formSetVisitedVisitOrder').attr('data-id', visit_order_id);
    modal_set_visited_visit_order.modal('show');
  });

  // Delete status visit order
  $(document).on('click', '#dropdownMenuDelete', function () {
    var visit_order_id = $(this).data('id');

    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'error',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          data: { visit_order_id: visit_order_id, visit_order_status: 99 },
          type: 'POST',
          url: baseUrl + 'site-visit/visit-order-manage/delete',
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

  // Re Visit status visit order
  $(document).on('click', '#dropdownMenuReVisit', function () {
    var visit_order_id = $(this).data('id');

    $('#partner').removeClass('d-none');
    $('#title-header').text("Re-Visit Visit Order");
    $('#desc-header').text('re-visit.');
    $('#formChangeStatus').attr('data-id', visit_order_id);
    $('#formChangeStatus').attr('data-status', 4);
    modal_change_status.modal('show');
  });

  // Validate status visit order
  $(document).on('click', '#dropdownMenuValidate', function () {
    var visit_order_id = $(this).data('id');

    $('#partner').addClass('d-none');
    $('#partner_id').val(null).trigger('change');
    $('#title-header').text("Validate Visit Order");
    $('#desc-header').text('validate.');
    $('#formChangeStatus').attr('data-id', visit_order_id);
    $('#formChangeStatus').attr('data-status', 6);
    modal_change_status.modal('show');
  });

  $(document).on('click', '#dropdownMenuDetail', function () {
    var visit_order_id = $(this).data('id');
    visit_order_id_global = $(this).data('id');

    // Fungsi untuk Detail order visit
    // get data
    $.ajax({
      url: baseUrl + "site-visit/visit-order-manage/show/" + visit_order_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {

        $('#edit_number').text(response.data.visit_order_number);

        // Mengubah format tanggal
        function formatDate(inputDate) {

          if (!inputDate || inputDate === '-') {
            return '-';
          }

          const months = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
          ];

          const parts = inputDate.split("-");
          const day = parseInt(parts[2], 10);
          const monthIndex = parseInt(parts[1], 10) - 1;
          const year = parseInt(parts[0], 10);

          return `${day} ${months[monthIndex]} ${year}`;
        }

        const originalDate = response.data.visit_order_date;
        // Memformat tanggal dengan fungsi formatDate
        const formattedDate = formatDate(originalDate);

        // visited date format
        const latestVisitedDate = response.data.visit_order_visited_date
        const formattedLatestVisitedDate = response.data.visit_order_visited_date ? formatDate(latestVisitedDate) : '-';

        // Menetapkan hasilnya ke elemen dengan id "edit_date"
        $('#edit_date').text(formattedDate);
        $('#edit_visited_date').text(formattedLatestVisitedDate);
        $('#edit_location').text(response.data.visit_order_location);
        $('#edit_client_name').text(response.data.client.client_name);
        $('#edit_site_name').text(response.data.site.site_name);
        $('#edit_site_contact_name').text(response.data.site_contact.site_contact_fullname ? response.data.site_contact.site_contact_fullname : '-');
        $('#edit_debtor_name').text(response.data.debtor.debtor_name);
        $('#edit_visit_type_name').text(response.data.visit_type.visit_type_name);
        $('#edit_custom_number').text(response.data.visit_order_custom_number ? response.data.visit_order_custom_number : '-');
        $('#edit_debtor_address').text(response.data.debtor.debtor_address ? response.data.debtor.debtor_address : '-');
        $('.edit_desc').val(response.data.visit_order_note ? response.data.visit_order_note : '-');
        $('#detLocationMap').text(response.data.visit_order_location_map);

        // Construct the Google Maps URL with latitude and longitude
        var latitude = response.data.visit_order_latitude;
        var longitude = response.data.visit_order_longitude;

        if (latitude !== null && longitude !== null) {
          var mapUrl = "https://maps.google.com/maps?q=" + latitude + "," + longitude + "&output=embed";
          $('#mapIframe').attr('src', mapUrl);
          $('#openGoogleMapsBtn').attr('href', "https://maps.google.com/maps?q=" + latitude + "," + longitude);
          $('#mapIframe').show(); // Show the iframe
          $('#openGoogleMapsBtn').show(); // Show the button
        } else {
          $('#mapIframe').hide(); // Hide the iframe
          $('#openGoogleMapsBtn').hide(); // Hide the button
          $('#detLocationMap').text('Alamat belum di set / alamat belum ada.'); // Display the text
        }

        getImageData(response.data.visit_order_visual);
        getTimelineData(response.data.history);

        modal_class_loader.unblock();
      }
    });

    // Fungsi untuk Checklist order visit
    // Ambil data dari server menggunakan AJAX
    var modalContent = $('.modal-content');
    modalContent.addClass('modal-block-loader');
    $.ajax({
      url: baseUrl + "partner/partner-visit/checklist/" + visit_order_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {

        // Tampilkan data checklist
        getChecklistData(response);

        // Hilangkan loader dan hapus kelas CSS
        modalContent.removeClass('modal-block-loader');
        modal_class_loader.unblock();
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

        // Hapus kelas CSS dan hilangkan loader
        modalContent.removeClass('modal-block-loader');
        modal_class_loader.unblock();
      }
    });

    modal_detail_visit_order.modal('show');
  });

  // Delete visual button handler
  $(document).on('click', '#dropdownMenuDeleteVisual', function () {
    var visit_order_visual_id = $(this).data('id');

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
          data: { visit_order_visual_id: visit_order_visual_id },
          type: 'POST',
          url: baseUrl + 'site-visit/visit-order-manage/visual-file/delete',
          success: function success(response) {
            dt_ajax_visual_file.draw();
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
  // End Data Visual File

  // Refresh checklist visit order
  $(document).on('click', '#refresh-group-checklist', function () {
    var group_checklist_id = $(this).data('id');

    $('input[name="checklist_group_sort-' + group_checklist_id + '"]').prop('checked', false);
  });
  // End Refresh checklist visit order

  // location-maxlength & repeater (jquery)
  $(function () {
    var maxlengthInput = $('.location-maxlength');

    // Location Max Length
    // --------------------------------------------------------------------
    if (maxlengthInput.length) {
      maxlengthInput.each(function () {
        $(this).maxlength({
          warningClass: 'label label-success bg-success text-white',
          limitReachedClass: 'label label-danger',
          separator: ' out of ',
          preText: 'You typed ',
          postText: ' chars available.',
          validate: true,
          threshold: +this.getAttribute('maxlength')
        });
      });
    }
  });

  // note-maxlength & repeater (jquery)
  $(function () {
    var maxlengthInput = $('.note-maxlength');

    // Note Max Length
    // --------------------------------------------------------------------
    if (maxlengthInput.length) {
      maxlengthInput.each(function () {
        $(this).maxlength({
          warningClass: 'label label-success bg-success text-white',
          limitReachedClass: 'label label-danger',
          separator: ' out of ',
          preText: 'You typed ',
          postText: ' chars available.',
          validate: true,
          threshold: +this.getAttribute('maxlength')
        });
      });
    }
  });

  function getImageData(response) {
    var visual_image = response;

    // Bersihkan konten yang ada sebelumnya coy
    var fileContainer = document.getElementById('visual-file');
    fileContainer.innerHTML = '';

    if (visual_image.length === 0) {
      var textElement = document.createElement('h5');
      textElement.textContent = "Silahkan upload visual terlebih dahulu pada tab Visual";
      textElement.style.marginTop = '10px';
      fileContainer.appendChild(textElement)
    } else {
      visual_image.forEach(function (image) {
        // Tampilkan nama grup checklist
        var colElement = document.createElement('div');
        colElement.className = 'col-md-6';
        var fileNameElement = document.createElement('h5');
        fileNameElement.textContent = image.visit_order_visual_file_name;
        colElement.appendChild(fileNameElement);
        var fileImageElement = document.createElement('img');
        fileImageElement.src = image.visual_image_url;
        fileImageElement.width = '150';
        colElement.appendChild(fileImageElement);
        fileContainer.appendChild(colElement);
      });
    }
  }

  // Fungsi untuk mengambil data checklist dari response
  function getChecklistData(response) {
    var checklistGroups = response.checklist_groups;
    var checklists = response.checklists;
    var checklistAnswers = response.checklist_answers;

    // Bersihkan konten yang ada sebelumnya coy
    var groupContainer = document.getElementById('checklist-file');
    groupContainer.innerHTML = '';

    if (checklistAnswers.length === 0) {
      var textElement = document.createElement('h5');
      textElement.textContent = "Silahkan lengkapi checklist terlebih dahulu pada tab Checklist";
      textElement.style.marginTop = '10px';
      groupContainer.appendChild(textElement)
    } else {
      checklistGroups.forEach(function (checklistGroup) {
        // Tampilkan nama grup checklist
        var groupNameElement = document.createElement('label');
        groupNameElement.textContent = checklistGroup.checklist_group_name;
        groupNameElement.style.marginTop = '10px';

        var groupChecklists = checklists.filter(function (checklist) {
          return checklist.checklist_group_id === checklistGroup.checklist_group_id;
        });

        groupChecklists.forEach(function (checklist, index) {
          var checklistAnswer = checklistAnswers.find(function (answer) {
            return answer.checklist_id === checklist.checklist_id;
          });

          if (checklistAnswer) {
            var checklistNameElement = document.createElement('h6');
            if (checklist.checklist_is_freetext === 2) {
              checklistNameElement.textContent = checklist.checklist_name + ": " + checklistAnswer.checklist_answer;
            } else {
              checklistNameElement.textContent = checklist.checklist_name;
            }
            checklistNameElement.style.marginTop = '10px';
            groupNameElement.htmlFor = checklist.checklist_name
            checklistNameElement.id = checklist.checklist_name
            groupContainer.appendChild(groupNameElement);
            groupContainer.appendChild(checklistNameElement);
          }
        })
      });
    }
  }

  // Fungsi untuk mengambil data checklist dari response
  function getChecklist(response, idName) {
    var checklistGroups = response.checklist_groups;
    var checklists = response.checklists;
    var checklistAnswers = response.checklist_answers;

    // Bersihkan konten yang ada sebelumnya coy
    var groupContainer = document.getElementById(idName);
    groupContainer.innerHTML = '';

    checklistGroups.forEach(function (checklistGroup) {
      // Tampilkan nama grup checklist
      var groupNameElement = document.createElement('h5');
      groupNameElement.textContent = checklistGroup.checklist_group_name;
      groupNameElement.style.marginTop = '10px';
      groupContainer.appendChild(groupNameElement);

      // Tampilkan button grup checklist
      var groupButtonElement = document.createElement('button');
      groupButtonElement.type = 'button';
      groupButtonElement.className = "btn-refresh btn-primary";
      groupButtonElement.id = 'refresh-group-checklist';
      groupButtonElement.dataset.id = checklistGroup.checklist_group_id;
      var groupButtonIcon = document.createElement('i');
      groupButtonIcon.className = "bx bx-refresh"
      groupButtonElement.appendChild(groupButtonIcon);
      groupContainer.appendChild(groupButtonElement);

      // Tampilkan checklist
      var rowContainer = document.createElement('div');
      rowContainer.classList.add('row');
      groupContainer.appendChild(rowContainer);

      var groupChecklists = checklists.filter(function (checklist) {
        return checklist.checklist_group_id === checklistGroup.checklist_group_id;
      });

      groupChecklists.forEach(function (checklist, index) {
        var radioLabel = document.createElement('label');
        radioLabel.classList.add('radio-container');
        radioLabel.textContent = checklist.checklist_name;
        radioLabel.style.marginBottom = '10px';

        var radio = document.createElement('input');
        radio.type = 'radio';
        radio.name = 'checklist_group_sort-' + checklistGroup.checklist_group_id;
        radio.value = checklist.checklist_id;
        radio.classList.add('form-check-input');

        var inputText = null;
        if (checklist.checklist_is_freetext === 2) {
          // Jika checklist_is_freetext = 2, tambahkan input text
          var inputText = document.createElement('input');
          inputText.type = 'text';
          inputText.disabled = true;
          inputText.name = 'checklistText';
          inputText.placeholder = 'Lain-lain';
          inputText.classList.add('form-control');
          inputText.required = true;

          radioLabel.appendChild(radio);
          radioLabel.appendChild(inputText);
        } else {
          radioLabel.appendChild(radio);
        }

        // Tambahkan container form-check untuk tampilan yang bagus
        var formCheckContainer = document.createElement('div');
        formCheckContainer.classList.add('form-check', 'form-check-primary', 'mt-3');
        formCheckContainer.appendChild(radioLabel);

        // Tentukan posisi radio menggunakan modulus 2
        if (index % 2 === 0) {
          // Jika index genap, tambahkan ke kolom pertama
          var colContainer = document.createElement('div');
          colContainer.classList.add('col');
          colContainer.appendChild(formCheckContainer);
          rowContainer.appendChild(colContainer);
        } else {
          // Jika index ganjil, tambahkan ke kolom kedua
          var lastColContainer = rowContainer.lastElementChild;
          lastColContainer.appendChild(formCheckContainer);
        }

        // Cek apakah checklist sudah tercheck di tabel sys_checklist_answer
        var checklistAnswer = checklistAnswers.find(function (answer) {
          return answer.checklist_id === checklist.checklist_id;
        });

        if (checklistAnswer) {
          radio.checked = true;
          if (inputText) {
            inputText.disabled = !radio.checked;
            inputText.value = checklistAnswer.checklist_answer;
          }
        }

        radio.addEventListener('change', function () {
          if (inputText) {
            inputText.disabled = !radio.checked;
            if (!radio.checked) {
              if (inputText.value !== '') { // Tambahkan pemeriksaan nilai input text sebelum mengosongkannya
                return; // Jika input text memiliki nilai, jangan mengosongkannya
              }
              inputText.value = '';
              inputText.classList.remove('is-invalid'); // Hapus kelas CSS jika input valid
              inputText.setCustomValidity(''); // Hapus pesan validasi kustom
            }
          }

          // Dapatkan semua input radio di grup ini
          var radioInputs = document.querySelectorAll('input[name="checklist_group_sort-' + checklistGroup.checklist_group_id + '"]');

          // Nonaktifkan input teks "Lain-lain" pada radio yang bukan yang dipilih
          radioInputs.forEach(function (radioInput) {
            var inputText = radioInput.nextSibling;
            if (inputText && inputText.tagName === 'INPUT') {
              inputText.disabled = !radioInput.checked;
              if (!radioInput.checked) {
                if (inputText.value !== '') { // Tambahkan pemeriksaan nilai input text sebelum mengosongkannya
                  return; // Jika input text memiliki nilai, jangan mengosongkannya
                }
                inputText.value = '';
                inputText.classList.remove('is-invalid'); // Hapus kelas CSS jika input valid
                inputText.setCustomValidity(''); // Hapus pesan validasi kustom
              }
            }
          });
        });
      });
    });
  }

  // Mengambil data untuk timeline
  function getTimelineData(response) {
    var timeline_data = response;

    // Bersihkan konten yang ada sebelumnya
    var historyContainer = document.getElementById('history_visit_order');
    historyContainer.innerHTML = '';

    timeline_data.forEach(function (timeline) {
      var stats = getStatusDot(timeline.visit_order_status);
      var dot = `
          <li class="timeline-item timeline-item-transparent">
          <span class="timeline-point ${stats[0]}"></span>
          <div class="timeline-event">
            <div class="timeline-header mb-sm-0 mb-3">
              <h6 class="mb-0">${stats[1]}</h6>
              <small class="text-muted">${timeline.date_created_format} WIB</small>
            </div>
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap border-top-0 p-0">
                <p>
                  ${timeline.visit_order_history_desc}
                </p>
                <strong>
                  ${timeline.user.user_uniq_name}
                </strong>
              </li>
            </ul>
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
        return ['timeline-point-secondary', 'Open'];
      case 2:
        return ['timeline-point-warning', 'Assigned'];
      case 3:
        return ['timeline-point-danger', 'Cancelled'];
      case 4:
        return ['timeline-point-info', 'Revisit'];
      case 5:
        return ['timeline-point-success', 'Visited'];
      case 6:
        return ['timeline-point-primary', 'Validated'];
      case 7:
        return ['timeline-point-primary', 'Can\'t Billed'];
      case 9:
        return ['timeline-point-success', 'Paid from Client'];
      default:
        return ['timeline-point-danger', 'Unknown'];
    }
  }

  function getAllSiteContactData(selectedSiteId) {
    $('#site_contact_id').autocomplete({
      source: function (request, response) {
        $.ajax({
          url: baseUrl + 'autocomplete/get-all-site-contact/find/' + selectedSiteId,
          type: 'GET',
          dataType: 'json',
          disabled: false,
          data: { search: request.term },
          success: function (data) {
            // If there are results, show all site contact names, else show "No results found"
            if (data.length > 0) {
              response(data);
            } else {
              response([{ id: -1, value: 'No results found', label: 'No results found' }]);
            }
          },
          error: function (err) {
            console.error(err);
          },
        });
      },
      minLength: 0, // Allow empty searches
      select: function (event, ui) {
        event.preventDefault();

        // Check if the selected item is the "No results found" item (ID: -1)
        // if (ui.item.id !== -1) {
        //     $(this).val(ui.item.value);
        // }

        if (ui.item.id !== -1) {
          $(this).val(ui.item.label); // Set the input value to the selected site contact's label (name)
          $(this).data('selected-id', ui.item.id); // Store the selected site contact's ID as a data attribute
        }
      },
      open: function () {
        $(this).autocomplete("widget").addClass('dropdown-menu').css('max-height', '200px').css('overflow-y', 'auto');
      },
      focus: function (event, ui) {
        // Prevent the default behavior to stop the value from being set when an item is focused
        event.preventDefault();
      },
      autoFocus: true, // Automatically focus the first item when the menu is shown
    }).on('focus', function () {
      // Trigger the search manually when the input is focused
      $(this).autocomplete("search");
    });
  }

  // Clearing form data when modal hidden
  modal_set_visited_visit_order.on('hidden.bs.modal', function () {
    $('#partner_select_set_visited').val(null).trigger('change');
    fvSetVisit.resetForm(true);
  });

  function getStatusLabel(status) {

    // console.log("getStatusLabel called with status:", status);
    switch (status) {
      case 1:
        return '<span class="badge bg-label-secondary me-1">Open</span>';
      case 2:
        return '<span class="badge bg-label-warning me-1">Assigned</span>';
      case 3:
        return '<span class="badge bg-label-danger me-1">Cancelled</span>';
      case 4:
        return '<span class="badge bg-label-info me-1">Revisit</span>';
      case 5:
        return '<span class="badge bg-label-success me-1">Visited</span>';
      case 6:
        return '<span class="badge bg-label-primary me-1">Validated</span>';
      case 7:
        return '<span class="badge bg-label-primary me-1">Can\'t Billed</span>';
      case 8:
        return '<span class="badge bg-label-primary me-1">Paid to Partner</span>';
      case 9:
        return '<span class="badge bg-label-success me-1">Paid from Client</span>';
      default:
        return '<span class="badge bg-label-danger me-1">Unknown</span>';
    }
  }


});
