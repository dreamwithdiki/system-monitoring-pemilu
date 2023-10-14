/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var ac_client = $('.ac_client');
  var ac_job_type = $('.ac_job_type');
  var ac_partner = $('.ac_partner');
  var ac_merchant = $('.ac_merchant');

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
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
    })
  }

  // Select2 job type handler
  if (ac_job_type.length) {
    var $this = ac_job_type;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select job type',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/job-type/find',
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

  // Select2 merchant handler
  if (ac_merchant.length) {
    var $this = ac_merchant;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select merchant',
      minimumInputLength: 0,
      ajax: {
        url: baseUrl + 'autocomplete/merchant/find',
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

  // Mengubah opsi pilihan pada dropdown Regency berdasarkan Province yang dipilih
  $('#merchant_id').on('change', function () {
    var merchant_id = $(this).val();

    // get data
    $.ajax({
      url: baseUrl + "job-order/master-data/merchant/show/" + merchant_id,
      type: 'GET',
      success: function (response) {
        
        $('#job_order_province').val(response.data.province.name);
        $('#job_order_regency').val(response.data.regency.name);
        $('#job_order_location_map').val(response.data.merchant_location_map);

        // Set the latitude and longitude for the marker
        var latitude = parseFloat(response.data.merchant_latitude);
        var longitude = parseFloat(response.data.merchant_longitude);

        $('#job_order_latitude').val(isNaN(latitude) ? '' : latitude);
        $('#job_order_longitude').val(isNaN(longitude) ? '' : longitude);

        // Call initMap here with the updated values
        initMap();
      }
    });
  });

  // Add Form
  var add_visit_order_form = document.getElementById('formAddJobOrder');
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
      job_type_id: {
        validators: {
          notEmpty: {
            message: 'Please select job type'
          }
        }
      },
      merchant_id: {
        validators: {
          notEmpty: {
            message: 'Please select job type'
          }
        }
      },
      partner_id: {
      },
      job_order_number: {
        validators: {
          notEmpty: {
            message: 'Please enter order number'
          }
        }
      },
      job_order_date: {
        validators: {
          notEmpty: {
            message: 'Please enter order date'
          }
        }
      },
      job_order_due_date: {
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
      job_order_note: {
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
    var btn_submit = document.querySelectorAll("#job_order_submit");
    for (let i = 0; i < btn_submit.length; i++) {
      btn_submit[i].setAttribute('disabled', true);
    }
    // Adding visit order when form successfully validate
    if ($('#formAddJobOrder').data('method') == 'add') {
      var url = "job-order/job-order-create/store";
    } else {
      var url = "";
    }

    $.ajax({
      data: $('#formAddJobOrder').serialize(),
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
          $('#job_type_id').val('').trigger('change');
          $('#partner_id').val('').trigger('change');
          $('#merchant_id').val('').trigger('change');
          $('#job_order_date').val(null);
          $('#job_order_due_date').val(null);
          $('#job_order_location_map').val(null);
          $('#job_order_latitude').val(null);
          $('#job_order_longitude').val(null);
          $('#job_order_province').val(null);
          $('#job_order_regency').val(null);
          $('#autosize-note').val('');

          for (let i = 0; i < btn_submit.length; i++) {
            btn_submit[i].removeAttribute('disabled');
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
