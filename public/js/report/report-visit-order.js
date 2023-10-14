/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table = $('.datatables-ajax');
  var modal_detail_visit_order = $('#modalDetailVisitOrder');
  var modal_class_loader = $('.modal-block-loader');
  var ac_client = $('.ac_client');
  var ac_site = $('.ac_site');
  var ac_partner = $('.ac_partner');
  var ac_status = $('.ac_status');
  var ac_time = $('.ac_time');
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

  // Select2 client name handler
  if (ac_client.length) {
    var $this = ac_client;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select client name',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'report/report-visit-order/findClient',
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

  // Select2 site name handler
  if (ac_site.length) {
    var $this = ac_site;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select site name',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'report/report-visit-order/findSite',
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
        url: baseUrl + 'report/report-visit-order/findPartner',
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

  // Select2 status name handler
  if (ac_status.length) {
    var $this = ac_status;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select status name',
      minimumInputLength: 0,
      data: [
        {
          "id": 0,
          "text": "All"
        },
        {
          "id": 2,
          "text": "Assign"
        },
        {
          "id": 3,
          "text": "Cancel"
        },
        {
          "id": 5,
          "text": "Visited"
        },
        {
          "id": 6,
          "text": "Validated"
        },
        {
          "id": 8,
          "text": "Paid to Partner"
        },
        {
          "id": 9,
          "text": "Paid from Client"
        }
      ],
      dropdownParent: $this.parent()
    });
  }

  // Select2 status name handler
  if (ac_time.length) {
    var $this = ac_time;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select filter time',
      minimumInputLength: 0,
      data: [
        {
          "id": 1,
          "text": "Order Date"
        },
        {
          "id": 2,
          "text": "Visited Date"
        },
      ],
      dropdownParent: $this.parent()
    });
  }

  if (dt_ajax_table.length) {
    var dt_ajax_visit_order = dt_ajax_table.DataTable({
      dom: '<"row"<"col-sm-12 col-md-6"l>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
    });
  }

  $('input[type=radio][name=searchParam]').on('change', function () {
    $(".client_select").css({ "display": "none" });
    $(".site_select").css({ "display": "none" });
    $(".partner_select").css({ "display": "none" });
    $('#client_id').val(null).trigger('change')
    $('#site_id').val(null).trigger('change')
    $('#partner_id').val(null).trigger('change')
    $("." + this.value + "_select").css({ "display": "block" });
  });

  // Get Form
  var filter_visit_order_form = document.getElementById('formFilterVisitOrder');
  // visit order Form Validation
  var fv = FormValidation.formValidation(filter_visit_order_form, {
    fields: {
      status_id: {
        validators: {
          notEmpty: {
            message: 'Please enter status'
          }
        }
      },
      time_id: {
        validators: {
          notEmpty: {
            message: 'Please enter status'
          }
        }
      },
      start_date: {
        validators: {
          notEmpty: {
            message: 'Please enter start date'
          }
        }
      },
      end_date: {
        validators: {
          notEmpty: {
            message: 'Please enter end date'
          }
        }
      },
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        eleValidClass: '',
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  }).on('core.form.valid', function () {

    var form_data = new FormData(filter_visit_order_form);
    // Adding visit order when form successfully validate
    var url = "report/report-visit-order/get";

    dt_ajax_visit_order = dt_ajax_table.DataTable({
      processing: true,
      serverSide: true,
      initComplete: onInit,
      destroy: true,
      ajax: {
        url: baseUrl + url,
        type: "GET",
        data: {
          "client_id": $('#client_id').val(),
          "site_id": $('#site_id').val(),
          "partner_id": $('#partner_id').val(),
          "status_id": $('#status_id').val(),
          "time_id": $('#time_id').val(),
          "start_date": $('#start_date').val(),
          "end_date": $('#end_date').val(),
        },
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + url);
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
        { data: 'debtor_name' },
        { data: 'client_name' },
        { data: 'site_name' },
        { data: 'province_name' },
        { data: 'regency_name' },
        { data: 'visit_order_location' },
        { data: 'partner_name' },
        { data: 'visit_order_custom_number' },
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
        { data: 'visit_order_status' },
      ],
      columnDefs: [
        {
          targets: 13,
          searchable: false,
          render: function (data, type, row, meta) {
            if (data == 1) {
              return '<span class="badge bg-label-secondary me-1">Open</span>';
            } else if (data == 2) {
              return '<span class="badge bg-label-warning me-1">Assigned</span>';
            } else if (data == 3) {
              return '<span class="badge bg-label-danger me-1">Cancelled</span>';
            } else if (data == 4) {
              return '<span class="badge bg-label-info me-1">Revisit</span>';
            } else if (data == 5) {
              return '<span class="badge bg-label-success me-1">Visited</span>';
            } else if (data == 6) {
              return '<span class="badge bg-label-primary me-1">Validated</span>';
            } else if (data == 9) {
              return '<span class="badge bg-label-success me-1">Paid from Client</span>';
            } else {
              return '<span class="badge bg-label-danger me-1">Unknown</span>';
            }
          }
        },
        {
          targets: 14,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
            var text = '';
            text += '' +
              // '<button id="buttonMenuDetail" class="btn btn-sm btn-icon btn-primary mx-1" data-id="' + row.visit_order_id + '" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="Detail ' + row.visit_order_number + '"><i class="bx bx-detail me-1"></i></button>';
              '<div class="d-inline-block text-nowrap">' +
              '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
              '<div class="dropdown-menu">' +
              '<a id="dropdownMenuDetail" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-detail me-1"></i> Detail</a>';
            if (row.visit_order_status >= 5) {
              text += '<div class="dropdown-divider"></div>' +
                '<a id="dropdownMenuPdf" target="_blank" href="/report/report-visit-order/pdf/' + row.visit_order_encrypt_id + '" class="dropdown-item"><i class="bx bxs-file-pdf"></i> PDF</a>' +
                '<div class="dropdown-divider"></div>' +
                '<a id="dropdownMenuExcel" href="/report/report-visit-order/excel/' + row.visit_order_id + '" class="dropdown-item"><i class="bx bxs-spreadsheet"></i> Excel</a>' +
                '<div class="dropdown-divider"></div>' +
                '<a id="dropdownMenuSetDownload" data-id="' + row.visit_order_id + '" class="dropdown-item"><i class="bx bxs-download"></i> Set Download</a>';
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
  });
  // End Add Form

  function onInit() {
    $(document).on('keyup', '.search', function (e) {
      var $this = $(this);
      clearTimeout(typingTimer);
      typingTimer = setTimeout(function () {
        dt_ajax_visit_order.search($this.val()).draw();
      }, 1200);
    });
  }

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
            dt_ajax_table.DataTable().draw();
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

  $(document).on('click', '#dropdownMenuDetail', function () {
    var visit_order_id = $(this).data('id');
    visit_order_id_global = $(this).data('id');

    // Fungsi untuk Detail order visit
    // get data
    $.ajax({
      url: baseUrl + "report/report-visit-order/show/" + visit_order_id,
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
        const latestVisitedDate = response.data.visit_order_visited_date ? response.data.visit_order_visited_date : '-'
        const formattedLatestVisitedDate = formatDate(latestVisitedDate);

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

  $(document).on('click', '#printToExcel', function () {
    // var visit_order_id = $(this).data('id');
    var client_id = $('#client_id').val();
    var site_id = $('#site_id').val();
    var partner_id = $('#partner_id').val();
    var status_id = $('#status_id').val();
    var time_id = $('#time_id').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();

    if (status_id == '' || time_id == '' || start_date == '' || end_date == '') {
      Swal.fire({
        title: 'Error!',
        text: "Fill all required fields",
        icon: 'error',
        customClass: {
          confirmButton: 'btn btn-primary'
        }
      });
    } else {
      Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Apakah anda ingin mengunduh excel ?",
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
          window.location = baseUrl + "report/report-visit-order/data-excel?client_id=" + client_id + '&site_id='
            + site_id + '&partner_id=' + partner_id + '&status_id=' + status_id + '&time_id=' + time_id
            + '&start_date=' + start_date + '&end_date=' + end_date;
        }
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
      case 9:
        return ['timeline-point-success', 'Paid from Client'];
      default:
        return ['timeline-point-danger', 'Unknown'];
    }
  }

});
