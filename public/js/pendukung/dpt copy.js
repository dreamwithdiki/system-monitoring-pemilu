/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN         = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table      = $('.datatables-ajax');
  var modal_add_dpt      = $('#modalAddDpt');
  var modal_edit_dpt     = $('#modalEditDpt');
  var modal_detail_dpt   = $('#modalDetailDpt');
  var ac_tps             = $('.ac_tps');
  var ac_edit_tps        = $('.ac_edit_tps');
  var modal_class_loader = $('.modal-block-loader');
  var typingTimer;
  
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  });

   // Select2 tps handler
   if (ac_tps.length) {
    var $this = ac_tps;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select TPS',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/tps/find',
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
        url: baseUrl + 'pendukung/dpt/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'pendukung/dpt/get');
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
        { data: 'dpt_nik' },
        { data: 'dpt_name' },
        { data: 'dpt_jenkel', 
          render: function (data, type, row, meta) {
              if (data == 1) {
                  return '<span class="badge bg-primary">Laki-Laki</span>';
              } else if (data == 2) {
                  return '<span class="badge bg-success">Perempuan</span>';
              } else {
                  return '<span class="badge bg-danger">Unknown</span>';
              }
          },
          orderable: false
        },
        { data: 'tps_name' },
        { data: 'dpt_status', orderable: false }
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
                      '<a id="dropdownMenuEdit" data-id="' + row.dpt_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>' +
                      '<a id="dropdownMenuDetail" data-id="' + row.dpt_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-detail me-1"></i> Detail</a>' +
                      '<a id="dropdownMenuActivate" data-id="' + row.dpt_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
                      '<a id="dropdownMenuDeactivate" data-id="' + row.dpt_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Deactivate</a>' +
                      '<a id="dropdownMenuDelete" data-id="' + row.dpt_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
                  '</div>' +
              '</div>';
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
  var add_dpt_form = document.getElementById('formAddDpt');

  // Caleg Form Validation
  var fv = FormValidation.formValidation(add_dpt_form, {
    fields: {
      dpt_nik: {
        validators: {
          notEmpty: {
            message: 'Please enter NIK'
          }
        }
      },
      dpt_name: {
        validators: {
          notEmpty: {
            message: 'Please enter name'
          }
        }
      },
      'kecamatan_type[]': {
          validators: {
              notEmpty: {
                  message: 'Please select checklist kecamatan'
              }
          }
      },
      tps_id: {
          validators: {
          notEmpty: {
              message: 'Please select TPS'
          }
        }
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
    // Adding tps when form successfully validate
    if ($('#formAddDpt').data('method') == 'add') {
      var url = "pendukung/dpt/store";
    } else {
      var url = "";
    }

    $.ajax({
      data: $('#formAddDpt').serialize(),
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax.draw();
        modal_add_dpt.modal('hide');

        if (response.status) {
          Swal.fire({
            icon: 'success',
            title: response.message.title,
            text: response.message.text,
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });

          var checkedKecamatan = response.data.kecamatan_id;
          $('.checkbox-item-modal-add-kecamatan').prop('checked', false); // Deselect all checkboxes
          for (var i = 0; i < checkedKecamatan.length; i++) {
              var checkId = checkedKecamatan[i];
              $('#kecamatan_type_' + checkId).prop('checked', true); // Check checkboxes based on saved data
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
        modal_add_dpt.modal('hide');
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

  var editDptForm = document.getElementById('formEditDpt');
  // edit caleg form validation
  var fvEdit = FormValidation.formValidation(editDptForm, {
    fields: {
      dpt_nik: {
        validators: {
          notEmpty: {
            message: 'Please enter NIK'
          }
        }
      },
      dpt_name: {
        validators: {
          notEmpty: {
            message: 'Please enter name'
          }
        }
      },
      'edit_kecamatan_type[]': {
          validators: {
              notEmpty: {
                  message: 'Please select checklist kecamatan'
              }
          }
      },
      tps_id: {
          validators: {
          notEmpty: {
              message: 'Please select TPS'
          }
        }
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

    if ($('#formEditDpt').data('method') == 'edit') {
      var url = "pendukung/dpt/update/" + $('#formEditDpt').attr('data-id');
    } else {
      var url = "";
    }

    $.ajax({
      data: $('#formEditDpt').serialize(),
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax.draw();
        modal_edit_dpt.modal('hide');
        if (response.status) {
          Swal.fire({
            icon: 'success',
            title: response.message.title,
            text: response.message.text,
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });

          var checkedKecamatan = response.data.kecamatan_id;
          $('.checkbox-item-modal-edit-kecamatan').prop('checked', false); // Deselect all checkboxes
          for (var i = 0; i < checkedKecamatan.length; i++) {
              var checkId = checkedKecamatan[i];
              $('#edit_kecamatan_type_' + checkId).prop('checked', true); // Check checkboxes based on saved data
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
        modal_edit_dpt.modal('hide');
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
  
  // Function to set the selected dpt_jenkel value to localStorage
  function setSelectedClientParam(dpt_jenkel) {
    localStorage.setItem('selected_dpt_jenkel', dpt_jenkel);
  }

  // Function to get the selected dpt_jenkel value from localStorage
  function getSelectedClientParam() {
    return localStorage.getItem('selected_dpt_jenkel');
  }

  // Edit button handler
  $(document).on('click', '#dropdownMenuEdit', function () {
    var dpt_id = $(this).data('id');
    var tmp_id;

    // get data
    $.ajax({
      url: baseUrl + "pendukung/dpt/show/" + dpt_id,
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {
        if (response.data.dpt_status == 2) {
          $('#editStatus').prop('checked', true);
        } else {
          $('#editStatus').prop('checked', false);
        }

        $('#editNik').val(response.data.dpt_nik);
        $('#editName').val(response.data.dpt_name);
        
        if (response.data.dpt_jenkel == 1) {
          $('input[name="dpt_jenkel"][value="1"]').prop('checked', true);
          setSelectedClientParam(1); // Store selected value to localStorage
        } else if (response.data.dpt_jenkel == 2) {
          $('input[name="dpt_jenkel"][value="2"]').prop('checked', true);
          setSelectedClientParam(2); // Store selected value to localStorage
        } else {
          // Default to Individu (2) if dpt_jenkel is neither 1 nor 2
          $('input[name="dpt_jenkel"][value="2"]').prop('checked', true);
          setSelectedClientParam(2); // Store selected value to localStorage
        }

        $.each(response.data.kecamatan_ceklis, function (index, val) {
          tmp_id = val.kecamatan_id;
          $('#edit_kecamatan_type_' + tmp_id).prop('checked', true);
        })

        if (response.data.tps) {
          var tpsLabel = response.data.tps.tps_code + " - " + response.data.tps.tps_name;
          var option = new Option(tpsLabel, response.data.tps.tps_id, true, true);
          $('#editTps').append(option).trigger('change');
        }       

        modal_class_loader.unblock();
      }
    });

    $('#editFormLabel > p').html('Edit DPT.');
    $('#formEditDpt').attr('data-method', 'edit');
    $('#formEditDpt').data('method', 'edit');
    $('#formEditDpt').attr('data-id', dpt_id);
    modal_edit_dpt.modal('show');
  });

  // Restore the selected dpt_jenkel value from localStorage on page load
  $(document).ready(function() {
    var selectedClientParam = getSelectedClientParam();
    if (selectedClientParam == 1) {
      $('input[name="dpt_jenkel"][value="1"]').prop('checked', true);
    } else if (selectedClientParam == 2) {
      $('input[name="dpt_jenkel"][value="2"]').prop('checked', true);
    }
  });

  $(document).on('click', '#dropdownMenuDetail', function () {
    var dpt_id = $(this).data('id');
    // Fungsi untuk Detail dpt
    // get data
    $.ajax({
      url: baseUrl + "pendukung/dpt/show/" + dpt_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {

        $('#det_nik').text(response.data.dpt_nik);
        $('#det_nama').text(response.data.dpt_name);
        $('#det_jenkel').text(response.data.dpt_jenkel == 1 ? 'Laki-Laki' : 'Perempuan');
        $('#det_tps').text(response.data.tps.tps_code + " - " + response.data.tps.tps_name);
        $('#det_status').text(response.data.dpt_status == 1 ? 'Deactive' : 'Active');

        let kec = '';
        if (response.data.kecamatan_ceklis && response.data.kecamatan_ceklis.length > 0) {
          $.each(response.data.kecamatan_ceklis, function (index, val) {
            kec += "-" + val.checklist_kec.kecamatan_name + "<br>";
          });
        } else {
          kec = "No kecamatan.";
        }
        $('#det_kecamatan').html(kec);
                
        modal_class_loader.unblock();
      }
    });
    $('#detFormLabel > p').html('Detail DPT.');
    $('#formDetailDpt').attr('data-method', 'detail');
    $('#formDetailDpt').data('method', 'detail');
    $('#formDetailDpt').attr('data-id', dpt_id);
    modal_detail_dpt.modal('show');
  });
  
  // Active / Deactive status button handler
  $(document).on('click', '.dropdownMenuStatusUpdate', function () {
    var dpt_id = $(this).data('id'),
      dpt_status = $(this).data('status');

    if (dpt_status == 2) {
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
          data: {dpt_id: dpt_id, dpt_status: dpt_status},
          type: 'POST',
          url: baseUrl + 'pendukung/dpt/update-status',
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
    var dpt_id = $(this).data('id');

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
          data: {dpt_id: dpt_id},
          type: 'POST',
          url: baseUrl + 'pendukung/dpt/delete',
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
  modal_add_dpt.on('hidden.bs.modal', function () {
    $('#addFormLabel > p').html('Add new DPT.');
    $('#formAddDpt').attr('data-method', 'add');
    $('#formAddDpt').data('method', 'add');
    fv.resetForm(true);
  });

  modal_edit_dpt.on('hidden.bs.modal', function () {
    fvEdit.resetForm(true);
  });

  // Select2 edit tps handler
  if (ac_edit_tps.length) {
    var $this = ac_edit_tps;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select TPS',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/tps/find',
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

});
