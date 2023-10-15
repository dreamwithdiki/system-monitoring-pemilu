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

  // Mengirim permintaan Ajax untuk mendapatkan data Province
  $.ajax({
    url: baseUrl + 'pemilu/master-data/caleg/get-provinces',
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
      url: baseUrl + 'pemilu/master-data/caleg/get-regencies',
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
      url: baseUrl + 'pemilu/master-data/caleg/get-districts',
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
      url: baseUrl + 'pemilu/master-data/caleg/get-villages',
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
        { data: 'caleg_nik' },
        { data: 'caleg_name' },
        { data: 'caleg_province' },
        { data: 'caleg_regency' },
        { data: 'caleg_district' },
        { data: 'caleg_village' },
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
        { data: 'caleg_nama_partai', orderable: false },
        { data: 'caleg_status', orderable: false }
      ],
      columnDefs: [
        {
          targets: 10,
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
          targets: 11,
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
      caleg_province: {
          validators: {
          notEmpty: {
              message: 'Please select province name'
          }
        }
      },
      caleg_regency: {
          validators: {
          notEmpty: {
              message: 'Please select regency name'
          }
        }
      },
      caleg_district: {
          validators: {
          notEmpty: {
              message: 'Please select district name'
          }
        }
      },
      caleg_village: {
          validators: {
          notEmpty: {
              message: 'Please select village name'
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
      caleg_province: {
          validators: {
          notEmpty: {
              message: 'Please select province name'
          }
        }
      },
      caleg_regency: {
          validators: {
          notEmpty: {
              message: 'Please select regency name'
          }
        }
      },
      caleg_district: {
          validators: {
          notEmpty: {
              message: 'Please select district name'
          }
        }
      },
      caleg_village: {
          validators: {
          notEmpty: {
              message: 'Please select village name'
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

        // Display current photo
        if (response.data.caleg_photo) {
          var photoUrl = baseUrl + 'pemilu/master-data/caleg/uploads/' + caleg_id + '?' + Date.now();
          $('.current-photo').attr('src', photoUrl);
        }
        // Set value of oldImage input
        $('#oldImage').val(response.data.caleg_photo);

        // Kosongkan dropdown "Province" sebelum menambahkan opsi-opsi baru
        $('#editProvince').empty();

        // Mendapatkan data provinces
        $.ajax({
          url: baseUrl + 'pemilu/master-data/caleg/get-provinces',
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

  // Mengirim permintaan Ajax untuk mendapatkan data Province
  $.ajax({
    url: baseUrl + 'pemilu/master-data/caleg/get-provinces',
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
      url: baseUrl + 'pemilu/master-data/caleg/get-regencies',
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
      url: baseUrl + 'pemilu/master-data/caleg/get-districts',
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
      url: baseUrl + 'pemilu/master-data/caleg/get-villages',
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
