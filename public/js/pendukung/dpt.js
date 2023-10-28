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

   // textarea
   const textarea = document.querySelector('#autosize-address');
   const textareaAddress = document.querySelector('#edit-autosize-address');

   // Autosize
   // --------------------------------------------------------------------
   if (textarea) {
     autosize(textarea);
   }

   if (textareaAddress) {
    autosize(textareaAddress);
  }

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

  // Mengirim permintaan Ajax untuk mendapatkan data Province
  $.ajax({
    url: baseUrl + 'pendukung/dpt/get-provinces',
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
      url: baseUrl + 'pendukung/dpt/get-regencies',
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
      url: baseUrl + 'pendukung/dpt/get-districts',
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
      url: baseUrl + 'pendukung/dpt/get-villages',
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
        {
          data: 'dpt_address',
          render: function (data, type, row) {
            if (data) {
              var expanded = row.expanded ? row.expanded : false;

              if (!expanded) {
                var shortDesc = data.length > 25 ? data.substr(0, 25) + '...' : data;
                var showMoreHtml = data.length > 25 ? '<a href="javascript:void(0);" class="show-more">Show More</a>' : '';
                return '<div style="white-space: pre-wrap;" class="short-desc">' + shortDesc + '</div>' + showMoreHtml;
              } else {
                return '<div style="white-space: pre-wrap;" class="full-desc">' + data + '</div><a href="javascript:void(0);" class="show-less">Show Less</a>';
              }
            } else {
              return '-';
            }
          }
        },
        { data: 'dpt_rt' },
        { data: 'dpt_rw' },
        { data: 'dpt_province' },
        { data: 'dpt_regency' },
        { data: 'dpt_district' },
        { data: 'dpt_village' },
        { data: 'tps_name' },
        { data: 'dpt_status', orderable: false }
      ],
      columnDefs: [
        {
          targets: 12,
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
          targets: 13,
          searchable: false,
          orderable: false,
          // render: function (data, type, row, meta) {
          //     return '' +
          //     '<div class="d-inline-block text-nowrap">' +
          //         '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
          //         '<div class="dropdown-menu">' +
          //             '<a id="dropdownMenuEdit" data-id="' + row.dpt_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>' +
          //             '<div class="dropdown-divider"></div>' +
          //             '<a id="dropdownMenuDetail" data-id="' + row.dpt_id + '" data-name="' + row.dpt_name + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-detail me-1"></i> Detail</a>' +
          //             '<div class="dropdown-divider"></div>' +
          //             '<a id="dropdownMenuActivate" data-id="' + row.dpt_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
          //             '<a id="dropdownMenuDeactivate" data-id="' + row.dpt_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Deactivate</a>' +
          //             '<div class="dropdown-divider"></div>' +
          //             '<a id="dropdownMenuDelete" data-id="' + row.dpt_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
          //         '</div>' +
          //     '</div>';
          // }

          render: function (data, type, row, meta) {
            let dropdownMenu = '<div class="dropdown-menu">';
            
            if (row.role_id === 5) {
              // Role_id is 5, only show the "Detail" option
              dropdownMenu += '<a id="dropdownMenuDetail" data-id="' + row.dpt_id + '" data-name="' + row.dpt_name + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-detail me-1"></i> Detail</a>';
            } else {
              // For other role_ids, show the full dropdown menu
              dropdownMenu += '<a id="dropdownMenuEdit" data-id="' + row.dpt_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>' +
                '<div class="dropdown-divider"></div>' +
                '<a id="dropdownMenuDetail" data-id="' + row.dpt_id + '" data-name="' + row.dpt_name + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-detail me-1"></i> Detail</a>' +
                '<div class="dropdown-divider"></div>' +
                '<a id="dropdownMenuActivate" data-id="' + row.dpt_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
                '<a id="dropdownMenuDeactivate" data-id="' + row.dpt_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);" style="padding-left: 20px;"><i class="bx bx-x me-1"></i> Deactivate</a>' +
                '<div class="dropdown-divider"></div>' +
                '<a id="dropdownMenuDelete" data-id="' + row.dpt_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>';
            }
          
            dropdownMenu += '</div>';
            
            return '' +
              '<div class="d-inline-block text-nowrap">' +
              '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
              dropdownMenu +
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
          },
          stringLength: {
            min: 16,
            message: 'NIK max harus 16 digit angka.'
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
      dpt_address: {
        validators: {
          notEmpty: {
            message: 'Please enter address'
          }
        }
      },
      dpt_rt: {
        validators: {
          notEmpty: {
            message: 'Please enter RT'
          }
        }
      },
      dpt_rw: {
        validators: {
          notEmpty: {
            message: 'Please enter RW'
          }
        }
      },
      dpt_province: {
          validators: {
          notEmpty: {
              message: 'Please select province name'
          }
        }
      },
      dpt_regency: {
          validators: {
          notEmpty: {
              message: 'Please select regency name'
          }
        }
      },
      dpt_district: {
          validators: {
          notEmpty: {
              message: 'Please select district name'
          }
        }
      },
      dpt_village: {
          validators: {
          notEmpty: {
              message: 'Please select village name'
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
        rowSelector: function (field, ele) {
          // field is the field name & ele is the field element
          switch (field) {
            case 'dpt_address':
              return '.col-md-12';
            default:
              return '.mb-3';
          }
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
          },
          stringLength: {
            min: 16,
            message: 'NIK max harus 16 digit angka.'
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
      dpt_address: {
        validators: {
          notEmpty: {
            message: 'Please enter address'
          }
        }
      },
      dpt_rt: {
        validators: {
          notEmpty: {
            message: 'Please enter RT'
          }
        }
      },
      dpt_rw: {
        validators: {
          notEmpty: {
            message: 'Please enter RW'
          }
        }
      },
      dpt_province: {
          validators: {
          notEmpty: {
              message: 'Please select province name'
          }
        }
      },
      dpt_regency: {
          validators: {
          notEmpty: {
              message: 'Please select regency name'
          }
        }
      },
      dpt_district: {
          validators: {
          notEmpty: {
              message: 'Please select district name'
          }
        }
      },
      dpt_village: {
          validators: {
          notEmpty: {
              message: 'Please select village name'
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
        rowSelector: function (field, ele) {
          // field is the field name & ele is the field element
          switch (field) {
            case 'dpt_address':
              return '.col-md-12';
            default:
              return '.mb-3';
          }
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

        $('.dpt_address').val(response.data.dpt_address);
        $('#editRT').val(response.data.dpt_rt);
        $('#editRW').val(response.data.dpt_rw);
        $('#edit-address').val(response.data.dpt_address);

        if (response.data.tps) {
          var tpsLabel = response.data.tps.tps_code + " - " + response.data.tps.tps_name;
          var option = new Option(tpsLabel, response.data.tps.tps_id, true, true);
          $('#editTps').append(option).trigger('change');
        }        

        // Kosongkan dropdown "Province" sebelum menambahkan opsi-opsi baru
        $('#editProvince').empty();

        // Mendapatkan data provinces
        $.ajax({
          url: baseUrl + 'pendukung/dpt/get-provinces',
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
      var dpt_name = $(this).data('name');
      // Fungsi untuk Detail dpt
      // get data
      $.ajax({
        url: baseUrl + "pendukung/dpt/show/" + dpt_id,
        type: 'GET',
        beforeSend: function (data) {
          window.Helpers.blockUIModalLoader(modal_class_loader);
        },
        success: function (response) {
  
          $('#detNik').text(response.data.dpt_nik);
          $('#detNama').text(response.data.dpt_name);
          $('#detJenkel').text(response.data.dpt_jenkel == 1 ? 'Laki-Laki' : 'Perempuan');
          $('#detAddress').text(response.data.dpt_address);
          $('#detRT').text(response.data.dpt_rt);
          $('#detRW').text(response.data.dpt_rw);
          $('#detProvince').text(response.data.province.name);
          $('#detRegency').text(response.data.regency.name);
          $('#detDistrict').text(response.data.district.name);
          $('#detVillage').text(response.data.village.name);
          $('#detTps').text(response.data.tps.tps_code + " - " + response.data.tps.tps_name);

          var statusText = response.data.dpt_status === 1 ? '<span class="badge bg-danger">Deactive</span>' : '<span class="badge bg-success">Active</span>';
          $('#detStatus').html(statusText);

          var createdBy =response.data.user.user_uniq_name;

          if (response.data.role.role_id === 1) {
              createdBy += ' <span class="badge bg-primary"><i class="bx bx-badge-check"></i></span>'; 
          } 

          $('#detCreatedBy').html(createdBy);

          modal_class_loader.unblock();
        }
      });
      $('#detFormLabel > p').html('Detail DPT <b>' + dpt_name + '</b>');
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

    $('#addProvince').val('').trigger('change');
    $('#addRegency').val('').trigger('change');
    $('#addDistrict').val('').trigger('change');
    $('#addVillage').val('').trigger('change');
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

  // Mengirim permintaan Ajax untuk mendapatkan data Province
  $.ajax({
    url: baseUrl + 'pendukung/dpt/get-provinces',
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
      url: baseUrl + 'pendukung/dpt/get-regencies',
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
      url: baseUrl + 'pendukung/dpt/get-districts',
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
      url: baseUrl + 'pendukung/dpt/get-villages',
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

   // address-maxlength & repeater (jquery)
   $(function () {
    var maxlengthInput = $('.address-maxlength');

    // Address Max Length
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
