/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {

  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var modal_add_user = $('#modalAddUser');
  var modal_edit_user = $('#modalEditUser');
  var ac_role       = $('.ac_role');
  var ac_edit_role       = $('.ac_edit_role');
  var dt_ajax_table = $('.datatables-ajax');
  var modal_class_loader = $('.modal-block-loader');

  var typingTimer;
  
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  });

  // Select2 role handler
  if (ac_role.length) {
    var $this = ac_role;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select role',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/role/find',
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
    url: baseUrl + 'pemilu/master-data/user/get-provinces',
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
      url: baseUrl + 'pemilu/master-data/user/get-regencies',
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
      url: baseUrl + 'pemilu/master-data/user/get-districts',
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
      url: baseUrl + 'pemilu/master-data/user/get-villages',
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
        url: baseUrl + 'pemilu/master-data/user/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'pemilu/master-data/user/get');
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
        // columns according to JSON
        { data: 'no' },
        { 
          data: 'user_photo',
          render: function (data, type, row) {
            if (data !== "" && data !== null && data !== undefined) {
              var avatarClass = '';
              
              if (row.user_status == 2) {
                avatarClass = 'avatar-online';
              } else if (row.user_status == 1) {
                avatarClass = 'avatar-away';
              } else if (row.user_status == 5) {
                avatarClass = 'avatar-busy';
              } else {
                avatarClass = 'avatar-offline';
              }

              var http = new XMLHttpRequest();
              http.open('HEAD', '/storage/users_uploads/' + row.user_photo, false);
              http.send();

              var fileImageElement = document.createElement('img'); // Create the image element

              if (http.status === 200) {
                fileImageElement.src = '/storage/users_uploads/' + row.user_photo;
              } else {
                fileImageElement.src = '/assets/upload/user/default.jpeg';
              }

              return '<div class="avatar avatar-md me-2 ' + avatarClass + '"><img src="' + fileImageElement.src + '" alt="Avatar" class="rounded-circle"></div>';

            } else {
              return '<span class="badge bg-label-warning me-1">No Photo</span>';
            }
          }
        },
        { 
          data: 'user_nik',
          render: function (data, type, row) {
            if (row.user_status == 5) {
              return '<del>' + data + '</del>';
            } else {
              return data;
            }
          }
        }, 
        { 
          data: 'user_uniq_name',
          render: function (data, type, row) {
            if (row.user_status == 5) {
              return '<del>' + data + '</del>';
            } else {
              return data;
            }
          }
        }, 
        { 
          data: 'user_no_hp',
          render: function (data, type, row) {
            if (row.user_status == 5) {
              return '<del>' + data + '</del>';
            } else {
              return data;
            }
          }
        },
        { 
          data: 'user_email',
          render: function (data, type, row) {
            if (row.user_status == 5) {
              return '<del>' + data + '</del>';
            } else {
              return data;
            }
          }
        },
        { 
          data: 'role_name',
          render: function (data, type, row) {
            if (row.user_status == 5) {
              return '<del>' + data + '</del>';
            } else {
              return data;
            }
          }
        },
        { 
          data: 'user_last_login',
          orderable: false,
          render: function (data, type, row) {
            if (row.user_status == 5) {
              return '<del>' + (data ? data : '') + '</del>';
            } else {
              return data ? data : '';
            }
          }
        },
        { data: 'user_status' },
      ],
      columnDefs: [
        {
          targets: 8,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
            if (data == 2) {
              return '<span class="badge bg-label-success me-1">Active</span>';
            } else if (data == 1) {
              return '<span class="badge bg-label-warning me-1">Deactive</span>';
            } else if (data == 5) {
              return '<span class="badge bg-label-danger me-1">Deleted</span>';
            } else {
              return '<span class="badge bg-label-secondary me-1">Unknown</span>';
            }
          }
        },
        {
          // Actions
          targets: 9,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
              try {
                // Original content of the render function
                var editButton = '';
                var deleteButton = '';

                if (row.user_status == 5) {
                  // Jika user_status = 5 (Deleted), tombol delete, tombol edit, change pass tidak ditampilkan
                  editButton = '';
                  deleteButton = '';
                } else if (row.user_status == 1 || row.user_status == 2) {
                  // Jika user_status = 1 (Deactive) atau user_status = 2 (Active), tombol delete, tombol edit ditampilkan
                  editButton =  '<button id="btn_user_edit" class="btn btn-sm btn-icon btn-primary mx-1" data-id="' + row.user_id + '" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="Edit ' + row.user_uniq_name + '"><i class="bx bx-edit me-1"></i></button>';
                  // changepasswordButton = '<a id="btn_change_pass" data-id="' + row.user_id + '" data-name="' + row.user_uniq_name + '" class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalChangePass"><i class="bx bx-key me-1"></i> Change Password</a>';
                  // kasih kondisi jika user_id = 1 yaitu button hapus nya di hilangin karena ini untuk benar-benar superadmin. walaupun yg lain ada superadmin tapi ini superadmin special tidak oleh ada button hapusnya.
                  if (row.user_id > 1) {
                    deleteButton = '<button id="btn_user_delete" class="btn btn-sm btn-icon btn-danger mx-1" data-id="' + row.user_id + '" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="Delete ' + row.user_uniq_name + '"><i class="bx bx-trash me-1"></i></button>';
                  }
                }
                
                return (
                    '<div class="d-inline-block text-nowrap">' +
                      editButton +
                      deleteButton +
                      '<button class="btn btn-sm btn-icon btn-info dropdown-toggle hide-arrow mx-1" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                      '<div class="dropdown-menu">' +
                        '<a id="btn_user_activate" data-id="' + row.user_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
                        '<a id="btn_user_deactivate" data-id="' + row.user_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Deactivate</a>' +
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
    $(document).on('keyup', '.search', function(e) {
      var $this = $(this);
      clearTimeout(typingTimer);
      typingTimer = setTimeout(function() {
        dt_ajax_table.DataTable().search($this.val()).draw();
      }, 1200);
    });    
  }

  // Form
  var addUserForm = document.getElementById('formAddUser');

  var fv = FormValidation.formValidation(addUserForm, {
    fields: {
      user_email: {
        validators: {
          notEmpty: {
            message: 'Please enter your email'
          },
          emailAddress: {
            message: 'Please enter valid email address'
          }
        }
      }, 
      user_ref_id: {
        validators: {
          notEmpty: {
            message: 'The partner name is required'
          }
        }
      },
      user_nik: {
       validators: {
         notEmpty: {
           message: 'Please enter your NIK'
         }
       }
      },
      user_uniq_name: {
        validators: {
          notEmpty: {
            message: 'Please enter your full name'
          }
        }
       },
       user_no_hp: {
        validators: {
          notEmpty: {
            message: 'Please enter mobile phone'
          },
          regexp: {
            message: 'Please enter a valid user phone',
            regexp: /^\+?[0-9]+$/,
          }
        }
      },
      role_id: {
        validators: {
          notEmpty: {
            message: 'Please select role name',
          }
        }
      },
      user_password: {
        validators: {
          notEmpty: {
            message: 'Please enter your password'
          },
          stringLength: {
            min: 8,
            message: 'Password must be more than 8 characters'
          }
        }
      },
      confirm_password: {
        validators: {
          notEmpty: {
            message: 'Please confirm password'
          },
          identical: {
            compare: function () {
              return addUserForm.querySelector('[name="user_password"]').value;
            },
            message: 'The password and its confirm are not the same'
          },
          stringLength: {
            min: 6,
            message: 'Password must be more than 6 characters'
          }
        }
      },
      user_photo : {
      },
      user_province: {
          validators: {
          notEmpty: {
              message: 'Please select province name'
          }
        }
      },
      user_regency: {
          validators: {
          notEmpty: {
              message: 'Please select regency name'
          }
        }
      },
      user_district: {
          validators: {
          notEmpty: {
              message: 'Please select district name'
          }
        }
      },
      user_village: {
          validators: {
          notEmpty: {
              message: 'Please select village name'
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
      autoFocus: new FormValidation.plugins.AutoFocus()
    },
    init: instance => {
      instance.on('plugins.message.placed', function (e) {
        if (e.element.parentElement.classList.contains('input-group')) {
          e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
        }
      });
    }
  }).on('core.form.valid', function () {
    var url = "pemilu/master-data/user/store";
    var form_data = new FormData(addUserForm); 

    $.ajax({
      data: form_data,
      url: baseUrl + url,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function success(response) {
        dt_ajax.draw();
        modal_add_user.modal('hide');

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
        modal_add_user.modal('hide');
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
  
  var editUserForm = document.getElementById('formEditUser');
  // edit user form validation
  var fvEdit = FormValidation.formValidation(editUserForm, {
    fields: {
      user_email: {
        validators: {
          notEmpty: {
            message: 'Please enter your email'
          },
          emailAddress: {
            message: 'Please enter valid email address'
          }
        }
      }, 
      user_ref_id: {
        validators: {
          notEmpty: {
            message: 'The partner name is required'
          }
        }
      },
      user_nik: {
        validators: {
          notEmpty: {
            message: 'Please enter your NIK'
          }
        }
       },
       user_uniq_name: {
         validators: {
           notEmpty: {
             message: 'Please enter your full name'
           }
         }
        },
        user_no_hp: {
         validators: {
           notEmpty: {
             message: 'Please enter mobile phone'
           },
           regexp: {
             message: 'Please enter a valid user phone',
             regexp: /^\+?[0-9]+$/,
           }
         }
       },
      role_id: {
        validators: {
          notEmpty: {
            message: 'Please select role name'
          }
        }
      },
      user_photo : {
      },
      user_province: {
          validators: {
          notEmpty: {
              message: 'Please select province name'
          }
        }
      },
      user_regency: {
          validators: {
          notEmpty: {
              message: 'Please select regency name'
          }
        }
      },
      user_district: {
          validators: {
          notEmpty: {
              message: 'Please select district name'
          }
        }
      },
      user_village: {
          validators: {
          notEmpty: {
              message: 'Please select village name'
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
    var url = "pemilu/master-data/user/update/" + $('#formEditUser').attr('data-id');
    var form_data = new FormData(editUserForm); 

    $.ajax({
      data: form_data,
      url: baseUrl + url,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function success(response) {
        dt_ajax.draw();
        modal_edit_user.modal('hide');
        if (response.status) {
          Swal.fire({
            icon: 'success',
            title: response.message.title,
            text: response.message.text,
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });

           // Update user photo using the handlePhotoChange function
           handlePhotoChange(response);
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
        modal_edit_user.modal('hide');
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

  // Edit Record
  $(document).on('click', '#btn_user_edit', function () {
    var user_id = $(this).data('id');
  
    // empty image preview
    $('#imagePreview').empty();
  
    // get data
    $.ajax({
      url: baseUrl + "pemilu/master-data/user/show/" + user_id,
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {
        if (response.data.user_status == 2) {
          $('#editStatus').prop('checked', true);
        } else {
          $('#editStatus').prop('checked', false);
        }
  
        $('#edit_user_nik').val(response.data.user_nik);
        $('#edit_user_uniq_name').val(response.data.user_uniq_name);
        $('#edit_user_no_hp').val(response.data.user_no_hp);
        $('#edit_user_email').val(response.data.user_email);
        if (response.data.role) {
          var option = new Option(response.data.role.role_name, response.data.role.role_id, true, true);
          $('#edit_role').append(option).trigger('change');
        }
  
        // Kosongkan dropdown "Province" sebelum menambahkan opsi-opsi baru
        $('#editProvince').empty();

        // Mendapatkan data provinces
        $.ajax({
          url: baseUrl + 'pemilu/master-data/user/get-provinces',
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
  
        // Display current photo
        if (response.data.user_photo) {
          var photoUrl = baseUrl + 'pemilu/master-data/user/uploads/' + response.data.user_id;
          $('.current-photo').attr('src', photoUrl);
        } else {
          $('.current-photo').attr('src', '#');
          $('.current-photo').attr('alt', 'No Photo');
        }
        // Set value of oldImage input
        $('#oldImage').val(response.data.user_photo);
  
        modal_class_loader.unblock();
      }
    });
  
    $('#editFormLabel > p').html('Edit User.');
    $('#formEditUser').attr('data-method', 'edit');
    $('#formEditUser').data('method', 'edit');
    $('#formEditUser').attr('data-id', user_id);
  
    modal_edit_user.modal('show');
  });
  
  // Active / Deactive status button handler
  $(document).on('click', '.dropdownMenuStatusUpdate', function () {
    var user_id = $(this).data('id'),
      user_status = $(this).data('status');

    if (user_status == 2) {
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
          data: {user_id: user_id, user_status: user_status},
          type: 'POST',
          url: baseUrl + 'pemilu/master-data/user/update-status',
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

  // Delete Record handler
  $(document).on('click', '#btn_user_delete', function () {
    var user_id = $(this).data('id');
    // sweetalert for confirmation of delete
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        // delete the data
        $.ajax({
          data: {user_id: user_id},
          type: 'POST',
          url: baseUrl + 'pemilu/master-data/user/delete',
          success: function () {
            dt_ajax.draw();
          },
          error: function (error) {
            console.log(error);
          }
        });

        // success sweetalert
        Swal.fire({
          icon: 'success',
          title: 'Deleted!',
          text: 'The user has been deleted!',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.fire({
          title: 'Cancelled',
          text: 'The User is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });


  modal_add_user.on('hidden.bs.modal', function () {
    $('#addFormLabel > p').html('Add new User.');
    $('#formAddUser').attr('data-method', 'add');
    $('#formAddUser').data('method', 'add');
    
    $('#user_uniq_name').val(null);
    $('#user_ref_id').val(null);
    $('#role_id').val('').trigger('change');
    $('#user_nik').val(null);
    $('#user_no_hp').val(null);
    $('#user_email').val(null);
    $('#user_password').val(null);
    $('#confirm_password').val(null);
    $('#user_photo').val(null);
    $('#user_province').val('').trigger('change');
    $('#user_regency').val('').trigger('change');
    $('#user_district').val('').trigger('change');
    $('#user_village').val('').trigger('change');
    // fv.resetForm(true);
  });

  modal_edit_user.on('hidden.bs.modal', function () {
    // fvEdit.resetForm(true);
    $('#file').val(null);
    $('#imagePreview').empty();
    $('#imagePreview').html('<img src="#" class="img-fluid" style="max-width: 100%; height: auto;">');
    $('#imagePreview').css("background-image", "none");
  });

  // Image Preview
  $(function() {
      $("#file").on("change", function()
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
      placeholder: 'Select role',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/role/find',
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
    url: baseUrl + 'pemilu/master-data/user/get-provinces',
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
      url: baseUrl + 'pemilu/master-data/user/get-regencies',
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
      url: baseUrl + 'pemilu/master-data/user/get-districts',
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
      url: baseUrl + 'pemilu/master-data/user/get-villages',
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

   // Ambil elemen gambar dengan ID 'userPhoto' dan 'userPhotonav'
   var userPhoto = document.getElementById('userPhoto');
   var userPhotoNav = document.getElementById('userPhotoNav'); 

   // Fungsi untuk mengganti atribut src elemen gambar
   function changeUserPhoto(newPhotoUrl) {
       userPhoto.src = newPhotoUrl;
       userPhotoNav.src = newPhotoUrl;
   }

   // Ini akan dipanggil setelah permintaan perubahan foto berhasil
   function handlePhotoChange(response) {
       if (response.status) {
           changeUserPhoto(response.newUserPhoto); // Panggil fungsi untuk mengganti foto
       }
   }

 }); 