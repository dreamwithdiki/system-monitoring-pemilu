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

  // Function to disable email validation
  function disableEmailValidation() {
    fv.disableValidator('user_email', 'notEmpty');
    fv.disableValidator('user_email', 'emailAddress');
    fv.disableValidator('user_uniq_name', 'notEmpty');
    fv.enableValidator('user_ref_id', 'notEmpty');
  }

  // Function to disable partner email validation
  function disablePartnerEmailValidation() {
    fv.disableValidator('user_ref_id', 'notEmpty');
    fv.enableValidator('user_uniq_name', 'notEmpty');
    fv.enableValidator('user_email', 'notEmpty');
    fv.enableValidator('user_email', 'emailAddress');
  }

  // Function to initialize the Select2 for partner name input
  function initializePartnerSelect2() {
    $('#partner_name').select2({
      placeholder: 'Select Partner Name',
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
      dropdownParent: $('#partnerNameField')
    });
  }

  // Document ready function
  $(document).ready(function() {
    // Handle the role selection change
    $('#role_id').on('change', function() {
      var selectedRoleId = $(this).val();
      var userEmailInput = $('#user_email');

      if (selectedRoleId === '3') { // Partner
        // Hide the regular email input
        $('#emailField').hide();
        // Show the partner email input
        $('#partnerNameField').show();

        // Fetch and initialize the partner email Select2
        initializePartnerSelect2();

        $('#user_email').val('');
        $('#user_email').attr('disabled', true);
        $('#user_uniq_name').attr('disabled', true);
        disableEmailValidation();
      } else { // Superadmin or Admin
        // Show the regular email input
        $('#emailField').show();
        // Hide the partner email input
        $('#partnerNameField').hide();

        $('#user_uniq_name').attr('disabled', false);
        $('#user_email').attr('disabled', false);
        $('#user_email').attr('required', true);
        disablePartnerEmailValidation();
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
        url: baseUrl + 'settings/user/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'settings/user/get');
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
          targets: 6,
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
          targets: 7,
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
      dom: '<"row"<"col-sm-12 col-md-6"l>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
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
      user_uniq_name: {
       validators: {
         notEmpty: {
           message: 'Please enter your full name'
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
            min: 8,
            message: 'Password must be more than 8 characters'
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
    var url = "settings/user/store";
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
      user_uniq_name: {
       validators: {
         notEmpty: {
           message: 'Please enter your full name'
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
    var url = "settings/user/update/" + $('#formEditUser').attr('data-id');
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
      url: baseUrl + "settings/user/show/" + user_id,
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
  
        $('#edit_user_uniq_name').val(response.data.user_uniq_name);
        if (response.data.role) {
          var option = new Option(response.data.role.role_name, response.data.role.role_id, true, true);
          $('#edit_role').append(option).trigger('change');
        }
  
        var selectedRoleId = response.data.role ? response.data.role.role_id : '';
        var userEmailInput = $('#edit_user_email');
  
        if (selectedRoleId.toString() === '3') { // Partner
          // Hide the regular email input
          $('#editemailField').hide();
          // Show the partner email input
          $('#editpartnerNameField').show();
  
          // Fetch and initialize the partner email Select2 for edit
          EditPartnerSelect2();
  
          // Clear the email input value for role_id 1 and 2
          userEmailInput.val('');
  
          // Disable the regular email input
          userEmailInput.prop('disabled', true);
          // Enable partner email input
          $('#edit_partner_name').prop('disabled', false);
  
          // Set the partner name value
          if (response.data.partner) {
            var partnerOptionText = response.data.partner.partner_name;
            var option = new Option(partnerOptionText, response.data.partner.partner_id, true, true);
            $('#edit_partner_name').append(option);
          
            // Trigger the change event to update the select2 plugin
            $('#edit_partner_name').trigger('change');
          } else {
            $('#edit_partner_name').empty();
            $('#edit_partner_name').append('<option value="">No Partner</option>').trigger('change');
          }
        } else { // Superadmin or Admin
          // Show the regular email input
          $('#editemailField').show();
          // Hide the partner email input
          $('#editpartnerNameField').hide();
  
          // Enable the regular email input and make it required
          userEmailInput.prop('disabled', false);
          userEmailInput.prop('required', true);
  
          // Set the regular email value
          $('#edit_user_email').val(response.data.user_email);
        }
  
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
          url: baseUrl + 'settings/user/update-status',
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
          url: baseUrl + 'settings/user/delete',
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
    $('#user_email').val(null);
    $('#user_password').val(null);
    $('#confirm_password').val(null);
    $('#user_photo').val(null);
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


  // Function to initialize the Select2 for partner email input
  function EditPartnerSelect2() {
    $('#edit_partner_name').select2({
      placeholder: 'Select Partner Email',
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
      dropdownParent: $('#editpartnerNameField')
    });
  }


  // Function to disable email validation
  function disableEditEmailValidation() {
    fvEdit.disableValidator('user_email', 'notEmpty');
    fvEdit.disableValidator('user_email', 'emailAddress');
    fvEdit.enableValidator('user_ref_id', 'notEmpty');
  }

  // Function to disable partner email validation
  function disableEditPartnerEmailValidation() {
    fvEdit.disableValidator('user_ref_id', 'notEmpty');
    fvEdit.enableValidator('user_email', 'notEmpty');
    fvEdit.enableValidator('user_email', 'emailAddress');
  }

  // Document ready function
  $(document).ready(function() {
    // Handle the role selection change
    $('#edit_role').on('change', function() {
      var selectedRoleId = $(this).val();

      if (selectedRoleId.toString() === '3') { // Partner
        // Hide the regular email input
        $('#editemailField').hide();
        // Show the partner email input
        $('#editpartnerNameField').show();

        // Fetch and initialize the partner email Select2
        EditPartnerSelect2();

        $('#edit_user_email').val('');
        $('#edit_user_email').attr('disabled', true);
        disableEditEmailValidation();
      } else { // Superadmin or Admin
        // Show the regular email input
        $('#editemailField').show();
        // Hide the partner email input
        $('#editpartnerNameField').hide();

        $('#edit_user_email').attr('disabled', false);
        $('#edit_user_email').attr('required', true);
        disableEditPartnerEmailValidation();
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