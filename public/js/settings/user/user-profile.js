/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {

  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var modal_edit_user = $('#modalEditUser');
  var modal_class_loader = $('.modal-block-loader');
  
  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  });
  
  var editUserForm = document.getElementById('formEditUser');
  // edit user form validation
  var fvEdit = FormValidation.formValidation(editUserForm, {
    fields: {
      user_uniq_name: {
       validators: {
         notEmpty: {
           message: 'Please enter your full name'
         }
       }
      },
      user_photo : {
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
    var url = "settings/user/update-user/" + $('#formEditUser').attr('data-id');
    var form_data = new FormData(editUserForm); 

    $.ajax({
      data: form_data,
      url: baseUrl + url,
      type: 'POST',
      processData: false,
      contentType: false,
      success: function success(response) {
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

          // Me-refresh halaman setelah 1 detik
          setTimeout(function() {
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
      url: baseUrl + "settings/user/show/" + user_id,
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {
        $('#edit_user_uniq_name').val(response.data.user_uniq_name);
        $('#edit_role').val(response.data.role.role_id);

        // Display current photo
        if (response.data.user_photo) {
          var photoUrl = baseUrl + 'settings/user/uploads/' + response.data.user_id;
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

    $('#formEditUser').attr('data-method', 'edit');
    $('#formEditUser').data('method', 'edit');
    $('#formEditUser').attr('data-id', user_id);

    modal_edit_user.modal('show');
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
  

 }); 