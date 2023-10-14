/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var ac_client = $('.ac_client');
  var ac_site = $('.ac_site');
  var ac_visit_type = $('.ac_visit_type');
  var ac_partner = $('.ac_partner');

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
        $('#visit_order_province').append('<option value="">Choice</option>');

        var provinces = response.data;

        $.each(provinces, function (index, province) {
          $('#visit_order_province').append('<option value="' + province.id + '">' + province.name + '</option>');
        });

        // Memperbarui tampilan Select2 pada dropdown Province
        $('#visit_order_province').select2({
          placeholder: 'Choice',
          minimumInputLength: 0,
          dropdownParent: $('#visit_order_province').parent()
        });
        $('#visit_order_regency').select2({
          disabled: true,
        });
        $('#visit_order_district').select2({
          disabled: true,
        });
        $('#visit_order_village').select2({
          disabled: true,
        });
      } else {
        $('#visit_order_province').append('<option value="">' + response.data + '</option>');
        $('#visit_order_regency').select2({
          disabled: true,
        });
        $('#visit_order_district').select2({
          disabled: true,
        });
        $('#visit_order_village').select2({
          disabled: true,
        });
      }
    }
  });

  // Mengubah opsi pilihan pada dropdown Regency berdasarkan Province yang dipilih
  $('#visit_order_province').on('change', function () {
    var provinceId = $(this).val();

    // Jika Province tidak dipilih, kosongkan juga dropdown District dan Village
    if (provinceId === '') {
      $('#visit_order_regency').empty();
      $('#visit_order_district').empty();
      $('#visit_order_village').empty();
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
          var currentRegency = $('#visit_order_regency').val();

          // Hapus opsi lama pada dropdown Regency
          $('#visit_order_regency').empty();

          $('#visit_order_regency').append('<option value="">Choice</option>');
          $('#visit_order_district').append('<option value="">Choice</option>');
          $('#visit_order_village').append('<option value="">Choice</option>');

          $.each(regencies, function (index, regency) {
            $('#visit_order_regency').append('<option value="' + regency.id + '">' + regency.name + '</option>');
          });

          // Pilih kembali opsi Regency yang sebelumnya dipilih (jika ada)
          if (currentRegency) {
            $('#visit_order_regency').val(currentRegency);
          }

          // Perbarui tampilan Select2 pada dropdown Regency
          $('#visit_order_regency').select2({
            placeholder: 'Choice',
            minimumInputLength: 0,
            disabled: false,
            dropdownParent: $('#visit_order_regency').parent()
          });

          $('#visit_order_regency').trigger('change');
        } else {
          $('#visit_order_regency').empty();
          $('#visit_order_district').empty();
          $('#visit_order_village').empty();
          $('#visit_order_regency').select2({
            disabled: true,
          });
          $('#visit_order_district').select2({
            disabled: true,
          });
          $('#visit_order_village').select2({
            disabled: true,
          });
        }
      }
    });
  });

  // Mengubah opsi pilihan pada dropdown District berdasarkan Regency yang dipilih
  $('#visit_order_regency').on('change', function () {
    var regencyId = $(this).val();

    // Jika Regency tidak dipilih, kosongkan juga dropdown Village
    if (regencyId === '') {
      $('#visit_order_district').empty();
      $('#visit_order_village').empty();
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
          var currentDistrict = $('#visit_order_district').val();

          // Hapus opsi lama pada dropdown District
          $('#visit_order_district').empty();

          $('#visit_order_district').append('<option value="">Choice</option>');

          // Mengisi opsi pilihan pada dropdown District
          $.each(districts, function (index, district) {
            $('#visit_order_district').append('<option value="' + district.id + '">' + district.name + '</option>');
          });

          // Pilih kembali opsi District yang sebelumnya dipilih (jika ada)
          if (currentDistrict) {
            $('#visit_order_district').val(currentDistrict);
          }

          // Perbarui tampilan Select2 pada dropdown District
          $('#visit_order_district').select2({
            placeholder: 'Choice',
            minimumInputLength: 0,
            disabled: false,
            dropdownParent: $('#visit_order_district').parent()
          });

          $('#visit_order_district').trigger('change');
        } else {
          $('#visit_order_district').empty();
          $('#visit_order_village').empty();
          $('#visit_order_district').select2({
            disabled: true,
          });
          $('#visit_order_village').select2({
            disabled: true,
          });
        }
      }
    });
  });

  // Mengubah opsi pilihan pada dropdown Village berdasrkan District yang dipilih
  $('#visit_order_district').on('change', function () {
    var districtId = $(this).val();

    // Jika District tidak dipilih, kosongkan dropdown Village
    if (districtId === '') {
      $('#visit_order_village').empty();
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
          var currentVillage = $('#visit_order_village').val();

          // Hapus opsi lama pada dropdown Village
          $('#visit_order_village').empty();

          $.each(villages, function (index, village) {
            $('#visit_order_village').append('<option value="' + village.id + '">' + village.name + '</option>');
          });

          // Pilih kembali opsi Village yang sebelumnya dipilih (jika ada)
          if (currentVillage) {
            $('#visit_order_village').val(currentVillage);
          }

          // Perbarui tampilan Select2 pada dropdown Village
          $('#visit_order_village').select2({
            placeholder: 'Choice',
            disabled: false,
            minimumInputLength: 0,
            dropdownParent: $('#visit_order_village').parent()
          });

          $('#visit_order_village').trigger('change');
        } else {
          $('#visit_order_village').empty();
          $('#visit_order_village').select2({
            disabled: true,
          });
        }
      }
    });
  });

  $(function () {
    // Form sticky actions
    var topSpacing;
    const stickyEl = $('.sticky-element');

    // Init custom option check
    window.Helpers.initCustomOptionCheck();

    // Set topSpacing if the navbar is fixed
    if (Helpers.isNavbarFixed()) {
      topSpacing = $('.layout-navbar').height() + 7;
    } else {
      topSpacing = 0;
    }

    // sticky element init (Sticky Layout)
    if (stickyEl.length) {
      stickyEl.sticky({
        topSpacing: topSpacing,
        zIndex: 9
      });
    }

  });

  // datepicker
  $(".dt").flatpickr({
    monthSelectorType: 'static'
  });

  // textarea
  const textarea = document.querySelector('#autosize-note');
  const location = document.querySelector('#autosize-location');

  // Autosize
  // --------------------------------------------------------------------
  if (textarea) {
    autosize(textarea);
  }

  if (location) {
    autosize(location);
  }

  // Function to clear Site Contact field
  function clearSiteContactField() {
    $('#site_contact_id').val(null);
  }

  function clearSiteField() {
    var $siteField = ac_site;
    $siteField.val('').trigger('change');
  }

  // Select2 Site name handler
  if (ac_site.length) {
    var $this = ac_site;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select site',
      disabled: true,
    });
  }

  $("#site_contact_id").prop("disabled", true);
  $("#save_to_site_contact").prop("disabled", true);

  // Select2 client handler
  if (ac_client.length) {
    var $clientNameField = ac_client;
    $clientNameField.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select client',
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
      dropdownParent: $clientNameField.parent()
    }).on('change', function () {
      var $thisSite = ac_site;
      var clientId = $('#client_id').val();
      $thisSite.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select site',
        disabled: false,
        minimumInputLength: 0,
        ajax: {
          url: baseUrl + 'autocomplete/site/find-by-id/' + clientId,
          // url: baseUrl + 'autocomplete/site/find',
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
        dropdownParent: $thisSite.parent()
      });
      clearSiteField();
    });
  }

  // Select2 Site handler
  var selectedSiteId; // Declare a global variable to store the selected site_id

  if (ac_site.length) {
    clearSiteContactField();
    var $thisSite = ac_site;
    var clientId = $('#client_id').val();
    $thisSite.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select site',
      minimumInputLength: 0,
      ajax: {
        // url: baseUrl + 'autocomplete/site/find',
        url: baseUrl + 'autocomplete/site/find-by-id/' + clientId,
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
      dropdownParent: $thisSite.parent()
    }).on('change', function () {
      clearSiteContactField();
      // Enable the input field and the button
      $("#site_contact_id").prop("disabled", false);
      $("#save_to_site_contact").prop("disabled", false);

      selectedSiteId = $('#site_id').val();

      getAllSiteContactData(selectedSiteId);
    });
  }

  // Select2 visit type handler
  if (ac_visit_type.length) {
    var $this = ac_visit_type;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select visit type',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/visit-type/find',
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

  // Select2 partner handler
  if (ac_partner.length) {
    var $this = ac_partner;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select partner',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/partner/find',
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

  // Add Form
  var add_visit_order_form = document.getElementById('formAddVisitOrder');
  // visit order Form Validation
  var fv = FormValidation.formValidation(add_visit_order_form, {
    fields: {
      client_id: {
        validators: {
          notEmpty: {
            message: 'Please select client'
          }
        }
      },
      site_id: {
        validators: {
          notEmpty: {
            message: 'Please select site'
          }
        }
      },
      site_contact_id: {
        validators: {
          notEmpty: {
            message: 'Please select site contact name'
          }
        }
      },
      debtor_id: {
        validators: {
          notEmpty: {
            message: 'Please enter debtor'
          }
        }
      },
      visit_type_id: {
        validators: {
          notEmpty: {
            message: 'Please select visit type'
          }
        }
      },
      partner_id: {
      },
      visit_order_number: {
        validators: {
          notEmpty: {
            message: 'Please enter order number'
          }
        }
      },
      visit_order_location: {
        validators: {
          notEmpty: {
            message: 'Please enter order location'
          }
        }
      },
      visit_order_date: {
        validators: {
          notEmpty: {
            message: 'Please enter order date'
          }
        }
      },
      visit_order_due_date: {
        validators: {
          notEmpty: {
            message: 'Please enter order due date'
          },
          date: {
            format: 'YYYY-MM-DD',
            message: 'The order due date is not a valid date'
          },
          callback: {
            message: 'The order due date must be after or equal to the order date',
            callback: function (input) {
              var startDate = new Date(add_visit_order_form.querySelector('[name="visit_order_date"]').value);
              var endDate = new Date(input.value);

              // Mengubah tanggal menjadi bilangan bulan dalam setahun
              var startMonth = startDate.getFullYear() * 12 + startDate.getMonth();
              var endMonth = endDate.getFullYear() * 12 + endDate.getMonth();

              return endDate >= startDate && endMonth >= startMonth;
            }
          }

        }
      },
      visit_order_note: {
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
    var btn_submit = document.querySelectorAll("#visit_order_submit");
    for (let i = 0; i < btn_submit.length; i++) {
      btn_submit[i].setAttribute('disabled', true);
    }
    // Adding visit order when form successfully validate
    if ($('#formAddVisitOrder').data('method') == 'add') {
      var url = "site-visit/visit-order-create/store";
    } else {
      var url = "";
    }

    var form_data = {
      client_id: $('#client_id').val(),
      site_id: $('#site_id').val(),
      site_contact_id: $('#site_contact_id').data('selected-id'),
      debtor_id: $('#debtor_id').data('selected-id'),
      visit_type_id: $('#visit_type_id').val(),
      partner_id: $('#partner_id').val(),
      visit_order_number: $('#visit_order_number').val(),
      visit_order_custom_number: $('#visit_order_custom_number').val(),
      visit_order_date: $('#visit_order_date').val(),
      visit_order_due_date: $('#visit_order_due_date').val(),
      visit_order_location: $('#autosize-location').val(),
      visit_order_province: $('#visit_order_province').val(),
      visit_order_regency: $('#visit_order_regency').val(),
      visit_order_district: $('#visit_order_district').val(),
      visit_order_location_map: $('#visit_order_location_map').val(),
      visit_order_latitude: $('#visit_order_latitude').val(),
      visit_order_longitude: $('#visit_order_longitude').val(),
      visit_order_note: $('#autosize-note').val(),
    };

    $.ajax({
      // data: $('#formAddVisitOrder').serialize(),
      data: form_data, // harusnya pake $('#formAddVisitOrder').serialize(), ini juga cukup tapi karena ada autocomplete jadi harus manual bikin site_form
      url: baseUrl + url,
      type: 'POST',
      success: function success(response) {

        if (response.status) {
          Swal.fire({
            icon: 'success',
            title: response.message.title,
            text: response.message.text,
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });

          // jika sudah sukses maka clear seluruh form
          $('#client_id').val('').trigger('change');
          $('#site_id').val('').trigger('change');
          $('#site_contact_id').val(null);
          $('#debtor_id').val(null);
          $('#visit_type_id').val('').trigger('change');
          $('#partner_id').val('').trigger('change');
          $('#visit_order_date').val(null);
          $('#autosize-location').val(null);
          $('#visit_order_location_map').val(null);
          $('#visit_order_latitude').val(null);
          $('#visit_order_longitude').val(null);
          $('#autosize-note').val(null);
          $('#visit_order_custom_number').val(null);
          $('#visit_order_province').val('').trigger('change');
          $('#visit_order_regency').val('').trigger('change');
          $('#visit_order_district').val('').trigger('change');
          $('#visit_order_village').val('').trigger('change');

          for (let i = 0; i < btn_submit.length; i++) {
            btn_submit[i].removeAttribute('disabled');
          }

          // Me-refresh halaman setelah 1 detik
          setTimeout(function () {
            location.reload();
          }, 1000);
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
  // End Add Form

  $(document).ready(function () {
    $('#debtor_id').autocomplete({
      source: function (request, response) {
        $.ajax({
          url: baseUrl + 'autocomplete/get-all-debtor',
          type: 'GET',
          dataType: 'json',
          data: { search: request.term },
          success: function (data) {
            // If there are results, show all debtor names, else show "No results found"
            if (data.length > 0) {
              response(data);
            } else {
              response([{ id: -1, value: 'No results found', label: 'No results found' }]);
            }
          },
          error: function (err) {
            console.error(err);
          },
        });
      },
      minLength: 0, // Allow empty searches
      select: function (event, ui) {
        event.preventDefault();

        // Check if the selected item is the "No results found" item (ID: -1)
        // if (ui.item.id !== -1) {
        //     $(this).val(ui.item.value);
        // }

        if (ui.item.id !== -1) {
          $(this).val(ui.item.label); // Set the input value to the selected debtor's label (name)
          $(this).data('selected-id', ui.item.id); // Store the selected debtor's ID as a data attribute
        }
      },
      open: function () {
        $(this).autocomplete("widget").addClass('dropdown-menu').css('max-height', '200px').css('overflow-y', 'auto');
      },
      focus: function (event, ui) {
        // Prevent the default behavior to stop the value from being set when an item is focused
        event.preventDefault();
      },
      autoFocus: true, // Automatically focus the first item when the menu is shown
    }).on('focus', function () {
      // Trigger the search manually when the input is focused
      $(this).autocomplete("search");
    });
  });

  // Handle the click event on the save button
  $(document).on('click', '#save_to_debtor', function () {
    var debtor_name = $('#debtor_id').val();
    var debtor_id = $('#debtor_id').data('selected-id'); // Retrieve the selected debtor's ID

    if (debtor_name.trim() !== '') {
      // Show SweetAlert confirmation before saving the data
      Swal.fire({
        title: 'Confirmation',
        text: 'Are you sure you want to save debtor with name: ' + debtor_name + ' ?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
      }).then(function (result) {
        if (result.isConfirmed) {
          // If user confirms, proceed with saving data
          $.ajax({
            url: baseUrl + 'site-visit/visit-order-create/store-to-debtor',
            type: 'POST',
            dataType: 'json',
            data: {
              debtor_id: debtor_id,
              debtor_name: debtor_name
            },
            success: function (response) {
              if (response.status === true) {
                Swal.fire({
                  icon: 'success',
                  title: response.message.title,
                  text: response.message.text,
                  customClass: {
                    confirmButton: 'btn btn-success'
                  }
                });
                // Optionally, you can update the input value with the newly created debtor
                $('#debtor_id').val(response.debtor_name);
              } else {
                // jika debtor sudah ada
                Swal.fire({
                  icon: 'info',
                  title: response.message.title,
                  text: response.message.text,
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  }
                });
              }
            },
            error: function (err) {
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
    } else {
      // Show an error message that the debtor name must be filled
      Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'Please input a debtor name before saving.',
        customClass: {
          confirmButton: 'btn btn-primary'
        }
      });
    }
  });

  function getAllSiteContactData(selectedSiteId) {
    $('#site_contact_id').autocomplete({
      source: function (request, response) {
        $.ajax({
          url: baseUrl + 'autocomplete/get-all-site-contact/find/' + selectedSiteId,
          type: 'GET',
          dataType: 'json',
          disabled: false,
          data: { search: request.term },
          success: function (data) {
            // If there are results, show all site contact names, else show "No results found"
            if (data.length > 0) {
              response(data);
            } else {
              response([{ id: -1, value: 'No results found', label: 'No results found' }]);
            }
          },
          error: function (err) {
            console.error(err);
          },
        });
      },
      minLength: 0, // Allow empty searches
      select: function (event, ui) {
        event.preventDefault();

        // Check if the selected item is the "No results found" item (ID: -1)
        // if (ui.item.id !== -1) {
        //     $(this).val(ui.item.value);
        // }

        if (ui.item.id !== -1) {
          $(this).val(ui.item.label); // Set the input value to the selected site contact's label (name)
          $(this).data('selected-id', ui.item.id); // Store the selected site contact's ID as a data attribute
        }
      },
      open: function () {
        $(this).autocomplete("widget").addClass('dropdown-menu').css('max-height', '200px').css('overflow-y', 'auto');
      },
      focus: function (event, ui) {
        // Prevent the default behavior to stop the value from being set when an item is focused
        event.preventDefault();
      },
      autoFocus: true, // Automatically focus the first item when the menu is shown
    }).on('focus', function () {
      // Trigger the search manually when the input is focused
      $(this).autocomplete("search");
    });
  }

  // Handle the click event on the save button
  $(document).on('click', '#save_to_site_contact', function () {
    var site_contact_fullname = $('#site_contact_id').val();
    var site_contact_id = $('#site_contact_id').data('selected-id'); // Retrieve the selected site contact's ID

    if (site_contact_fullname.trim() !== '') {
      // Show SweetAlert confirmation before saving the data
      Swal.fire({
        title: 'Confirmation',
        text: 'Are you sure you want to save site contact with name: ' + site_contact_fullname + ' ?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
      }).then(function (result) {
        if (result.isConfirmed) {
          // If user confirms, proceed with saving data
          $.ajax({
            url: baseUrl + 'site-visit/visit-order-create/store-to-site-contact',
            type: 'POST',
            dataType: 'json',
            data: {
              site_contact_id: site_contact_id,
              site_id: selectedSiteId, // var global bray
              site_contact_fullname: site_contact_fullname
            },
            success: function (response) {
              if (response.status === true) {
                Swal.fire({
                  icon: 'success',
                  title: response.message.title,
                  text: response.message.text,
                  customClass: {
                    confirmButton: 'btn btn-success'
                  }
                });
                // Optionally, you can update the input value with the newly created site contact name
                $('#site_contact_id').val(response.site_contact_fullname);

              } else {
                // jika site contact name sudah ada
                Swal.fire({
                  icon: 'info',
                  title: response.message.title,
                  text: response.message.text,
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  }
                });
              }
            },
            error: function (err) {
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
    } else {
      // Show an error message that the site contact name must be filled
      Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'Please input a site contact name before saving.',
        customClass: {
          confirmButton: 'btn btn-primary'
        }
      });
    }
  });

  // location-maxlength & repeater (jquery)
  $(function () {
    var maxlengthInput = $('.location-maxlength');

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

});
