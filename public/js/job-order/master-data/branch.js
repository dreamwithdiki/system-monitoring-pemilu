/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table = $('.datatables-ajax');
  var modal_add_branch = $('#modalAddBranch');
  var modal_class_loader = $('.modal-block-loader');
  var ac_client = $('.ac_client');
  var typingTimer;

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  });

  // Mengirim permintaan Ajax untuk mendapatkan data Province
  $.ajax({
    url: baseUrl + 'site-visit/master-data/partner/get-provinces',
    type: 'GET',
    dataType: 'json',
    success: function (response) {
      if (response.status) {
        $('#addProvince').append('<option value="">Choice</option>');

        var provinces = response.data;

        $.each(provinces, function (index, province) {
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
  $('#addProvince').on('change', function () {
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
      url: baseUrl + 'site-visit/master-data/partner/get-regencies',
      type: 'GET',
      data: { provinceId: provinceId },
      dataType: 'json',
      success: function (response) {
        if (response.status) {
          var regencies = response.data;

          // Simpan opsi Regency yang saat ini dipilih
          var currentRegency = $('#addRegency').val();

          // Hapus opsi lama pada dropdown Regency
          $('#addRegency').empty();

          $('#addRegency').append('<option value="">Choice</option>');
          $('#addDistrict').append('<option value="">Choice</option>');
          $('#addVillage').append('<option value="">Choice</option>');

          $.each(regencies, function (index, regency) {
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
  $('#addRegency').on('change', function () {
    var regencyId = $(this).val();

    // Jika Regency tidak dipilih, kosongkan juga dropdown Village
    if (regencyId === '') {
      $('#addDistrict').empty();
      $('#addVillage').empty();
      return;
    }

    // Mengirim permintaan Ajax untuk mendapatkan data District berdasarkan Regency yang dipilih
    $.ajax({
      url: baseUrl + 'site-visit/master-data/partner/get-districts',
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
  $('#addDistrict').on('change', function () {
    var districtId = $(this).val();

    // Jika District tidak dipilih, kosongkan dropdown Village
    if (districtId === '') {
      $('#addVillage').empty();
      return;
    }

    // Mengirim permintaan Ajax untuk mendapatkan data Village berdasarkan District yang dipilih
    $.ajax({
      url: baseUrl + 'site-visit/master-data/partner/get-villages',
      type: 'GET',
      data: { districtId: districtId },
      dataType: 'json',
      success: function (response) {
        if (response.status) {
          var villages = response.data;

          // Simpan opsi District yang saat ini dipilih
          var currentVillage = $('#addVillage').val();

          // Hapus opsi lama pada dropdown Village
          $('#addVillage').empty();

          $.each(villages, function (index, village) {
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
        url: baseUrl + 'job-order/master-data/branch/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'job-order/master-data/branch/get');
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
        { data: 'branch_code' },
        { data: 'branch_name' },
        {
          data: 'branch_address',
          orderable: false,
          render: function (data, type, row) {
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
        { data: 'branch_province_name'},
        { data: 'branch_regency_name'},
        { data: 'branch_status', orderable: false }
      ],
      columnDefs: [
        {
          targets: 6,
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
          targets: 7,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
            return '' +
              '<div class="d-inline-block text-nowrap">' +
              '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
              '<div class="dropdown-menu">' +
              '<a id="dropdownMenuEdit" data-id="' + row.branch_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>' +
              '<a id="dropdownMenuActivate" data-id="' + row.branch_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
              '<a id="dropdownMenuDeactivate" data-id="' + row.branch_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Deactivate</a>' +
              '<a id="dropdownMenuDelete" data-id="' + row.branch_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
              '</div>' +
              '</div>';
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

  $(document).on('click', '.show-more', function (e) {
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

  $(document).on('click', '.show-less', function (e) {
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

  $(document).on('click', '#addBranchButton', function (e) {
    initMap();
  });

  // Add Form
  var add_branch_form = document.getElementById('formAddBranch');

  // Site Form Validation
  var fv = FormValidation.formValidation(add_branch_form, {
    fields: {
      branch_name: {
        validators: {
          notEmpty: {
            message: 'Please enter branch name'
          }
        }
      },
      branch_address: {
        validators: {
          notEmpty: {
            message: 'Please enter branch address'
          }
        }
      },
      branch_phone: {
        validators: {
          regexp: {
            message: 'Please enter a valid partner phone',
            regexp: /^\+?[0-9]+$/,
          }
        }
      },
      branch_fax: {
        validators: {
          regexp: {
            message: 'Please enter a valid partner fax',
            regexp: /^\+?[0-9]+$/,
          }
        }
      },
      branch_email: {
        validators: {
          emailAddress: {
            message: 'Please enter valid email address'
          }
        }
      },
      branch_postal_code: {
      },
      branch_province: {
        validators: {
          notEmpty: {
            message: 'Please select province name'
          }
        }
      },
      branch_regency: {
        validators: {
          notEmpty: {
            message: 'Please select regency name'
          }
        }
      },
      // branch_location_map: {
      //   validators: {
      //     notEmpty: {
      //       message: 'Please select point in map'
      //     }
      //   }
      // },
      // branch_latitude: {
      //   validators: {
      //     notEmpty: {
      //       message: 'Please select point in map'
      //     }
      //   }
      // },
      // branch_longitude: {
      //   validators: {
      //     notEmpty: {
      //       message: 'Please select point in map'
      //     }
      //   }
      // },
      branch_desc: {
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
    // Adding partner when form successfully validate
    var url = "";
    if ($('#formAddBranch').data('method') == 'add') {
      url = "job-order/master-data/branch/store";
    } else if ($('#formAddBranch').data('method') == 'edit') {
      url = "job-order/master-data/branch/update/" + $('#formAddBranch').data('id');
    } else {
      url = "";
    }

    $.ajax({
      data: $('#formAddBranch').serialize(),
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax.draw();
        modal_add_branch.modal('hide');

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
        modal_add_branch.modal('hide');
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

  // Edit button handler
  $(document).on('click', '#dropdownMenuEdit', function () {
    var branch_id = $(this).data('id');

    // get data
    $.ajax({
      url: baseUrl + "job-order/master-data/branch/show/" + branch_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {
        if (response.data.branch_status == 2) {
          $('#addStatus').prop('checked', true);
        } else {
          $('#addStatus').prop('checked', false);
        }

        $('#addCode').val(response.data.branch_code);
        $('#addName').val(response.data.branch_name);

        $('#addEmail').val(response.data.branch_email);
        $('#addAddress').val(response.data.branch_address);
        $('#addPostalCode').val(response.data.branch_postal_code);

        // Kosongkan dropdown "Province" sebelum menambahkan opsi-opsi baru
        $('#addProvince').empty();

        // Mendapatkan data provinces
        $.ajax({
          url: baseUrl + 'site-visit/master-data/partner/get-provinces',
          type: 'GET',
          dataType: 'json',
          success: function (provinceResponse) {
            if (provinceResponse.status) {
              var provinces = provinceResponse.data;

              // Menambahkan opsi-opsi baru pada dropdown "Province"
              $.each(provinces, function (index, province) {
                var option = new Option(province.name, province.id);
                $('#addProvince').append(option);
              });

              // Memperbarui tampilan Select2 pada dropdown "Province"
              $('#addProvince').select2({
                placeholder: 'Choice',
                minimumInputLength: 0,
                dropdownParent: $('#addProvince').parent()
              });

              // Memilih kembali opsi "Province" yang sebelumnya dipilih (jika ada)
              if (response.data.province) {
                $('#addProvince').val(response.data.province.id).trigger('change');
              }
            } else {
              $('#addProvince').append('<option value="">' + provinceResponse.data + '</option>');
              $('#addRegency').append('<option value="">' + provinceResponse.data + '</option>');
              $('#addDistrict').append('<option value="">' + provinceResponse.data + '</option>');
              $('#addVillage').append('<option value="">' + provinceResponse.data + '</option>');
            }
          }
        });

        if (response.data.regency) {
          var option = new Option(response.data.regency.name, response.data.regency.id, true, true);
          $('#addRegency').append(option).trigger('change');
        }

        if (response.data.district) {
          var option = new Option(response.data.district.name, response.data.district.id, true, true);
          $('#addDistrict').append(option).trigger('change');
        }

        if (response.data.village) {
          var option = new Option(response.data.village.name, response.data.village.id, true, true);
          $('#addVillage').append(option).trigger('change');
        }

        $('#addPhone').val(response.data.branch_phone);
        $('#addFax').val(response.data.branch_fax);
        $('#addDesc').val(response.data.branch_desc);
        $('#branch_location_map').val(response.data.branch_location_map);

        // Set the latitude and longitude for the marker
        var latitude = parseFloat(response.data.branch_latitude);
        var longitude = parseFloat(response.data.branch_longitude);

        $('#branch_latitude').val(isNaN(latitude) ? '' : latitude);
        $('#branch_longitude').val(isNaN(longitude) ? '' : longitude);

        // Call initMap here with the updated values
        initMap();
        modal_class_loader.unblock();
      }
    });

    $('#addFormLabel > p').html('Edit branch.');
    $('#formAddBranch').attr('data-method', 'edit');
    $('#formAddBranch').data('method', 'edit');
    $('#formAddBranch').attr('data-id', branch_id);
    modal_add_branch.modal('show');
  });

  // Active / Deactive status button handler
  $(document).on('click', '.dropdownMenuStatusUpdate', function () {
    var branch_id = $(this).data('id'),
      branch_status = $(this).data('status');

    if (branch_status == 2) {
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
          data: { branch_id: branch_id, branch_status: branch_status },
          type: 'POST',
          url: baseUrl + 'job-order/master-data/branch/update-status',
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
    var branch_id = $(this).data('id');

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
          data: { branch_id: branch_id },
          type: 'POST',
          url: baseUrl + 'job-order/master-data/branch/delete',
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
  modal_add_branch.on('hidden.bs.modal', function () {
    $('#addFormLabel > p').html('Add new branch.');
    $('#formAddBranch').attr('data-method', 'add');
    $('#formAddBranch').data('method', 'add');

    $('#client_id').val('').trigger('change');
    $('#addProvince').val('').trigger('change');
    $('#addRegency').val('').trigger('change');
    $('#addDistrict').val('').trigger('change');
    $('#addVillage').val('').trigger('change');
    $('#branch_location_map').val(null);
    $('#branch_latitude').val(null);
    $('#branch_longitude').val(null);
    fv.resetForm(true);
  });

});
