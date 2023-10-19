/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table = $('.datatables-ajax');
  var modal_add_caleg = $('#modalAddCaleg');
  var modal_edit_caleg = $('#modalEditCaleg');
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
        url: baseUrl + 'pemilu/master-data/caleg/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'pemilu/master-data/caleg/get');
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
        { data: 'caleg_nik' },
        { data: 'caleg_name' },
        {
          data: 'caleg_visi_misi',
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
        { data: 'caleg_no_urut_partai', orderable: false },
        { data: 'caleg_nama_partai', orderable: false },
        { data: 'caleg_no_urut_caleg', orderable: false },
        {
          data: 'caleg_photo',
          render: function(data, type, row, meta) {
            if (data !== "" && data !== null && data !== undefined) {
              var images = '';
              var caleg_files = data.split(',');
              for (var i = 0; i < caleg_files.length; i++) {
                var url = baseUrl + 'pemilu/master-data/caleg/uploads/' + row.caleg_id;
                url = url.replace(':filename', caleg_files[i]);
                images += '<img src="' + url + '" width="50px" height="50px" class="rounded-circle" />';
              }
              return images;
            } else {
              return 'No Photo';
            }
          },
          orderable: false
        },
        {
          data: 'caleg_photo_partai',
          render: function(data, type, row, meta) {
            if (data !== "" && data !== null && data !== undefined) {
              var images = '';
              var caleg_files = data.split(',');
              for (var i = 0; i < caleg_files.length; i++) {
                var url = baseUrl + 'pemilu/master-data/caleg/upload-partai/' + row.caleg_id;
                url = url.replace(':filename', caleg_files[i]);
                images += '<img src="' + url + '" width="50px" height="50px" class="rounded-circle" />';
              }
              return images;
            } else {
              return 'No Photo';
            }
          },
          orderable: false
        },
        { data: 'caleg_status', orderable: false }
      ],
      columnDefs: [
        {
          targets: 9,
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
          targets: 10,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
              return '' +
              '<div class="d-inline-block text-nowrap">' +
                  '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                  '<div class="dropdown-menu">' +
                      '<a id="dropdownMenuEdit" data-id="' + row.caleg_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>' +
                      '<a id="dropdownMenuActivate" data-id="' + row.caleg_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
                      '<a id="dropdownMenuDeactivate" data-id="' + row.caleg_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Deactivate</a>' +
                      '<a id="dropdownMenuDelete" data-id="' + row.caleg_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
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
  var add_caleg_form = document.getElementById('formAddCaleg');

  // Caleg Form Validation
  var fv = FormValidation.formValidation(add_caleg_form, {
    fields: {
      caleg_name: {
        validators: {
          notEmpty: {
            message: 'Please enter name'
          }
        }
      },
      caleg_nik: {
        validators: {
            notEmpty: {
              message: 'Please enter NIK'
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
      caleg_visi_misi: {
          validators: {
          notEmpty: {
              message: 'Please enter visi & misi'
          }
        }
      },
      caleg_nama_partai: {
          validators: {
          notEmpty: {
              message: 'Please enter nama partai'
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
    // Adding caleg when form successfully validate
    if ($('#formAddCaleg').data('method') == 'add') {
      var url = "pemilu/master-data/caleg/store";
    } else {
      var url = "";
    }

    var form_data = new FormData(add_caleg_form); 
    $.ajax({
      data: form_data,
      url: baseUrl + url,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function success(response) {
        dt_ajax.draw();
        modal_add_caleg.modal('hide');

        if (response.status) {
          Swal.fire({
            icon: 'success',
            title: response.message.title,
            text: response.message.text,
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });

          var checkedKecamatan = response.data.kecmatan_id;
          console.log(checkedKecamatan);
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
        modal_add_caleg.modal('hide');
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

  var editCalegForm = document.getElementById('formEditCaleg');
  // edit caleg form validation
  var fvEdit = FormValidation.formValidation(editCalegForm, {
      fields: {
      caleg_name: {
        validators: {
          notEmpty: {
            message: 'Please enter name'
          }
        }
      },
      caleg_nik: {
        validators: {
            notEmpty: {
              message: 'Please enter NIK'
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
      caleg_visi_misi: {
          validators: {
          notEmpty: {
              message: 'Please enter visi & misi'
          }
        }
      },
      caleg_nama_partai: {
          validators: {
          notEmpty: {
              message: 'Please enter nama partai'
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

    if ($('#formEditCaleg').data('method') == 'edit') {
      var url = "pemilu/master-data/caleg/update/" + $('#formEditCaleg').attr('data-id');
    } else {
      var url = "";
    }
    var form_data = new FormData(editCalegForm); 

    $.ajax({
      data: form_data,
      url: baseUrl + url,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function success(response) {
        dt_ajax.draw();
        modal_edit_caleg.modal('hide');
        if (response.status) {
          Swal.fire({
            icon: 'success',
            title: response.message.title,
            text: response.message.text,
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });

          var checkedKecamatan = response.data.kecmatan_id;
          console.log(checkedKecamatan);
          $('.checkbox-item-modal-edit-kecamatan').prop('checked', false); // Deselect all checkboxes
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
        modal_edit_caleg.modal('hide');
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
    var caleg_id = $(this).data('id');
    var tmp_id;

    // get data
    $.ajax({
      url: baseUrl + "pemilu/master-data/caleg/show/" + caleg_id,
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {
        if (response.data.caleg_status == 2) {
          $('#editStatus').prop('checked', true);
        } else {
          $('#editStatus').prop('checked', false);
        }

        $('#editName').val(response.data.caleg_name);
        $('#editNIK').val(response.data.caleg_nik);

        $.each(response.data.detail, function (index, val) {
          tmp_id = val.kecamatan_id;
          $('#edit_kecamatan_type_' + tmp_id).prop('checked', true);
        })

        // Display current photo caleg
        if (response.data.caleg_photo) {
          var photoUrl = baseUrl + 'pemilu/master-data/caleg/uploads/' + caleg_id + '?' + Date.now();
          $('.current-photo').attr('src', photoUrl);
        }
        // Set value of oldImage input
        $('#oldImage').val(response.data.caleg_photo);

         // Display current photo partai
         if (response.data.caleg_photo_partai) {
          var photoUrl = baseUrl + 'pemilu/master-data/caleg_partai/uploads/' + caleg_id + '?' + Date.now();
          $('.current-photo-partai').attr('src', photoUrl);
        }
        // Set value of oldImage Partai input
        $('#oldImagePartai').val(response.data.caleg_photo_partai);

        $('#editVisiMisi').val(response.data.caleg_visi_misi);
        $('#editNamaPartai').val(response.data.caleg_nama_partai);
        modal_class_loader.unblock();
      }
    });

    $('#editFormLabel > p').html('Edit Caleg.');
    $('#formEditCaleg').attr('data-method', 'edit');
    $('#formEditCaleg').data('method', 'edit');
    $('#formEditCaleg').attr('data-id', caleg_id);
    modal_edit_caleg.modal('show');
  });
  
  // Active / Deactive status button handler
  $(document).on('click', '.dropdownMenuStatusUpdate', function () {
    var caleg_id = $(this).data('id'),
      caleg_status = $(this).data('status');

    if (caleg_status == 2) {
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
          data: {caleg_id: caleg_id, caleg_status: caleg_status},
          type: 'POST',
          url: baseUrl + 'pemilu/master-data/caleg/update-status',
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
    var caleg_id = $(this).data('id');

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
          data: {caleg_id: caleg_id},
          type: 'POST',
          url: baseUrl + 'pemilu/master-data/caleg/delete',
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
  modal_add_caleg.on('hidden.bs.modal', function () {
    $('#addFormLabel > p').html('Add new caleg.');
    $('#formAddCaleg').attr('data-method', 'add');
    $('#formAddCaleg').data('method', 'add');

    $('#addProvince').val('').trigger('change');
    $('#addRegency').val('').trigger('change');
    $('#addDistrict').val('').trigger('change');
    $('#addVillage').val('').trigger('change');
    fv.resetForm(true);
  });

  modal_edit_caleg.on('hidden.bs.modal', function () {
    // fvEdit.resetForm(true);
    $('#caleg_photo').val(null);
    $('#imagePreview').empty();
    $('#imagePreview').html('<img src="#" class="img-fluid" style="max-width: 100%; height: auto;">');
    $('#imagePreview').css("background-image", "none");
  });

  // Image Preview
  $(function() {
      $("#caleg_photo").on("change", function()
      {
          var files = !!this.files ? this.files : [];
          if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
          
          if (/^image/.test( files[0].type)){ // only image file
              var reader = new FileReader(); // instance of the FileReader
              reader.readAsDataURL(files[0]); // read the local file
              
              reader.onloadend = function(){ // set image data as background of div
                  $("#imagePreview").css("background-image", "url("+this.result+")");
              }
          }
      });
  });

  // Get the checkbox
  var checkboxVisual = document.getElementById("checkbox");
  
  // Get the close button
  var closeVisual = document.getElementsByClassName("btn-close")[0];

  // When the user clicks on the close button, clear the checkbox
  closeVisual.onclick = function() {
    modal_add_caleg.style.display = "none";
    checkboxVisual.checked = false;
  }

  // When the user clicks anywhere outside of the modal, close it and clear the checkbox
  window.onclick = function(event) {
    if (event.target == modal_add_caleg) {
      modal_add_caleg.style.display = "none";
      checkboxVisual.checked = false;
    }
  }

});
