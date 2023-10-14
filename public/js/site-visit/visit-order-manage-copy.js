/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  var dt_ajax_table = $('.datatables-ajax');
  var modal_detail_visit_order = $('#modalDetailVisitOrder');
  var modal_checklist = $('#modalChecklist');
  var modal_class_loader = $('.modal-block-loader');
  
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  });

  // Data Table
  if (dt_ajax_table.length) {
    var dt_ajax = dt_ajax_table.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: baseUrl + 'site-visit/visit-order-manage/get',
        beforeSend: function () {
          window.Helpers.blockUIPageLoader(baseUrl + 'site-visit/visit-order-manage/get');
        },
        complete: function () {
          $.unblockUI();
        },
        error: function (jqXHR, textStatus, errorThrown) {
          // Do something here
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
      },
      columns: [
        { data: 'no', orderable: false },
        { data: 'visit_order_number', orderable: false},
        { data: 'visit_order_date' },
        { data: 'client_name' },
        { data: 'site_name' },
        { data: 'visit_order_location' },
        { data: 'partner_name' },
        { data: 'visit_order_status', orderable: false },
      ],
      columnDefs: [
        {
          targets: 7,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
            if (data == 1) {
              return '<span class="badge bg-label-secondary me-1">Open</span>';
            } else if (data == 2) {
                return '<span class="badge bg-label-warning me-1">Assigned</span>';
            } else if (data == 3) {
                return '<span class="badge bg-label-danger me-1">Cancelled</span>';
            } else if (data == 4) {
                return '<span class="badge bg-label-info me-1">Revisit</span>';
            } else if (data == 5) {
                return '<span class="badge bg-label-success me-1">Done</span>';
            } else if (data == 6) {
                return '<span class="badge bg-label-primary me-1">Validated</span>';
            } else {
              return '<span class="badge bg-label-danger me-1">Unknown</span>';
            }
          }
        },
        {
          targets: 8,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
              return '' +
              // '<button id="buttonMenuDetail" class="btn btn-sm btn-icon btn-primary mx-1" data-id="' + row.visit_order_id + '" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="Detail ' + row.visit_order_number + '"><i class="bx bx-detail me-1"></i></button>';
              '<div class="d-inline-block text-nowrap">' +
                  '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                  '<div class="dropdown-menu">' +
                      '<a id="dropdownMenuDetail" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-detail me-1"></i> Detail</a>' +
                      '<div class="dropdown-divider"></div>' +
                      '<a id="dropdownMenuChecklist" data-id="' + row.visit_order_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-list-check me-1"></i> Checklist</a>' +
                  '</div>' +
              '</div>';
          }
        }
      ],
      order: [[1, 'asc']],
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
    });
  }
  
  // Detail button handler
  var visit_order_id; // variabel global untuk menampung nilai visit_order_id

  $(document).on('click', '#dropdownMenuDetail', function () {
    visit_order_id = $(this).data('id');
    visit_order_id_global = visit_order_id; // ditangkap di Visual File Data

    // get data
    $.ajax({
      url: baseUrl + "site-visit/visit-order-manage/show/" + visit_order_id,
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {

        $('#detNumber').text(response.data.visit_order_number);
        $('#detDate').text(response.data.visit_order_date);
        $('#detLocation').text(response.data.visit_order_location);
        $('#detClientName').text(response.data.client.client_name);
        $('#detSiteName').text(response.data.site.site_name);
        $('#detPartnerName').text(response.data.partner.partner_name);
        $('#detDesc').text(response.data.visit_order_note);
        modal_class_loader.unblock();
      }
    });

    $('#detFormLabel > p').html('Detail Visit Order.');
    $('#formDetVisitOrder').attr('data-method', 'detail');
    $('#formDetVisitOrder').data('method', 'detail');
    $('#formDetVisitOrder').attr('data-id', visit_order_id);
    modal_detail_visit_order.modal('show');
  });

  // Fungsi untuk mengambil data checklist dari response
  function getChecklistData(response) {
    var checklistGroups = response.checklist_groups;
    var checklists = response.checklists;
    var checklistAnswers = response.checklist_answers;

    // Bersihkan konten yang ada sebelumnya coy
    var groupContainer = document.getElementById('checkboxContainer');
    groupContainer.innerHTML = '';

    checklistGroups.forEach(function(checklistGroup) {
      // Tampilkan nama grup checklist
      var groupNameElement = document.createElement('h5');
      groupNameElement.textContent = checklistGroup.checklist_group_name;
      groupNameElement.style.marginTop = '10px';
      groupContainer.appendChild(groupNameElement);

      // Tampilkan checklist
      var rowContainer = document.createElement('div');
      rowContainer.classList.add('row');
      groupContainer.appendChild(rowContainer);

      var groupChecklists = checklists.filter(function(checklist) {
        return checklist.checklist_group_id === checklistGroup.checklist_group_id;
      });

      groupChecklists.forEach(function(checklist, index) {
        var checkboxLabel = document.createElement('label');
        checkboxLabel.classList.add('checkbox-container');
        checkboxLabel.textContent = checklist.checklist_name;
        checkboxLabel.style.marginBottom = '10px';

        var checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.name = 'checklist';
        checkbox.value = checklist.checklist_id;
        checkbox.classList.add('form-check-input');

        if (checklist.checklist_is_freetext === 2) {
          // Jika checklist_is_freetext = 2, tambahkan input text
          var inputText = document.createElement('input');
          inputText.type = 'text';
          inputText.name = 'checklistText';
          inputText.placeholder = 'Lain-lain';
          inputText.classList.add('form-control');

          checkboxLabel.appendChild(checkbox);
          checkboxLabel.appendChild(inputText);
        } else {
          checkboxLabel.appendChild(checkbox);
        }

        // Tambahkan container form-check untuk tampilan yang bagus
        var formCheckContainer = document.createElement('div');
        formCheckContainer.classList.add('form-check', 'form-check-primary', 'mt-3');
        formCheckContainer.appendChild(checkboxLabel);

        // Tentukan posisi checkbox menggunakan modulus 2
        if (index % 2 === 0) {
          // Jika index genap, tambahkan ke kolom pertama
          var colContainer = document.createElement('div');
          colContainer.classList.add('col');
          colContainer.appendChild(formCheckContainer);
          rowContainer.appendChild(colContainer);
        } else {
          // Jika index ganjil, tambahkan ke kolom kedua
          var lastColContainer = rowContainer.lastElementChild;
          lastColContainer.appendChild(formCheckContainer);
        }

        // Cek apakah checklist sudah tercheck di tabel sys_checklist_answer
        checklistAnswers.forEach(function(checklistAnswer) {
            if (checklistAnswer.checklist_id === checklist.checklist_id) {
                checkbox.checked = true;
            }
        });
        
      });
    });
  }

  // Event handler saat tombol dropdown di klik
  $(document).on('click', '#dropdownMenuChecklist', function () {
    visit_order_id = $(this).data('id');
    visit_order_id_global = visit_order_id;

    // Ambil data dari server menggunakan AJAX
    var modalContent = $('.modal-content');
    modalContent.addClass('modal-block-loader');

    $.ajax({
      url: baseUrl + "site-visit/visit-order-manage/checklist",
      type: 'GET',
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {
        // console.log(response);

        // Tampilkan data checklist
        getChecklistData(response);

        // Hilangkan loader dan hapus kelas CSS
        modalContent.removeClass('modal-block-loader');
        modal_class_loader.unblock();
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

        // Hapus kelas CSS dan hilangkan loader
        modalContent.removeClass('modal-block-loader');
        modal_class_loader.unblock();
      }
    });

    $('#checklistFormLabel > p').html('Data Checklist.');
    $('#formChecklistVisitOrder').attr('data-method', 'detail');
    $('#formChecklistVisitOrder').data('method', 'detail');
    $('#formChecklistVisitOrder').attr('data-id', visit_order_id);
    modal_checklist.modal('show');
  });

  $(document).on('click', '#saveChangesButton', function() {
    // Ambil semua checkbox yang dicentang
    var checkboxes = document.querySelectorAll('input[name="checklist"]:checked');

    // Buat array utk menyimpan nilai-nilai yang akan disimpan atau diperbaharui
    var selectedChecklists = [];
    // loop melalui checkbox yang dicentang
    checkboxes.forEach(function(checkbox){
      var checklistId = checkbox.value;
      var checklistText = '';

      // Jika checkbox memiliki input teks terkait, ambil nilainya
      if (checkbox.nextSibling && checkbox.nextSibling.tagName === 'INPUT') {
        checklistText = checkbox.nextSibling.value;
      }

      // Buat objek untuk setiap checklist yg dicentang
      var selectedChecklist = {
        checklistId: checklistId,
        checklistText: checklistText
      };

      // Tambahkan objek ke dalam array
      selectedChecklists.push(selectedChecklist);
    });

    // Kirim perminataan AJAX utk menyimpan atau memperbaharui data checklist
    $.ajax({
      url: baseUrl + "site-visit/visit-order-manage/checklist/save",
      type: "POST",
      data: {
        visitOrderId: visit_order_id,
        checklists: selectedChecklists
      },
      beforeSend: function(data) {
        window.Helpers.blockUIModalLoader(modal_class_loader);
      },
      success: function(response) {
        // tindakan yg diambil setelah berhasil menyimpan / memperbaharui data checklist
        Swal.fire({
          icon: 'success',
          title: response.message.title,
          text: response.message.text,
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });

        modal_class_loader.unblock();
      },
      error: function(err){
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
  
   /**
   * 
   * Visual File Data
   * 
   */

   var dt_ajax_table_visual_file = $('.datatables-ajax-visual');
   var visit_order_id_global = visit_order_id;
 
   // Data table visual
   if (dt_ajax_table_visual_file.length) {
     var dt_ajax_visual_file = dt_ajax_table_visual_file.DataTable({
       processing: true,
       serverSide: true,
       ajax: {
         url: baseUrl + 'site-visit/visit-order-manage/visual-file/get',
         data: function(d) {
           d.visit_order_id = visit_order_id_global;
         }
       },
       columns: [
         { data: 'no', orderable: false }, 
         { data: 'visual_file_name' },
         {
           data: 'visual_file',
           render: function(data, type, row, meta) {
             var visual = row.visual_file.split(',');
             var files = '';
             for (var i = 0; i < visual.length; i++) {
               var fileExt = visual[i].split('.').pop().toLowerCase();
               var url = baseUrl + 'site-visit/visit-order-manage/visual-file/uploads/' + row.visual_file_id;
               var pdf  = baseUrl + 'assets/img/icons/misc/custom-pdf.png';
               var word = baseUrl + 'assets/img/icons/misc/custom-ms-word.png';
               var excel= baseUrl + 'assets/img/icons/misc/custom-ms-excel.png';
               var ppt  = baseUrl + 'assets/img/icons/misc/custom-ms-ppt.png';
               
               url = url.replace(':filename', visual[i]);
               if (fileExt === 'jpg' || fileExt === 'jpeg' || fileExt === 'png') {
                 files += '<a href="' + url + '" target="_blank"><img src="' + url + '" width="50px" height="50px" title="'+ row.visual_file +'"/></a>';
               } else if (fileExt === 'pdf') {
                 files += '<a href="' + url + '" download="' + visual[i] + '" target="_blank"><img src="' + pdf + '" width="50px" height="50px" title="'+ row.visual_file +'"/></a>';
               } else if (fileExt === 'doc' || fileExt === 'docx') {
                 files += '<a href="' + url + '" download="' + visual[i] + '" target="_blank"><img src="' + word + '" width="50px" height="50px" title="'+ row.visual_file +'" /></a>';
               } else if (fileExt === 'xls' || fileExt === 'xlsx') {
                 files += '<a href="' + url + '" download="' + visual[i] + '" target="_blank"><img src="' + excel + '" width="50px" height="50px" title="'+ row.visual_file +'" /></a>';
               } else if (fileExt === 'ppt' || fileExt === 'pptx') {
                 files += '<a href="' + url + '" download="' + visual[i] + '" target="_blank"><img src="' + ppt + '" width="50px" height="50px" title="'+ row.visual_file +'" /></a>';
               } else {
                 files += '<span class="badge bg-danger">Unknown</span>';
               }
             }
             return files;
           },
           orderable: false
         },
        //  { data: 'visual_file_desc', orderable: false },
         { data: 'visual_file_actions', orderable: false }      
       ],
       columnDefs: [
         {
           targets: 3,
           searchable: false,
           orderable: false,
           render: function (data, type, row, meta) {
               return '' +
               '<div class="d-inline-block text-nowrap">' +
                   '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                   '<div class="dropdown-menu">' +
                       '<a id="dropdownMenuDeleteVisual" data-id="' + row.visual_file_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
                   '</div>' +
               '</div>';
           }
         }
       ],
       order: [[1, 'asc']],
       dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
     });
   }
 
   // visual config
   const previewTemplateVisual = `
     <div class="dz-preview dz-file-preview">
       <div class="dz-details">
         <div class="dz-thumbnail">
           <img data-dz-thumbnail>
           <span class="dz-nopreview">No preview</span>
           <div class="dz-success-mark"></div>
           <div class="dz-error-mark"></div>
           <div class="dz-error-message"><span data-dz-errormessage></span></div>
           <div class="progress">
             <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
           </div>
         </div>
         <div class="dz-filename" data-dz-name></div>
         <div class="dz-size" data-dz-size></div>
       </div>
     </div>`;
 
 
   const visualDropzoneMulti = new Dropzone('#add-visual-file', {
     url: '#',
     paramName: 'visual_files',
     previewTemplate: previewTemplateVisual,
     autoProcessQueue: false,
     acceptedFiles: 'image/jpg, image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-powerpoint, application/vnd.openxmlformats-officedocument.presentationml.presentation',
     parallelUploads: 20,
     maxFilesize: 0.5, // max size 512 KB/file
     addRemoveLinks: true,
   });
     
   // End visual file config
 
   $(document).on('click', '#dropdownMenuDetail', function () {
   // Reinit datatable with new param file
    visit_order_id_global = visit_order_id;
    dt_ajax_visual_file.draw();
   });
 
   // Add Visual Form
   var addNewVisualForm = document.getElementById('form_add_visual');
   // Visual File Form Validation
   var fv_visual = FormValidation.formValidation(addNewVisualForm, {
   fields: {
     visual_file: {
       validators: {
         notEmpty: {
           message: 'Please enter visual file'
         }
       }
     },
     visual_file_name: {
       validators: {
         notEmpty: {
           message: 'Please enter visual file name'
         }
       }
     },
     visual_file_desc: {
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
     autoFocus: new FormValidation.plugins.AutoFocus()
    }
   }).on('core.form.valid', function () {
     var url_visual = "site-visit/visit-order-manage/visual-file/store";
     var rejected_files_visual = visualDropzoneMulti.getRejectedFiles();
 
     if (Array.isArray(rejected_files_visual) && rejected_files_visual.length) {
       Swal.fire({
         title: 'Check your visual file!',
         text: 'Remove any wrong visual file.',
         icon: 'error',
         customClass: {
           confirmButton: 'btn btn-primary'
         }
       });
     } else {
       var form_data = new FormData(addNewVisualForm);
       form_data.append('visit_order_id', visit_order_id_global);
 
       var visual_files = visualDropzoneMulti.getAcceptedFiles();
       visual_files.forEach((val, index) => {
         form_data.append('visual_files[]', val); // append visual file to form data
       });
 
       $.ajax({
         data: form_data,
         url: baseUrl + url_visual,
         type: 'POST',
         processData: false,
         contentType: false,
         success: function success(response) {
           // console.log(response);
           dt_ajax_visual_file.draw();
 
           if (response.status) {
             $('#visual_file_name').val(null);
             $('#visual_file_desc').val(null);
             visualDropzoneMulti.removeAllFiles(true);
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
      }
   });
   // End Add Visual File Form
 
   // Delete visual button handler
   $(document).on('click', '#dropdownMenuDeleteVisual', function () {
     var visual_file_id = $(this).data('id');
 
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
           data: {visual_file_id: visual_file_id},
           type: 'POST',
           url: baseUrl + 'site-visit/visit-order-manage/visual-file/delete',
           success: function success(response) {
            dt_ajax_visual_file.draw();
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
   // End Data Visual File

});
