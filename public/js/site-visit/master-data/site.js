/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table = $('.datatables-ajax');
  var ac_client = $('.ac_client');
  var modal_add_site = $('#modalAddSite');
  var modal_class_loader = $('.modal-block-loader');
  var typingTimer;

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  });

  // Select2 client name handler
  if (ac_client.length) {
    var $this = ac_client;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select client name',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/client/find',
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
    url: baseUrl + 'site-visit/master-data/site/get-provinces',
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
      url: baseUrl + 'site-visit/master-data/site/get-regencies',
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
      url: baseUrl + 'site-visit/master-data/site/get-districts',
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
      url: baseUrl + 'site-visit/master-data/site/get-villages',
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
          // $('#addVillage').append('<option value="">' + response.data + '</option>');
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
        url: baseUrl + 'site-visit/master-data/site/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'site-visit/master-data/site/get');
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
        { data: 'site_code' },
        {
          data: 'site_name',
          orderable: false,
          render: function (data, type, row) {
            if (data) {
              var expandedSiteName = row.expandedSiteName ? row.expandedSiteName : false;

              if (!expandedSiteName) {
                var shortDescSiteName = data.length > 25 ? data.substr(0, 25) + '...' : data;
                var showMoreHtml = data.length > 25 ? '<a href="javascript:void(0);" class="show-more-site-name">Show More</a>' : '';
                return '<div style="white-space: pre-wrap;" class="short-desc-site-name">' + shortDescSiteName + '</div>' + showMoreHtml;
              } else {
                return '<div style="white-space: pre-wrap;" class="full-desc-site-name">' + data + '</div><a href="javascript:void(0);" class="show-less-site-name">Show Less</a>';
              }
            } else {
              return '-';
            }
          }
        },
        { data: 'client_name' },
        {
          data: 'site_address',
          orderable: false,
          render: function (data, type, row) {
            if (data) {
              var expanded = row.expanded ? row.expanded : false;

              if (!expanded) {
                var shortDesc = data.length > 30 ? data.substr(0, 30) + '...' : data;
                var showMoreHtml = data.length > 30 ? '<a href="javascript:void(0);" class="show-more-site-address">Show More</a>' : '';
                return '<div style="white-space: pre-wrap;" class="short-desc-site-address">' + shortDesc + '</div>' + showMoreHtml;
              } else {
                return '<div style="white-space: pre-wrap;" class="full-desc-site-address">' + data + '</div><a href="javascript:void(0);" class="show-less-site-address">Show Less</a>';
              }
            } else {
              return '-';
            }
          }
        },
        { data: 'site_status', orderable: false }
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
              '<a id="dropdownMenuEdit" data-id="' + row.site_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>' +
              '<a id="dropdownMenuActivate" data-id="' + row.site_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
              '<a id="dropdownMenuDeactivate" data-id="' + row.site_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdate" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Deactivate</a>' +
              '<a id="dropdownMenuDelete" data-id="' + row.site_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
              '<div class="dropdown-divider"></div>' +
              '<a id="dropdownMenuContact" data-id="' + row.site_id + '" data-name="' + row.site_code + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-id-card me-1"></i> Contact</a>' +
              '</div>' +
              '</div>';
          }
        }
      ],
      order: [[1, 'asc']],
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

  $(document).on('click', '.show-more-site-name', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $shortDescSiteName = $this.prev('.short-desc-site-name');
    var $fullDescSiteName = $shortDescSiteName.next('.full-desc-site-name');
    $shortDescSiteName.hide();
    $fullDescSiteName.show();
    $this.text('Show Less');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedSiteName = true; // Menandai bahwa deskripsi telah di-expand
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-less-site-name', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $fullDescSiteName = $this.prev('.full-desc-site-name');
    var $shortDescSiteName = $fullDescSiteName.prev('.short-desc-site-name');
    $fullDescSiteName.hide();
    $shortDescSiteName.show();
    $this.text('Show More');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expandedSiteName = false; // Menandai bahwa deskripsi telah di-collapse
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-more-site-address', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $shortDescSiteAddress = $this.prev('.short-desc-site-address');
    var $fullDescSiteAddress = $shortDescSiteAddress.next('.full-desc-site-address');
    $shortDescSiteAddress.hide();
    $fullDescSiteAddress.show();
    $this.text('Show Less');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expanded = true; // Menandai bahwa deskripsi telah di-expand
    dt_ajax.row($this.closest('tr')).data(row);
  });

  $(document).on('click', '.show-less-site-address', function (e) {
    e.preventDefault();
    var $this = $(this);
    var $fullDescSiteAddress = $this.prev('.full-desc-site-address');
    var $shortDescSiteAddress = $fullDescSiteAddress.prev('.short-desc-site-address');
    $fullDescSiteAddress.hide();
    $shortDescSiteAddress.show();
    $this.text('Show More');
    var row = dt_ajax.row($this.closest('tr')).data();
    row.expanded = false; // Menandai bahwa deskripsi telah di-collapse
    dt_ajax.row($this.closest('tr')).data(row);
  });

  // Add Form
  var add_site_form = document.getElementById('formAddSite');

  // Site Form Validation
  var fv = FormValidation.formValidation(add_site_form, {
    fields: {
      client_id: {
        validators: {
          notEmpty: {
            message: 'Please enter client name'
          }
        }
      },
      site_code: {
        validators: {
          notEmpty: {
            message: 'Please enter site code'
          }
        }
      },
      site_name: {
        validators: {
          notEmpty: {
            message: 'Please enter site name'
          }
        }
      },
      site_address: {
        validators: {
          notEmpty: {
            message: 'Please enter site address'
          }
        }
      },
      site_postal_code: {
        validators: {
          notEmpty: {
            message: 'Please enter site postal code'
          }
        }
      },
      site_province: {
      },
      site_regency: {
      },
      site_district: {
      },
      site_village: {
      },
      site_phone: {
        validators: {
          regexp: {
            message: 'Please enter a valid phone number',
            regexp: /^\+?[0-9]+$/,
          }
        }
      },

      site_fax: {
        validators: {
          regexp: {
            message: 'Please enter a valid fax number',
            regexp: /^\+?[0-9]+$/,
          }
        }
      },

      site_desc: {
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
    // Adding or Updating site when form successfully validate
    if ($('#formAddSite').data('method') == 'add') {
      var url = "site-visit/master-data/site/store";
    } else if ($('#formAddSite').data('method') == 'edit') {
      var url = "site-visit/master-data/site/update/" + $('#formAddSite').attr('data-id');
    } else {
      var url = "";
    }

    $.ajax({
      data: $('#formAddSite').serialize(),
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax.draw();
        modal_add_site.modal('hide');

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
        modal_add_site.modal('hide');
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
    var site_id = $(this).data('id');

    // get data
    $.ajax({
      url: baseUrl + "site-visit/master-data/site/show/" + site_id,
      type: 'GET',
      beforeSend: function (data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function (response) {

        if (response.data.site_status == 2) {
          $('#addStatus').prop('checked', true);
        } else {
          $('#addStatus').prop('checked', false);
        }

        if (response.data.client) {
          var option = new Option(response.data.client.client_name, response.data.client.client_id, true, true);
          $('#addClient').append(option).trigger('change');
        }

        $('#addCode').val(response.data.site_code);
        $('#addName').val(response.data.site_name);
        $('#addPhone').val(response.data.site_phone);
        $('#addFax').val(response.data.site_fax);
        $('#addPostalCode').val(response.data.site_postal_code);

        // Kosongkan dropdown "Province" sebelum menambahkan opsi-opsi baru
        $('#addProvince').empty();

        // Mendapatkan data provinces
        $.ajax({
          url: baseUrl + 'site-visit/master-data/site/get-provinces',
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

        $('#addAddress').val(response.data.site_address);

        $('#addDesc').val(response.data.site_desc);
        modal_class_loader.unblock();
      }
    });

    $('#addFormLabel > p').html('Edit site.');
    $('#formAddSite').attr('data-method', 'edit');
    $('#formAddSite').data('method', 'edit');
    $('#formAddSite').attr('data-id', site_id);
    modal_add_site.modal('show');
  });

  // Active / Deactive status button handler
  $(document).on('click', '.dropdownMenuStatusUpdate', function () {
    var site_id = $(this).data('id'),
      site_status = $(this).data('status');

    if (site_status == 2) {
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
          data: { site_id: site_id, site_status: site_status },
          type: 'POST',
          url: baseUrl + 'site-visit/master-data/site/update-status',
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
    var site_id = $(this).data('id');

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
          data: { site_id: site_id },
          type: 'POST',
          url: baseUrl + 'site-visit/master-data/site/delete',
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
  modal_add_site.on('hidden.bs.modal', function () {
    $('#addFormLabel > p').html('Add new site.');
    $('#formAddSite').attr('data-method', 'add');
    $('#formAddSite').data('method', 'add');
    $('#addClient').val('').trigger('change');
    $('#addProvince').val('').trigger('change');
    $('#addRegency').val('').trigger('change');
    $('#addDistrict').val('').trigger('change');
    $('#addVillage').val('').trigger('change');
    fv.resetForm(true);
  });

  /**
   * 
   * Contact Site
   * 
   */

  var dt_ajax_table_contact_c = $('.datatables-ajax-contact'),
    modal_manage_contact = $('#modalManageContact'),
    modal_edit_contact = $('#modalEditContact'),
    site_id_param = 0;

  // Datatable contact
  if (dt_ajax_table_contact_c.length) {
    var dt_ajax_table_contact = dt_ajax_table_contact_c.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: baseUrl + 'site-visit/master-data/site/contact/get',
        data: function (d) {
          d.site_id = site_id_param;
        }
      },
      columns: [
        { data: 'no', orderable: false },
        { data: 'site_contact_fullname' },
        { data: 'site_contact_email' },
        { data: 'site_contact_mobile_phone' },
        { data: 'site_contact_phone' },
        { data: 'site_contact_status', orderable: false },
        { data: 'site_contact_actions', orderable: false },
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
              '<a id="dropdownMenuEditContact" data-id="' + row.site_contact_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-1"></i> Edit</a>' +
              '<a id="dropdownMenuActivateContact" data-id="' + row.site_contact_id + '" data-status="2" class="dropdown-item dropdownMenuStatusUpdateContact" href="javascript:void(0);"><i class="bx bx-check me-1"></i> Activate</a>' +
              '<a id="dropdownMenuDeactivateContact" data-id="' + row.site_contact_id + '" data-status="1" class="dropdown-item dropdownMenuStatusUpdateContact" href="javascript:void(0);"><i class="bx bx-x me-1"></i> Deactivate</a>' +
              // '<a id="dropdownMenuDeleteContact" data-id="' + row.site_contact_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
              '</div>' +
              '</div>';
          }
        }
      ],
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
    });
  }

  // Manage Contact type button handler
  $(document).on('click', '#dropdownMenuContact', function () {
    var site_id = $(this).data('id');
    var site_code = $(this).data('name');

    // Reinit contact with new param site_id
    site_id_param = site_id
    dt_ajax_table_contact.draw();

    $('#addFormContactlabel > h3').html('<b>' + site_code + '</b>' + ' - Contact');
    modal_manage_contact.modal('show');
  });

  // Add Contact Form
  var addNewContactForm = document.getElementById('formAddContact');
  // Contact Form Validation
  var fv_add_contact = FormValidation.formValidation(addNewContactForm, {
    fields: {
      site_contact_fullname: {
        validators: {
          notEmpty: {
            message: 'Please enter contact full name'
          }
        }
      },
      site_contact_email: {
        validators: {
          notEmpty: {
            message: 'Please enter contact email'
          },
          emailAddress: {
            message: 'Please enter valid email address'
          }
        }
      },
      site_contact_mobile_phone: {
        validators: {
          notEmpty: {
            message: 'Please enter contact mobile phone'
          },
          regexp: {
            message: 'Please enter a valid contact mobile phone',
            regexp: /^\+?[0-9]+$/,
          }
        }
      },
      site_contact_phone: {
        validators: {
          notEmpty: {
            message: 'Please enter contact phone'
          },
          regexp: {
            message: 'Please enter a valid contact phone',
            regexp: /^\+?[0-9]+$/,
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
    var form_contact = $('#formAddContact');
    var url = "site-visit/master-data/site/contact/store";

    $.ajax({
      data: form_contact.serialize() + "&site_id=" + site_id_param,
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax_table_contact.draw();

        if (response.status) {
          fv_add_contact.resetForm(true);
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
  // End Add Contact Form

  // Edit contact button handler
  $(document).on('click', '#dropdownMenuEditContact', function () {
    var site_contact_id = $(this).data('id');

    // get data
    $.get("".concat(baseUrl, "site-visit/master-data/site/contact/show/").concat(site_contact_id), function (response) {
      if (response.data.site_contact_status == 2) {
        $('#editContactStatus').prop('checked', true);
      } else {
        $('#editContactStatus').prop('checked', false);
      }
      $('#editContactFullname').val(response.data.site_contact_fullname);
      $('#editContactEmail').val(response.data.site_contact_email);
      $('#editContactMobilePhone').val(response.data.site_contact_mobile_phone);
      $('#editContactPhone').val(response.data.site_contact_phone);
    });

    $('#formEditContact').attr('data-method', 'edit');
    $('#formEditContact').data('method', 'edit');
    $('#formEditContact').data('id', site_contact_id);
    $('#formEditContact').attr('data-id', site_contact_id);
    modal_edit_contact.modal('show');
  });

  // Edit Contact Form
  var editContactForm = document.getElementById('formEditContact');
  // Contact Form Validation
  var fv_edit_contact = FormValidation.formValidation(editContactForm, {
    fields: {
      site_contact_fullname: {
        validators: {
          notEmpty: {
            message: 'Please enter contact full name'
          }
        }
      },
      site_contact_email: {
        validators: {
          emailAddress: {
            message: 'Please enter valid email address'
          }
        }
      },
      site_contact_mobile_phone: {
        validators: {
          regexp: {
            message: 'Please enter a valid contact mobile phone',
            regexp: /^\+?[0-9]+$/,
          }
        }
      },
      site_contact_phone: {
        validators: {
          regexp: {
            message: 'Please enter a valid contact phone',
            regexp: /^\+?[0-9]+$/,
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
    var form_contact = $('#formEditContact');
    var url = "site-visit/master-data/site/contact/update/" + form_contact.attr('data-id');

    $.ajax({
      data: form_contact.serialize(),
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {
        dt_ajax_table_contact.draw();
        modal_edit_contact.modal('hide');

        if (response.status) {
          fv_edit_contact.resetForm(true);
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
        modal_edit_contact.modal('hide');
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
  // End Edit Contact Form

  // Active / Deactive contact status button handler
  $(document).on('click', '.dropdownMenuStatusUpdateContact', function () {
    var site_contact_id = $(this).data('id'),
      site_contact_status = $(this).data('status');

    if (site_contact_status == 2) {
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
          data: { site_contact_id: site_contact_id, site_contact_status: site_contact_status },
          type: 'POST',
          url: baseUrl + 'site-visit/master-data/site/contact/update-status',
          success: function success(response) {
            dt_ajax_table_contact.draw();
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

  // Delete contact button handler
  $(document).on('click', '#dropdownMenuDeleteContact', function () {
    var site_contact_id = $(this).data('id');

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
          data: { site_contact_id: site_contact_id },
          type: 'POST',
          url: baseUrl + 'site-visit/master-data/site/contact/delete',
          success: function success(response) {
            dt_ajax_table_contact.draw();
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

  // Clearing form data contact when modal hidden
  modal_manage_contact.on('hidden.bs.modal', function () {
    fv_add_contact.resetForm(true);
    fv_edit_contact.resetForm(true);
    $('#formAddContact').attr('data-method', 'add');
    $('#formAddContact').data('method', 'add');
  });

});
