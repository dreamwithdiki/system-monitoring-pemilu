/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN         = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table      = $('.datatables-ajax');
  var modal_add_tps      = $('#modalAddTps');
  var modal_edit_tps     = $('#modalEditTps');
  var ac_role            = $('.ac_role');
  var ac_edit_role       = $('.ac_edit_role');
  var modal_class_loader = $('.modal-block-loader');
  var typingTimer;
  
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  });

   // Select2 role handler
   if (ac_role.length) {
    var $this = ac_role;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select saksi',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/role/find-saksi',
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

  // Mengirim permintaan Ajax untuk mendapatkan data Province
  $.ajax({
    url: baseUrl + 'pemilu/master-data/tps/get-provinces',
    type: 'GET',
    dataType: 'json',
    success: function(response) {
      if (response.status) {
        $('#addProvince').append('<option value="">Choice</option>');
        
        var provinces = response.data;
  
        $.each(provinces, function(index, province) {
          $('#addProvince').append('<option value="' + province.id + '">' + province.name + '</option>');
        });
  
        // Memperbarui tampilan Select2 pada dropdown Province
        $('#addProvince').select2({
            placeholder: 'Choice',
            minimumInputLength: 0,
            dropdownParent: $('#addProvince').parent()
        });
        $('#addRegency').select2({
          disabled: true,
        });
        $('#addDistrict').select2({
          disabled: true,
        });
        $('#addVillage').select2({
          disabled: true,
        });
      } else {
        $('#addProvince').append('<option value="">' + response.data + '</option>');
        $('#addRegency').select2({
          disabled: true,
        });
        $('#addDistrict').select2({
          disabled: true,
        });
        $('#addVillage').select2({
          disabled: true,
        });
      }
    }
  });

  // Mengubah opsi pilihan pada dropdown Regency berdasarkan Province yang dipilih
  $('#addProvince').on('change', function(){
    var provinceId = $(this).val();

    // Jika Province tidak dipilih, kosongkan juga dropdown District dan Village
    if (provinceId === '') {
      $('#addRegency').empty();
      $('#addDistrict').empty();
      $('#addVillage').empty();
      return;
    }

    // Mengirim permintaan Ajax untuk mendapatkan data Regency berdasarkan Province yang dipilih
    $.ajax({
      url: baseUrl + 'pemilu/master-data/tps/get-regencies',
      type: 'GET',
      data: { provinceId: provinceId },
      dataType: 'json',
      success: function(response) {
        if (response.status) {
          var regencies = response.data;

          // Simpan opsi Regency yang saat ini dipilih
          var currentRegency = $('#addRegency').val();

          // Hapus opsi lama pada dropdown Regency
          $('#addRegency').empty();

          $('#addRegency').append('<option value="">Choice</option>');
          $('#addDistrict').append('<option value="">Choice</option>');
          $('#addVillage').append('<option value="">Choice</option>');

          $.each(regencies, function(index, regency) {
            $('#addRegency').append('<option value="' + regency.id + '">' + regency.name + '</option>');
          });

          // Pilih kembali opsi Regency yang sebelumnya dipilih (jika ada)
          if (currentRegency) {
            $('#addRegency').val(currentRegency);
          }

          // Perbarui tampilan Select2 pada dropdown Regency
          $('#addRegency').select2({
            placeholder: 'Choice',
            minimumInputLength: 0,
            disabled: false,
            dropdownParent: $('#addRegency').parent()
          });

          $('#addRegency').trigger('change');
        } else {
          $('#addRegency').empty();
          $('#addDistrict').empty();
          $('#addVillage').empty();
          $('#addRegency').select2({
            disabled: true,
          });
          $('#addDistrict').select2({
            disabled: true,
          });
          $('#addVillage').select2({
            disabled: true,
          });
        }
      }
    });
  });

  // Mengubah opsi pilihan pada dropdown District berdasarkan Regency yang dipilih
  $('#addRegency').on('change', function() {
    var regencyId = $(this).val();

    // Jika Regency tidak dipilih, kosongkan juga dropdown Village
    if (regencyId === '') {
      $('#addDistrict').empty();
      $('#addVillage').empty();
      return;
    }

    // Mengirim permintaan Ajax untuk mendapatkan data District berdasarkan Regency yang dipilih
    $.ajax({
      url: baseUrl + 'pemilu/master-data/tps/get-districts',
      type: 'GET',
      data: { regencyId: regencyId },
      dataType: 'json',
      success: function (response) {
        if (response.status) {
          var districts = response.data;

          // Simpan opsi District yang saat ini dipilih
          var currentDistrict = $('#addDistrict').val();

          // Hapus opsi lama pada dropdown District
          $('#addDistrict').empty();

          $('#addDistrict').append('<option value="">Choice</option>');

          // Mengisi opsi pilihan pada dropdown District
          $.each(districts, function (index, district) {
            $('#addDistrict').append('<option value="' + district.id + '">' + district.name + '</option>');
          });

          // Pilih kembali opsi District yang sebelumnya dipilih (jika ada)
          if (currentDistrict) {
            $('#addDistrict').val(currentDistrict);
          }

          // Perbarui tampilan Select2 pada dropdown District
          $('#addDistrict').select2({
            placeholder: 'Choice',
            minimumInputLength: 0,
            disabled: false,
            dropdownParent: $('#addDistrict').parent()
          });

          $('#addDistrict').trigger('change');
        } else {
          $('#addDistrict').empty();
          $('#addVillage').empty();
          $('#addDistrict').select2({
            disabled: true,
          });
          $('#addVillage').select2({
            disabled: true,
          });
        }
      }
    });
  });

  // Mengubah opsi pilihan pada dropdown Village berdasrkan District yang dipilih
  $('#addDistrict').on('change', function() {
    var districtId = $(this).val();

    // Jika District tidak dipilih, kosongkan dropdown Village
    if (districtId === '') {
      $('#addVillage').empty();
      return;
    }

    // Mengirim permintaan Ajax untuk mendapatkan data Village berdasarkan District yang dipilih
    $.ajax({
      url: baseUrl + 'pemilu/master-data/tps/get-villages',
      type: 'GET',
      data: { districtId: districtId },
      dataType: 'json',
      success: function(response) {
        if (response.status) {
          var villages = response.data;

          // Simpan opsi District yang saat ini dipilih
          var currentVillage = $('#addVillage').val();

          // Hapus opsi lama pada dropdown Village
          $('#addVillage').empty();

          $.each(villages, function(index, village) {
            $('#addVillage').append('<option value="' + village.id + '">' + village.name + '</option>');
          });

          // Pilih kembali opsi Village yang sebelumnya dipilih (jika ada)
          if (currentVillage) {
            $('#addVillage').val(currentVillage);
          }

          // Perbarui tampilan Select2 pada dropdown Village
          $('#addVillage').select2({
            placeholder: 'Choice',
            disabled: false,
            minimumInputLength: 0,
            dropdownParent: $('#addVillage').parent()
          });

          $('#addVillage').trigger('change');
        } else {
          $('#addVillage').empty();
          $('#addVillage').select2({
            disabled: true,
          });
        }
      }
    });
  });


  // Data Table
  if (dt_ajax_table.length) {
    var dt_ajax = dt_ajax_table.DataTable({
      processing: true,
      serverSide: true,
      initComplete: onInit,
      ajax: {
        url: baseUrl + 'pemilu/master-data/tps/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'pemilu/master-data/tps/get');
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
        { data: 'tps_code' },
        { data: 'tps_name' },
        {
          data: 'tps_address',
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
        { data: 'tps_province' },
        { data: 'tps_regency' },
        { data: 'tps_district' },
        { data: 'tps_village' },
        { data: 'tps_suara_caleg' },
        { data: 'tps_suara_partai' },
        {
            data: 'tps_docs',
            render: function(data, type, row, meta) {
              if (data !== "" && data !== null && data !== undefined) {
                var images = '';
                var tps_files = data.split(',');
                for (var i = 0; i < tps_files.length; i++) {
                  var url = baseUrl + 'pemilu/master-data/tps/uploads/' + row.tps_id;
                  url = url.replace(':filename', tps_files[i]);
                  images += '<img src="' + url + '" width="50px" height="50px" class="rounded-circle" />';
                }
                return images;
              } else {
                return 'No Photo';
              }
            },
            orderable: false
          },
        { data: 'tps_status', orderable: false }
      ],
      columnDefs: [
        {
          targets: 11,
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
          targets: 12,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
              return '' +
              '<div class="d-inline-block text-nowrap">' +
                  '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                  '<div class="dropdown-menu">' +
                      '<a id="dropdownMenuEdit" data-id="' + row.tps_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>' +
                      '<a id="dropdownMenuActivate" data-id="' + row.tps_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
                      '<a id="dropdownMenuDeactivate" data-id="' + row.tps_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Deactivate</a>' +
                      '<a id="dropdownMenuDelete" data-id="' + row.tps_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
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
  var add_tps_form = document.getElementById('formAddTps');

  // Caleg Form Validation
  var fv = FormValidation.formValidation(add_tps_form, {
    fields: {
      tps_code: {
        validators: {
          notEmpty: {
            message: 'Please enter Code'
          }
        }
      },
      tps_name: {
        validators: {
          notEmpty: {
            message: 'Please enter name'
          }
        }
      },
      tps_address: {
        validators: {
            notEmpty: {
              message: 'Please enter Address'
            }
          }
      },
      tps_province: {
          validators: {
          notEmpty: {
              message: 'Please select province name'
          }
        }
      },
      tps_regency: {
          validators: {
          notEmpty: {
              message: 'Please select regency name'
          }
        }
      },
      role_id: {
          validators: {
          notEmpty: {
              message: 'Please select saksi'
          }
        }
      },
      tps_suara_caleg: {
          validators: {
          notEmpty: {
              message: 'Please enter suara caleg'
          }
        }
      },
      tps_suara_partai: {
          validators: {
          notEmpty: {
              message: 'Please enter suara partai'
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
    if ($('#formAddTps').data('method') == 'add') {
      var url = "pemilu/master-data/tps/store";
    } else {
      var url = "";
    }

    var form_data = new FormData(add_tps_form); 
    $.ajax({
      data: form_data,
      url: baseUrl + url,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function success(response) {
        dt_ajax.draw();
        modal_add_tps.modal('hide');

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
        modal_add_tps.modal('hide');
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

  var editTpsForm = document.getElementById('formEditTps');
  // edit caleg form validation
  var fvEdit = FormValidation.formValidation(editTpsForm, {
    fields: {
      tps_code: {
        validators: {
          notEmpty: {
            message: 'Please enter Code'
          }
        }
      },
      tps_name: {
        validators: {
          notEmpty: {
            message: 'Please enter name'
          }
        }
      },
      tps_address: {
        validators: {
            notEmpty: {
              message: 'Please enter Address'
            }
          }
      },
      tps_province: {
          validators: {
          notEmpty: {
              message: 'Please select province name'
          }
        }
      },
      tps_regency: {
          validators: {
          notEmpty: {
              message: 'Please select regency name'
          }
        }
      },
      role_id: {
          validators: {
          notEmpty: {
              message: 'Please select saksi'
          }
        }
      },
      tps_suara_caleg: {
          validators: {
          notEmpty: {
              message: 'Please enter suara caleg'
          }
        }
      },
      tps_suara_partai: {
          validators: {
          notEmpty: {
              message: 'Please enter suara partai'
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

    if ($('#formEditTps').data('method') == 'edit') {
      var url = "pemilu/master-data/tps/update/" + $('#formEditTps').attr('data-id');
    } else {
      var url = "";
    }
    var form_data = new FormData(editTpsForm); 

    $.ajax({
      data: form_data,
      url: baseUrl + url,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function success(response) {
        dt_ajax.draw();
        modal_edit_tps.modal('hide');
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
        modal_edit_tps.modal('hide');
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
    var tps_id = $(this).data('id');

    // get data
    $.ajax({
      url: baseUrl + "pemilu/master-data/tps/show/" + tps_id,
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {
        if (response.data.tps_status == 2) {
          $('#editStatus').prop('checked', true);
        } else {
          $('#editStatus').prop('checked', false);
        }

        $('#editCode').val(response.data.tps_code);
        $('#editTps').val(response.data.tps_name);
        $('#editAddress').val(response.data.tps_address);

        // Display current docs
        if (response.data.tps_docs) {
          var photoUrl = baseUrl + 'pemilu/master-data/tps/uploads/' + tps_id + '?' + Date.now();
          $('.current-photo').attr('src', photoUrl);
        }
        // Set value of oldImage input
        $('#oldImage').val(response.data.tps_docs);

        // Kosongkan dropdown "Province" sebelum menambahkan opsi-opsi baru
        $('#editProvince').empty();

        // Mendapatkan data provinces
        $.ajax({
          url: baseUrl + 'pemilu/master-data/tps/get-provinces',
          type: 'GET',
          dataType: 'json',
          success: function(provinceResponse) {
            if (provinceResponse.status) {
              var provinces = provinceResponse.data;
              
              // Menambahkan opsi-opsi baru pada dropdown "Province"
              $.each(provinces, function(index, province) {
                var option = new Option(province.name, province.id);
                $('#editProvince').append(option);
              });
              
              // Memperbarui tampilan Select2 pada dropdown "Province"
              $('#editProvince').select2({
                placeholder: 'Choice',
                minimumInputLength: 0,
                dropdownParent: $('#editProvince').parent()
              });

              // Memilih kembali opsi "Province" yang sebelumnya dipilih (jika ada)
              if (response.data.province) {
                $('#editProvince').val(response.data.province.id).trigger('change');
              }
            } else {
              $('#editProvince').append('<option value="">' + provinceResponse.data + '</option>');
              $('#editRegency').append('<option value="">' + provinceResponse.data + '</option>');
              $('#editDistrict').append('<option value="">' + provinceResponse.data + '</option>');
              $('#editVillage').append('<option value="">' + provinceResponse.data + '</option>');
            }
          }
        }); 

        if (response.data.regency) {
          var option = new Option(response.data.regency.name, response.data.regency.id, true, true);
          $('#editRegency').append(option).trigger('change');
        }
        
        if (response.data.district) {
          var option = new Option(response.data.district.name, response.data.district.id, true, true);
          $('#editDistrict').append(option).trigger('change');
        }

        if (response.data.village) {
          var option = new Option(response.data.village.name, response.data.village.id, true, true);
          $('#editVillage').append(option).trigger('change');
        }

        modal_class_loader.unblock();
      }
    });

    $('#editFormLabel > p').html('Edit Tps.');
    $('#formEditTps').attr('data-method', 'edit');
    $('#formEditTps').data('method', 'edit');
    $('#formEditTps').attr('data-id', tps_id);
    modal_edit_tps.modal('show');
  });
  
  // Active / Deactive status button handler
  $(document).on('click', '.dropdownMenuStatusUpdate', function () {
    var tps_id = $(this).data('id'),
      tps_status = $(this).data('status');

    if (tps_status == 2) {
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
          data: {tps_id: tps_id, tps_status: tps_status},
          type: 'POST',
          url: baseUrl + 'pemilu/master-data/tps/update-status',
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
    var tps_id = $(this).data('id');

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
          data: {tps_id: tps_id},
          type: 'POST',
          url: baseUrl + 'pemilu/master-data/tps/delete',
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
  modal_add_tps.on('hidden.bs.modal', function () {
    $('#addFormLabel > p').html('Add new tps.');
    $('#formAddTps').attr('data-method', 'add');
    $('#formAddTps').data('method', 'add');
    fv.resetForm(true);
  });

  modal_edit_tps.on('hidden.bs.modal', function () {
    // fvEdit.resetForm(true);
    $('#tps_docs').val(null);
    $('#imagePreview').empty();
    $('#imagePreview').html('<img src="#" class="img-fluid" style="max-width: 100%; height: auto;">');
    $('#imagePreview').css("background-image", "none");
  });

  // Image Preview
  $(function() {
      $("#tps_docs").on("change", function()
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

  // Select2 edit role handler
  if (ac_edit_role.length) {
    var $this = ac_edit_role;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select saksi',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/role/find-saksi',
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

  // Mengirim permintaan Ajax untuk mendapatkan data Province
  $.ajax({
    url: baseUrl + 'pemilu/master-data/tps/get-provinces',
    type: 'GET',
    dataType: 'json',
    success: function(response) {
      if (response.status) {
        $('#editProvince').append('<option value="">Choice</option>');
        
        var provinces = response.data;
  
        $.each(provinces, function(index, province) {
          $('#editProvince').append('<option value="' + province.id + '">' + province.name + '</option>');
        });
  
        // Memperbarui tampilan Select2 pada dropdown Province
        $('#editProvince').select2({
            placeholder: 'Choice',
            minimumInputLength: 0,
            dropdownParent: $('#editProvince').parent()
        });
        $('#editRegency').select2({
          disabled: true,
        });
        $('#editDistrict').select2({
          disabled: true,
        });
        $('#editVillage').select2({
          disabled: true,
        });
      } else {
        $('#editProvince').append('<option value="">' + response.data + '</option>');
        $('#editRegency').select2({
          disabled: true,
        });
        $('#editDistrict').select2({
          disabled: true,
        });
        $('#editVillage').select2({
          disabled: true,
        });
      }
    }
  });

  // Mengubah opsi pilihan pada dropdown Regency berdasarkan Province yang dipilih
  $('#editProvince').on('change', function(){
    var provinceId = $(this).val();

    // Jika Province tidak dipilih, kosongkan juga dropdown District dan Village
    if (provinceId === '') {
      $('#editRegency').empty();
      $('#editDistrict').empty();
      $('#editVillage').empty();
      return;
    }

    // Mengirim permintaan Ajax untuk mendapatkan data Regency berdasarkan Province yang dipilih
    $.ajax({
      url: baseUrl + 'pemilu/master-data/tps/get-regencies',
      type: 'GET',
      data: { provinceId: provinceId },
      dataType: 'json',
      success: function(response) {
        if (response.status) {
          var regencies = response.data;

          // Simpan opsi Regency yang saat ini dipilih
          var currentRegency = $('#editRegency').val();

          // Hapus opsi lama pada dropdown Regency
          $('#editRegency').empty();

          $('#editRegency').append('<option value="">Choice</option>');
          $('#editDistrict').append('<option value="">Choice</option>');
          $('#editVillage').append('<option value="">Choice</option>');

          $.each(regencies, function(index, regency) {
            $('#editRegency').append('<option value="' + regency.id + '">' + regency.name + '</option>');
          });

          // Pilih kembali opsi Regency yang sebelumnya dipilih (jika ada)
          if (currentRegency) {
            $('#editRegency').val(currentRegency);
          }

          // Perbarui tampilan Select2 pada dropdown Regency
          $('#editRegency').select2({
            placeholder: 'Choice',
            minimumInputLength: 0,
            disabled: false,
            dropdownParent: $('#editRegency').parent()
          });

          $('#editRegency').trigger('change');
        } else {
          $('#editRegency').empty();
          $('#editDistrict').empty();
          $('#editVillage').empty();
          $('#editRegency').select2({
            disabled: true,
          });
          $('#editDistrict').select2({
            disabled: true,
          });
          $('#editVillage').select2({
            disabled: true,
          });
        }
      }
    });
  });

  // Mengubah opsi pilihan pada dropdown District berdasarkan Regency yang dipilih
  $('#editRegency').on('change', function() {
    var regencyId = $(this).val();

    // Jika Regency tidak dipilih, kosongkan juga dropdown Village
    if (regencyId === '') {
      $('#editDistrict').empty();
      $('#editVillage').empty();
      return;
    }

    // Mengirim permintaan Ajax untuk mendapatkan data District berdasarkan Regency yang dipilih
    $.ajax({
      url: baseUrl + 'pemilu/master-data/tps/get-districts',
      type: 'GET',
      data: { regencyId: regencyId },
      dataType: 'json',
      success: function (response) {
        if (response.status) {
          var districts = response.data;

          // Simpan opsi District yang saat ini dipilih
          var currentDistrict = $('#editDistrict').val();

          // Hapus opsi lama pada dropdown District
          $('#editDistrict').empty();

          $('#editDistrict').append('<option value="">Choice</option>');

          // Mengisi opsi pilihan pada dropdown District
          $.each(districts, function (index, district) {
            $('#editDistrict').append('<option value="' + district.id + '">' + district.name + '</option>');
          });

          // Pilih kembali opsi District yang sebelumnya dipilih (jika ada)
          if (currentDistrict) {
            $('#editDistrict').val(currentDistrict);
          }

          // Perbarui tampilan Select2 pada dropdown District
          $('#editDistrict').select2({
            placeholder: 'Choice',
            minimumInputLength: 0,
            disabled: false,
            dropdownParent: $('#editDistrict').parent()
          });

          $('#editDistrict').trigger('change');
        } else {
          $('#editDistrict').empty();
          $('#editVillage').empty();
          $('#editDistrict').select2({
            disabled: true,
          });
          $('#editVillage').select2({
            disabled: true,
          });
        }
      }
    });
  });

  // Mengubah opsi pilihan pada dropdown Village berdasrkan District yang dipilih
  $('#editDistrict').on('change', function() {
    var districtId = $(this).val();

    // Jika District tidak dipilih, kosongkan dropdown Village
    if (districtId === '') {
      $('#editVillage').empty();
      return;
    }

    // Mengirim permintaan Ajax untuk mendapatkan data Village berdasarkan District yang dipilih
    $.ajax({
      url: baseUrl + 'pemilu/master-data/tps/get-villages',
      type: 'GET',
      data: { districtId: districtId },
      dataType: 'json',
      success: function(response) {
        if (response.status) {
          var villages = response.data;

          // Simpan opsi District yang saat ini dipilih
          var currentVillage = $('#editVillage').val();

          // Hapus opsi lama pada dropdown Village
          $('#editVillage').empty();

          $.each(villages, function(index, village) {
            $('#editVillage').append('<option value="' + village.id + '">' + village.name + '</option>');
          });

          // Pilih kembali opsi Village yang sebelumnya dipilih (jika ada)
          if (currentVillage) {
            $('#editVillage').val(currentVillage);
          }

          // Perbarui tampilan Select2 pada dropdown Village
          $('#editVillage').select2({
            placeholder: 'Choice',
            disabled: false,
            minimumInputLength: 0,
            dropdownParent: $('#editVillage').parent()
          });

          $('#editVillage').trigger('change');
        } else {
          $('#editVillage').empty();
          $('#editVillage').select2({
            disabled: true,
          });
        }
      }
    });
  });

});
