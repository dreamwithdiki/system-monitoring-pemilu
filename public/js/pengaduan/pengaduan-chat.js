/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

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

  // Autosize
  // --------------------------------------------------------------------
  if (textarea) {
    autosize(textarea);
  }

  // Add Form
  var add_pengaduan_form = document.getElementById('formAddPengaduan');
  // visit order Form Validation
  var fv = FormValidation.formValidation(add_pengaduan_form, {
    fields: {
      pengaduan_note: {
        validators: {
          notEmpty: {
            message: 'Please enter pengaduan'
          }
        }
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
    var btn_submit = document.querySelectorAll("#pengaduan_submit");
    for (let i = 0; i < btn_submit.length; i++) {
      btn_submit[i].setAttribute('disabled', true);
    }
    // Adding visit order when form successfully validate
    if ($('#formAddPengaduan').data('method') == 'add') {
      var url = "pengaduan/chat/store";
    } else {
      var url = "";
    }

    $.ajax({
      data: $('#formAddPengaduan').serialize(),
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
          $('#autosize-note').val(null);

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
