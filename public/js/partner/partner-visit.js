/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var modal_class_loader = $('.modal-block-loader');
  var modal_view_visit_order = $('#modalViewVisitOrder');

  var typingTimer;
  var visit_order_id_global;
  var searchVisitOrderKeyword = '';

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  });

  // textarea
  const textarea = document.querySelector('#autosize-note');

  // Autosize
  // --------------------------------------------------------------------
  if (textarea) {
    autosize(textarea);
  }

  showCard(searchVisitOrderKeyword);

  // ini untuk next update pagination
  $(document).on('click', '.pagination a', function (event) {
    event.preventDefault();
    var url = $(this).attr('href');
    $.ajax({
      url: url,
      type: 'GET',
      data: {
        search: searchVisitOrderKeyword
      },
      beforeSend: function () {
        window.Helpers.blockUIPageLoader(baseUrl + 'partner/partner-visit/list');
      },
      complete: function () {
        $.unblockUI();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // Do something here
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
      },
      dataType: 'json',
      success: function (response) {
        // update tampilan dengan data yang baru
        // Get the card container element
        const cardContainer = document.getElementById('cardContainer');

        // Clear the container before appending new cards (optional, depends on your requirement)
        cardContainer.innerHTML = '';

        if (response.data.data.length === 0) {
          // Display "partner visit data not found" message
          cardContainer.innerHTML = `
                <div class="col-xl-12 col-lg-5 col-md-5">
                  <div class="card mb-4">
                    <div class="card-body">
                    <h5 class="card-header">Partner visit data not found.</h5>
                    </div>
                  </div>
                </div>`;
        } else {
          // Loop through the data and create cards dynamically
          response.data.data.forEach((item) => {
            const originalDate = item.visit_order_date;
            // Mengubah format tanggal
            function formatDate(inputDate) {
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

            // Memformat tanggal dengan fungsi formatDate
            const formattedDate = formatDate(originalDate);
            const dueDate = formatDate(item.visit_order_due_date);

            const statusLabel = getStatusLabel(item.visit_order_status);

            const card = `
                <div class="col-md-6 col-lg-4 mb-4">
                  <a id="detailClick" class="dropdown-item" data-id="${item.visit_order_id}" href="javascript:void(0);">
                    <div class="card h-100">
                      <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">${item.visit_order_number}</h5>
                        <h6 class="mb-0">${statusLabel}</h6>
                      </div>
                      <div class="card-body">
                        <ul class="p-0 m-0">
                          <li class="d-flex mb-4 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <small class="text-muted d-block mb-1">Order Date :</small>
                                <h6 class="mb-0">${formattedDate}</h6>
                              </div>
                            </div>
                          </li>
                          <li class="d-flex mb-4 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <small class="text-muted d-block mb-1">Due Date :</small>
                                <h6 class="mb-0">${dueDate}</h6>
                              </div>
                            </div>
                          </li>
                          <li class="d-flex mb-4 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <small class="text-muted d-block mb-1">Site Name :</small>
                                <h6 class="mb-0">${item.site.site_name}</h6>
                              </div>
                            </div>
                          </li>
                          <li class="d-flex mb-4 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <small class="text-muted d-block mb-1">Debtor :</small>
                                <h6 class="mb-0">${item.debtor.debtor_name}</h6>
                              </div>
                            </div>
                          </li>
                          <li class="d-flex mb-4 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <small class="text-muted d-block mb-1">Location :</small>
                                <h6 class="mb-0" style="white-space: pre-line">${item.visit_order_location}</h6>
                              </div>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </a>
                </div>
              `;
            // Append the card to the container
            cardContainer.innerHTML += card;
          });

          // update link pagination
          var pagination_links = '';
          var prevLink = '';
          var nextLink = '';

          $.each(response.data.links, function (index, link) {
            if (link.label === '&laquo; Previous') {
              prevLink = '<li class="page-item prev ' + (link.url ? '' : 'disabled') + '"><a class="page-link" href="' + (link.url || '#') + '">' + link.label + '</a></li>';
            } else if (link.label === 'Next &raquo;') {
              nextLink = '<li class="page-item next ' + (link.url ? '' : 'disabled') + '"><a class="page-link" href="' + (link.url || '#') + '">' + link.label + '</a></li>';
            } else {
              var active = link.active ? 'active' : '';
              pagination_links += '<li class="page-item ' + active + '"><a class="page-link" href="' + link.url + '">' + link.label + '</a></li>';
            }
          });

          var pagination = '<ul class="pagination">' + prevLink + pagination_links + nextLink + '</ul>';
          $('.pagination').html(pagination);
        }
      },
      error: function (xhr, status, error) {
        console.log(xhr.responseText);
      }
    });
  });

  // Function to get the corresponding status label based on the status number
  function getStatusLabel(status) {
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
      case 9:
        return '<span class="badge bg-label-success me-1">Paid from Client</span>';
      default:
        return '<span class="badge bg-label-danger me-1">Unknown</span>';
    }
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

  var visualVisitOrderForm = document.getElementById('formVisualVisitOrder');
  // visual visit order form validation
  FormValidation.formValidation(visualVisitOrderForm, {
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
    var url = "partner/partner-visit/updateVisual/" + $('#formVisualVisitOrder').attr('data-id');
    var form_data = new FormData(visualVisitOrderForm);

    $.ajax({
      data: form_data,
      url: baseUrl + url,
      type: 'POST',
      processData: false,
      contentType: false,
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function success(response) {
        modal_view_visit_order.modal('hide');
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
        modal_view_visit_order.modal('hide');
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
  // End Form Visual

  var noteVisitOrderForm = document.getElementById('formNoteVisitOrder');
  // notes visit order form validation
  FormValidation.formValidation(noteVisitOrderForm, {
    fields: {
      edit_desc: {
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
    var url = "partner/partner-visit/updateNotes/" + $('#formNoteVisitOrder').attr('data-id');
    var form_data = new FormData(noteVisitOrderForm);

    $.ajax({
      data: form_data,
      url: baseUrl + url,
      type: 'POST',
      processData: false,
      contentType: false,
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function success(response) {
        modal_view_visit_order.modal('hide');
        showCard(searchVisitOrderKeyword);
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
        modal_view_visit_order.modal('hide');
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
  // End Form Notes

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

        modal_view_visit_order.modal('hide');
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

  $(document).on('click', '#detailClick', function () {
    var visit_order_id = $(this).data('id');
    visit_order_id_global = $(this).data('id');

    // Fungsi untuk Detail order visit
    // get data
    $.ajax({
      url: baseUrl + "partner/partner-visit/show/" + visit_order_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {

        $('#edit_number').text(response.data.visit_order_number);

        const originalDate = response.data.visit_order_date;
        // Mengubah format tanggal
        function formatDate(inputDate) {
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
        $('#edit_debtor_address').text(response.data.debtor.debtor_address);
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

        $('#file_upload').empty();
        var text = '';
        $.each(response.data.visit_type.visit_visual_type, function (index, value) {
          text += '<div class="row">' +
            '<div class="col mb-3">' +
            '<label class="form-label" for="image-' + value.checklist_visual.visit_visual_type_id + '">Change Image ' + value.checklist_visual.visit_visual_type_name + ' <span style="color:red">*</span></label>' +
            '<input type="file" class="form-control" id="file" data-id="' + value.checklist_visual.visit_visual_type_id + '" name="image-' + value.checklist_visual.visit_visual_type_id + '" id="image-' + value.checklist_visual.visit_visual_type_id + '" required/>' +
            '<br>' +
            '<div id="imagePreview-' + value.checklist_visual.visit_visual_type_id + '"></div>' +
            '</div>' +
            '</div>';
        });
        $('#file_upload').append(text);

        getImageData(response.data.visit_order_visual);
        getTimelineData(response.data.history);

        modal_class_loader.unblock();
      }
    });

    $('#formVisualVisitOrder').attr('data-method', 'edit');
    $('#formVisualVisitOrder').attr('data-id', visit_order_id);
    $('#formNoteVisitOrder').attr('data-id', visit_order_id);

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
        getChecklist(response);
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

    $('#checklistFormLabel > p').html('Data Checklist.');
    $('#formChecklistVisitOrder').attr('data-method', 'detail');
    $('#formChecklistVisitOrder').data('method', 'detail');
    $('#formChecklistVisitOrder').attr('data-id', visit_order_id);

    modal_view_visit_order.modal('show');
  });

  // Refresh checklist visit order
  $(document).on('click', '#refresh-group-checklist', function () {
    var group_checklist_id = $(this).data('id');

    $('input[name="checklist_group_sort-' + group_checklist_id+ '"]').prop('checked', false);
  });
  // End Refresh checklist visit order

  $(document).on('keyup', '#search', function (e) {
    var $this = $(this);
    clearTimeout(typingTimer);
    typingTimer = setTimeout(function () {
      searchVisitOrderKeyword = $this.val();
      showCard(searchVisitOrderKeyword);
    }, 1200);
  });

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
          <p>
            ${timeline.visit_order_history_desc}
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

  // Fungsi menampilkan data ke card
  function showCard(search) {
    $.ajax({
      url: baseUrl + 'partner/partner-visit/list',
      data: {
        search: search,
      },
      type: 'GET',
      beforeSend: function () {
        window.Helpers.blockUIPageLoader(baseUrl + 'partner/partner-visit/list');
      },
      complete: function () {
        $.unblockUI();
      },
      error: function (jqXHR, textStatus, errorThrown) {
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
      },
      dataType: 'json',
      success: function (response) {
        // Get the card container element
        const cardContainer = document.getElementById('cardContainer');

        // Clear the container before appending new cards (optional, depends on your requirement)
        cardContainer.innerHTML = '';

        if (response.data.data.length === 0) {
          // Display "partner visit data not found" message
          cardContainer.innerHTML = `
              <div class="col-xl-12 col-lg-5 col-md-5">
                <div class="card mb-4">
                  <div class="card-body">
                  <h5 class="card-header">Partner visit data not found.</h5>
                  </div>
                </div>
              </div>`;
        } else {
          // Loop through the data and create cards dynamically
          response.data.data.forEach((item) => {

            const originalDate = item.visit_order_date;
            // Mengubah format tanggal
            function formatDate(inputDate) {
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

            // Memformat tanggal dengan fungsi formatDate
            const formattedDate = formatDate(originalDate);
            const dueDate = formatDate(item.visit_order_due_date);

            const statusLabel = getStatusLabel(item.visit_order_status);

            const card = `
                <div class="col-md-6 col-lg-4 mb-4">
                  <a id="detailClick" class="dropdown-item" data-id="${item.visit_order_id}" href="javascript:void(0);">
                    <div class="card h-100">
                      <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">${item.visit_order_number}</h5>
                        <h6 class="mb-0">${statusLabel}</h6>
                      </div>
                      <div class="card-body">
                        <ul class="p-0 m-0">
                          <li class="d-flex mb-4 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <small class="text-muted d-block mb-1">Order Date :</small>
                                <h6 class="mb-0">${formattedDate}</h6>
                              </div>
                            </div>
                          </li>
                          <li class="d-flex mb-4 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <small class="text-muted d-block mb-1">Due Date :</small>
                                <h6 class="mb-0">${dueDate}</h6>
                              </div>
                            </div>
                          </li>
                          <li class="d-flex mb-4 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <small class="text-muted d-block mb-1">Site Name :</small>
                                <h6 class="mb-0">${item.site.site_name}</h6>
                              </div>
                            </div>
                          </li>
                          <li class="d-flex mb-4 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <small class="text-muted d-block mb-1">Debtor :</small>
                                <h6 class="mb-0">${item.debtor.debtor_name}</h6>
                              </div>
                            </div>
                          </li>
                          <li class="d-flex mb-4 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <small class="text-muted d-block mb-1">Location :</small>
                                <h6 class="mb-0" style="white-space: pre-line">${item.visit_order_location}</h6>
                              </div>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </a>
                </div>
              `;
            // Append the card to the container
            cardContainer.innerHTML += card;
          });

          var pagination_links = '';
          var prevLink = '';
          var nextLink = '';

          $.each(response.data.links, function (index, link) {
            if (link.label === '&laquo; Previous') {
              prevLink = '<li class="page-item prev ' + (link.url ? '' : 'disabled') + '"><a class="page-link" href="' + (link.url || '#') + '">' + link.label + '</a></li>';
            } else if (link.label === 'Next &raquo;') {
              nextLink = '<li class="page-item next ' + (link.url ? '' : 'disabled') + '"><a class="page-link" href="' + (link.url || '#') + '">' + link.label + '</a></li>';
            } else {
              var active = link.active ? 'active' : '';
              pagination_links += '<li class="page-item ' + active + '"><a class="page-link" href="' + link.url + '">' + link.label + '</a></li>';
            }
          });

          // tambahkan tombol "Previous" dan "Next" ke dalam pagination
          $('.pagination').append(prevLink);
          $('.pagination').append(nextLink);
          // kosongkan terlebih dahulu data di paginate yang terpilih contoh pagination 1
          $('.pagination').empty();

          var pagination = '<ul class="pagination">' + prevLink + pagination_links + nextLink + '</ul>';
          $('.pagination').html(pagination);
        }
      },
    });
  }

  // Fungsi untuk mengambil data checklist dari response
  function getChecklist(response) {
    var checklistGroups = response.checklist_groups;
    var checklists = response.checklists;
    var checklistAnswers = response.checklist_answers;

    // Bersihkan konten yang ada sebelumnya coy
    var groupContainer = document.getElementById('checkboxContainer');
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

});
