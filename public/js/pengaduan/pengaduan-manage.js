/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table = $('.datatables-ajax');
  var modal_change_status = $('#modalChangeStatus');
  var modal_detail_pengaduan = $('#modalDetailPengaduan');
  var modal_edit_pengaduan = $('#modalEditPengaduan');
  var modal_class_loader = $('.modal-block-loader');

  var pengaduan_id_global;
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
  const textarea = document.querySelector('#autosize-note');
  const answer = document.querySelector('#autosize-answer');

  // Autosize
  // --------------------------------------------------------------------
  if (textarea) {
    autosize(textarea);
  }

  if (answer) {
    autosize(answer);
  }

  // Data Table
  if (dt_ajax_table.length) {
    var dt_ajax = dt_ajax_table.DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      initComplete: onInit,
      ajax: {
        url: baseUrl + 'pengaduan/manage/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'pengaduan/manage/get');
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
        { data: 'pengaduan_number', orderable: false },
        {
          data: 'pengaduan_note',
          render: function (data, type, row) {
            if (data) {
              var expandedNote = row.expandedNote ? row.expandedNote : false;

              if (!expandedNote) {
                var shortDescNote = data.length > 25 ? data.substr(0, 25) + '...' : data;
                var showMoreHtml = data.length > 25 ? '<a href="javascript:void(0);" class="show-more-note">Show More</a>' : '';
                return '<div style="white-space: pre-wrap;" class="short-desc-note">' + shortDescNote + '</div>' + showMoreHtml;
              } else {
                return '<div style="white-space: pre-wrap;" class="full-desc-note">' + data + '</div><a href="javascript:void(0);" class="show-less-note">Show Less</a>';
              }
            } else {
              return '-';
            }
          }
        },
        { data: 'role_name' },
        { data: 'pengaduan_created_by' },
        {
          data: 'pengaduan_answer',
          render: function (data, type, row) {
            if (data) {
              var expandedAnswer = row.expandedAnswer ? row.expandedAnswer : false;

              if (!expandedAnswer) {
                var shortDescAnswer = data.length > 15 ? data.substr(0, 15) + '...' : data;
                var showMoreHtml = data.length > 15 ? '<a href="javascript:void(0);" class="show-more-answer">Show More</a>' : '';
                return '<div style="white-space: pre-wrap;" class="short-desc-answer">' + shortDescAnswer + '</div>' + showMoreHtml;
              } else {
                return '<div style="white-space: pre-wrap;" class="full-desc-answer">' + data + '</div><a href="javascript:void(0);" class="show-less-answer">Show Less</a>';
              }
            } else {
              return '-';
            }
          }
        },
        {
          data: 'pengaduan_status',
          render: function (data, type, row) {
            return getStatusLabel(data, row.role_id);
          }
        },
        // {
        //     data: 'custom_message',
        //     render: function (data, type, row) {
        //         if (data) {
        //             return data;
        //         } else {
        //             // Display the original pengaduan_status
        //             return getStatusLabel(row.pengaduan_status);
        //         }
        //     }
        // }
      ],
      columnDefs: [
        {
          targets: 6,
          searchable: false,
          render: function (data, type, row) {
              if (data) {
                  return data;
              } else {
                  // Display the original pengaduan_status
                  return getStatusLabel(row.pengaduan_status);
              }
          }
        },
        {
          targets: 7,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
            try {
                var answerButton = '';
                var editButton = '';
                var deleteButton = '';

                if (row.role_id === 1 && !row.pengaduan_answer) {
                  answerButton =  '<button id="btn_answer" class="btn btn-sm btn-icon btn-primary mx-1" data-id="' + row.pengaduan_id + '" data-role_id="' + row.role_id + '" data-name="' + row.user_name + '"  data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="Answer ' + row.role_name + '"><i class="bx bx-chat me-1"></i></button>';
                }

                if (row.role_id >= 2 && row.role_id <= 4) {
                    deleteButton = '<button id="btn_delete" class="btn btn-sm btn-icon btn-danger mx-1" data-id="' + row.pengaduan_id + '" data-role_id="' + row.role_id + '" data-name="' + row.user_name + '"  data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="Delete"><i class="bx bx-trash me-1"></i></button>';
                    // Menampilkan tombol Edit jika pengaduan_answer adalah NULL (kosong) dan pengaduan_status adalah 1
                    if (!row.pengaduan_answer && row.pengaduan_status == 1) {
                        editButton = '<a id="dropdownMenuEdit" data-id="' + row.pengaduan_id + '" data-role_id="' + row.role_id + '" data-name="' + row.user_name + '"  class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>';
                    }
                    
                }
      
                return (
                  '<div class="d-inline-block text-nowrap">' +
                  deleteButton +
                  answerButton +
                  '<button class="btn btn-sm btn-icon btn-info dropdown-toggle hide-arrow mx-1" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                  '<div class="dropdown-menu">' +
                  '<a id="dropdownMenuDetail" data-id="' + row.pengaduan_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-detail me-1"></i> Detail</a>' +
                  '<div class="dropdown-divider"></div>' +
                  editButton +
                  '</div>' +
                  '</div>'
              );
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
      dom: '<"row"<"col-sm-12 col-md-6"l>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      drawCallback: function (settings) {
          // Cek jumlah data yang ditampilkan setelah DataTable digambar
          var api = this.api();
          var rowCount = api.rows().count();
  
          if (rowCount === 0) {
              // Sembunyikan elemen pagination jika tidak ada data
              $('.dataTables_paginate').hide();
          } else {
              // Tampilkan elemen pagination jika ada data
              $('.dataTables_paginate').show();
          }
      }
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

  $(document).on('click', '.show-more-note', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $shortDescnote = $this.prev('.short-desc-note');
    var $fullDescNote = $shortDescnote.next('.full-desc-note');
    $shortDescnote.hide();
    $fullDescNote.show();
    $this.text('Show Less');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedNote = true; // Menandai bahwa deskripsi telah di-expand
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-less-note', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $fullDescNote = $this.prev('.full-desc-note');
    var $shortDescnote = $fullDescNote.prev('.short-desc-note');
    $fullDescNote.hide();
    $shortDescnote.show();
    $this.text('Show More');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedNote = false; // Menandai bahwa deskripsi telah di-collapse
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-more-answer', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $shortDescAnswer = $this.prev('.short-desc-answer');
    var $fullDescAnswer = $shortDescAnswer.next('.full-desc-answer');
    $shortDescAnswer.hide();
    $fullDescAnswer.show();
    $this.text('Show Less');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedAnswer = true; // Menandai bahwa deskripsi telah di-expand
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-less-answer', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $fullDescAnswer = $this.prev('.full-desc-answer');
    var $shortDescAnswer = $fullDescAnswer.prev('.short-desc-answer');
    $fullDescAnswer.hide();
    $shortDescAnswer.show();
    $this.text('Show More');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedAnswer = false; // Menandai bahwa deskripsi telah di-collapse
    dt_ajax.row($this.closest('tr')).data(row);
  });

  var editPengaduanForm = document.getElementById('formEditPengaduan');
  // edit visit order form validation
  var fvEdit = FormValidation.formValidation(editPengaduanForm, {
    fields: {
      pengaduan_note: {
        validators: {
          notEmpty: {
            message: 'Please enter pengaduan'
          }
        }
      },
      pengaduan_answer: {
        validators: {
          notEmpty: {
            message: 'Please enter jawaban'
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
      // Submit the form when all fields are valid
      // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  }).on('core.form.valid', function () {

    var pengaduan_id = $('#formEditPengaduan').attr('data-id'); 
    var role_id = $('#formEditPengaduan').attr('data-role_id'); 
    // console.log(role_id);
    var form_data = {};
    
    if ($('#formEditPengaduan').data('method') == 'edit') {
      var url = "pengaduan/manage/update/" + pengaduan_id;
      
      form_data.pengaduan_number = $('#pengaduan_number').val();

      if (role_id == 1) {
        form_data.pengaduan_answer = $('#autosize-answer').val();
      } else {
        form_data.pengaduan_note = $('#autosize-note').val();
      }
    } else {
      var url = "";
    }

    $.ajax({
      data: form_data,
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax.draw();
        modal_edit_pengaduan.modal('hide');
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
        modal_edit_pengaduan.modal('hide');
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

  // Edit button handler
  $(document).on('click', '#dropdownMenuEdit', function () {
    var pengaduan_id = $(this).data('id');
    var role_id = $(this).data('role_id'); 
    var user_name = $(this).data('name');

    // get data
    $.ajax({
      url: baseUrl + "pengaduan/manage/show/" + pengaduan_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {
        $('#pengaduan_number').val(response.data.pengaduan_number);
        
        if (role_id == 1) {
            $('#autosize-answer').val(response.data.pengaduan_answer);
            $('#autosize-note').val(response.data.pengaduan_note);
        } else {
            $('#autosize-note').val(response.data.pengaduan_note);
            $('#autosize-answer').val(''); // Kosongkan answer field
        }
        
        modal_class_loader.unblock();
      }
    });

    if (role_id == 1) {
      $('#editFormLabel > p').html('Edit answer.');
      $('#editFormLabel > h3').html('Edit answer.');
      $('#editUraian > h5').html('Edit Uraian Jawaban anda <b>' + user_name + '</b>');
    } else {
      $('#editFormLabel > p').html('Edit pengaduan.');
      $('#editFormLabel > h3').html('Edit pengaduan.');
    }
    $('#formEditPengaduan').attr('data-method', 'edit');
    $('#formEditPengaduan').data('method', 'edit');
    $('#formEditPengaduan').attr('data-id', pengaduan_id);
    $('#formEditPengaduan').attr('data-role_id', role_id);
    modal_edit_pengaduan.modal('show');
  });

   // Edit button handler
   $(document).on('click', '#btn_answer', function () {
    var pengaduan_id = $(this).data('id');
    var role_id = $(this).data('role_id'); 
    var user_name = $(this).data('name');
    // console.log(role_id);

    // get data
    $.ajax({
      url: baseUrl + "pengaduan/manage/show/" + pengaduan_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {
        $('#pengaduan_number').val(response.data.pengaduan_number);
        
        if (role_id === 1) {
            $('#autosize-answer').val(response.data.pengaduan_answer);
            $('#autosize-note').val(response.data.pengaduan_note);
        }
        
        modal_class_loader.unblock();
      }
    });

    if (role_id === 1) {
      $('#editFormLabel > p').html('Edit answer.');
      $('#editFormLabel > h3').html('Edit answer.');
      $('#editUraian > h5').html('Edit Uraian Jawaban anda <b>' + user_name + '</b>');
    } 
    $('#formEditPengaduan').attr('data-method', 'edit');
    $('#formEditPengaduan').data('method', 'edit');
    $('#formEditPengaduan').attr('data-id', pengaduan_id);
    $('#formEditPengaduan').attr('data-role_id', role_id);
    modal_edit_pengaduan.modal('show');
  });

  // Delete status visit order
  $(document).on('click', '#btn_delete', function () {
    var pengaduan_id = $(this).data('id');

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
          data: { pengaduan_id: pengaduan_id, pengaduan_status: 5 },
          type: 'POST',
          url: baseUrl + 'pengaduan/manage/delete',
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

  $(document).on('click', '#dropdownMenuDetail', function () {
    var pengaduan_id = $(this).data('id');
    pengaduan_id_global = $(this).data('id');

    // Fungsi untuk Detail order visit
    // get data
    $.ajax({
      url: baseUrl + "pengaduan/manage/show/" + pengaduan_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {

        $('#det_number').text(response.data.pengaduan_number);
        $('#det_note').text(response.data.pengaduan_note ? response.data.pengaduan_note : '-');
        $('#det_answer').text(response.data.pengaduan_answer ? response.data.pengaduan_answer : '-');

        getTimelineData(response.data.history);

        modal_class_loader.unblock();
      }
    });

    modal_detail_pengaduan.modal('show');
  });

  // location-maxlength & repeater (jquery)
  $(function () {
    var maxlengthInput = $('.answer-maxlength');

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

  // Mengambil data untuk timeline
  function getTimelineData(response) {
    var timeline_data = response;

    // Bersihkan konten yang ada sebelumnya
    var historyContainer = document.getElementById('history_pengaduan');
    historyContainer.innerHTML = '';

    timeline_data.forEach(function (timeline) {
      var stats = getStatusDot(timeline.pengaduan_status);

       // Mengecek apakah timeline.pengaduan_history_desc memiliki nilai, jika tidak, ganti dengan "-"
       var statsDesc = timeline.pengaduan_history_desc ? timeline.pengaduan_history_desc : "-";
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
                  ${statsDesc}
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
        return ['timeline-point-info', 'Terkirim'];
      case 2:
        return ['timeline-point-success', 'Terjawab & Selesai'];
      case 3:
        return ['timeline-point-danger', 'Deleted'];
      default:
        return ['timeline-point-danger', 'Unknown'];
    }
  }

  function getStatusLabel(status, role_id) {
    switch (status) {
      case 1:
        if (role_id === 1) {
          return '<span class="badge bg-label-info me-1">Pesan Baru</span>';
        } else {
          return '<span class="badge bg-label-success me-1">Pesan Terkirim</span>';
        }
      case 2:
        return '<span class="badge bg-label-success me-1">Terjawab & Selesai</span>';
      case 5:
        return '<span class="badge bg-label-danger me-1">Deleted</span>';
      default:
        return '<span class="badge bg-label-danger me-1">Unknown</span>';
    }
  }
  
  

  // function getStatusLabel(status) {

  //   console.log("getStatusLabel called with status:", status);
  //   switch (status) {
  //     case 1:
  //       return '<span class="badge bg-label-success me-1">Terkirim</span>';
  //     case 2:
  //       return '<span class="badge bg-label-info me-1">Terjawab & Selesai</span>';
  //     case 5:
  //       return '<span class="badge bg-label-danger me-1">Deleted</span>';
  //     default:
  //       return '<span class="badge bg-label-danger me-1">Unknown</span>';
  //   }
  // }


});
