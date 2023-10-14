/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var dt_visit_order_table = $('.datatables-ajax-visit-order');
    var dt_visit_order_table_edit = $('.datatables-ajax-visit-order-edit');
    var dt_visit_order_table_paid = $('.datatables-ajax-visit-order-paid');
    var dt_visit_order_table_detail = $('.datatables-ajax-visit-order-detail');
    var dt_client_invoice_table = $('.datatables-ajax-client-invoice');
    var dt_client_invoice_file_edit_table = $('.datatables-ajax-client-invoice-file-edit');
    var dt_client_invoice_file_paid_table = $('.datatables-ajax-client-invoice-file-paid');
    var dt_client_invoice_file_detail_table = $('.datatables-ajax-client-invoice-file-detail');
    var modal_add_client_invoice = $('#modalAddClientInvoice');
    var modal_edit_client_invoice = $('#modalEditClientInvoice');
    var modal_detail_client_invoice = $('#modalDetailClientInvoice');
    var modal_paid_client_invoice = $('#modalPaidClientInvoice');
    var modal_class_loader = $('.modal-block-loader');
    var ac_site = $('.ac_site');
    var ac_site_edit = $('.ac_site_edit');
    var ac_site_paid = $('.ac_site_paid');
    var listMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    var client_invoice_id_global;

    var stepper_edit = new Stepper($('.bs-stepper')[0], {
        linear: false,
    });
    var stepper_detail = new Stepper($('.bs-stepper')[1], {
        linear: false,
    });
    var stepper_paid = new Stepper($('.bs-stepper')[2], {
        linear: false,
    });

    const previewTemplate = `<div class="dz-preview dz-file-preview">
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

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    });

    // Select2 site name handler
    if (ac_site.length) {
        var $this = ac_site;
        $this.wrap('<div class="position-relative"></div>').select2({
            placeholder: 'Select site name',
            minimumInputLength: 0,
            ajax: {
                url: baseUrl + 'autocomplete/site/find',
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

    if (ac_site_edit.length) {
        var $this = ac_site_edit;
        $this.wrap('<div class="position-relative"></div>').select2({
            placeholder: 'Select site name',
            minimumInputLength: 0,
            ajax: {
                url: baseUrl + 'autocomplete/site/find',
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

    if (ac_site_paid.length) {
        var $this = ac_site_paid;
        $this.wrap('<div class="position-relative"></div>').select2({
            placeholder: 'Select site name',
            minimumInputLength: 0,
            ajax: {
                url: baseUrl + 'autocomplete/site/find',
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

    if (dt_client_invoice_table.length) {
        var dt_ajax_client_invoice = dt_client_invoice_table.DataTable({
            processing: true,
            serverSide: true,
            initComplete: onInit,
            ajax: {
                url: baseUrl + 'billings/client-invoice/get',
                beforeSend: function () {
                    window.Helpers.blockUIPageLoader(baseUrl + 'billings/client-invoice/get');
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
                { data: 'client_invoice_number' },
                { data: 'client_invoice_name' },
                { data: 'site_name' },
                { data: 'client_invoice_month' },
                { data: 'client_invoice_year' },
                { data: 'client_invoice_status' },
            ],
            columnDefs: [
                {
                    targets: 4,
                    render: function (data, type, row, meta) {
                        return listMonth[data - 1];
                    }
                },
                {
                    targets: 6,
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        if (data == 1) {
                            return '<span class="badge bg-label-secondary me-1">Open</span>';
                        } else if (data == 2) {
                            return '<span class="badge bg-label-warning me-1">Billed</span>';
                        } else if (data == 3) {
                            return '<span class="badge bg-label-success me-1">Paid</span>';
                        } else if (data == 5) {
                            return '<span class="badge bg-label-danger me-1">Deleted</span>';
                        } else {
                            return '<span class="badge bg-label-danger me-1">Unknown</span>';
                        }
                    }
                },
                {
                    targets: 7,
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var text = '';
                        text += '' +
                            '<div class="d-inline-block text-nowrap">' +
                            '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                            '<div class="dropdown-menu">' +
                            '<a id="dropdownMenuDetail" data-id="' + row.client_invoice_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-detail me-1"></i> Detail</a>';
                        if (row.client_invoice_status === 2) {
                            text += '<div class="dropdown-divider"></div>' +
                                '<a id="dropdownMenuPaid" data-id="' + row.client_invoice_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-credit-card-front me-1"></i> Paid</a>';
                        }
                        text += '<div class="dropdown-divider"></div>' +
                            '<a id="dropdownMenuEdit" data-id="' + row.client_invoice_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit me-1"></i> Edit</a>' +
                            '<div class="dropdown-divider"></div>' +
                            '<a id="dropdownMenuDelete" data-id="' + row.client_invoice_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
                            '</div>' +
                            '</div>';
                        return text;
                    }
                }
            ],
            order: [[1, 'asc']],
            dom: '<"row"<"col-sm-12 col-md-6"l>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
        });
    }

    if (dt_visit_order_table.length) {
        dt_visit_order_table.DataTable({
            destroy: true,
            dom: '<"d-flex justify-content-between align-items-center row"<"col-sm-12 col-md-2 d-flex"f><"col-sm-12 col-md-10 d-none"i>>t',
            scrollY: '300px',
            scrollX: true,
            scrollCollapse: true,
        });
    }

    if (dt_visit_order_table_detail.length) {
        dt_visit_order_table_detail.DataTable({
            destroy: true,
            dom: '<"d-flex justify-content-between align-items-center row"<"col-sm-12 col-md-2 d-flex"f><"col-sm-12 col-md-10 d-none"i>>t',
            scrollY: '300px',
            scrollX: true,
            scrollCollapse: true,
        });
    }

    if (dt_client_invoice_file_edit_table.length) {
        var dt_invoice_file_edit = dt_client_invoice_file_edit_table.DataTable({
            destroy: true,
            dom: '<"d-flex justify-content-between align-items-center row"<"col-sm-12 col-md-2 d-flex"f><"col-sm-12 col-md-10 d-none"i>>t',
        });
    }

    if (dt_client_invoice_file_paid_table.length) {
        var dt_invoice_file_paid = dt_client_invoice_file_paid_table.DataTable({
            destroy: true,
            dom: '<"d-flex justify-content-between align-items-center row"<"col-sm-12 col-md-2 d-flex"f><"col-sm-12 col-md-10 d-none"i>>t',
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

    // Multiple Dropzone
    const fileInvoiceDropzoneMultiAdd = new Dropzone('#add-file-attachment', {
        url: "#",
        previewTemplate: previewTemplate,
        parallelUploads: 1,
        maxFilesize: 1,
        acceptedFiles: 'image/jpg, image/jpeg, image/png',
        addRemoveLinks: true
    });

    // Multiple Dropzone
    const fileInvoiceDropzoneMultiEdit = new Dropzone('#edit-file-attachment', {
        url: "#",
        previewTemplate: previewTemplate,
        parallelUploads: 1,
        maxFilesize: 1,
        acceptedFiles: 'image/jpg, image/jpeg, image/png',
        addRemoveLinks: true
    });

    $('#addSite').on('change', function () {
        dt_visit_order_table.DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: baseUrl + 'billings/client-invoice/get-visit-order',
                data: { site_id: $('#addSite').val(), method: 'add' },
                beforeSend: function () {
                    window.Helpers.blockUIPageLoader(baseUrl + 'billings/client-invoice/get-visit-order');
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
                { data: 'visit_order_id' },
                { data: 'visit_order_number', orderable: false },
                { data: 'visit_order_date' },
                { data: 'visit_order_due_date' },
                { data: 'client_name' },
                { data: 'site_name' },
                {
                    data: 'visit_order_location',
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
                {
                    data: 'partner_name',
                    render: function (data) {
                        if (data) {
                            return data;
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    data: 'download_status',
                    searchable: false,
                    orderable: false,
                    render: function (data) {
                        if (data == "Downloaded") {
                            return '<span class="badge bg-label-primary me-1">' + data + '</span>';
                        } else if (data == "Not Downloaded") {
                            return '<span class="badge bg-label-secondary me-1">' + data + '</span>';
                        } else {
                            return '<span class="badge bg-label-danger me-1">-</span>';
                        }
                    }
                },
                { data: 'visit_order_status' },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    checkboxes: true,
                    render: function (data, type, row) {
                        return '<input type="checkbox" class="dt-checkboxes form-check-input" name="visit_order[' + data + ']" >';
                    },
                    checkboxes: {
                        selectAllRender: '<input type="checkbox" class="form-check-input">'
                    }
                },
                {
                    targets: 9,
                    searchable: false,
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
                            return '<span class="badge bg-label-success me-1">Visited</span>';
                        } else if (data == 6) {
                            return '<span class="badge bg-label-primary me-1">Validated</span>';
                        } else {
                            return '<span class="badge bg-label-danger me-1">Unknown</span>';
                        }
                    }
                },
            ],
            order: [[1, 'asc']],
            dom: '<"row"<"col-sm-12 col-md-6"l>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            scrollY: '300px',
            scrollX: true,
            scrollCollapse: true,
            paging: false,
            info: false
        });
    });

    $('#editSite').on('change', function () {
        dt_visit_order_table_edit.DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: baseUrl + 'billings/client-invoice/get-visit-order',
                data: { site_id: $('#editSite').val(), method: 'edit', client_invoice_id: client_invoice_id_global },
                beforeSend: function () {
                    window.Helpers.blockUIPageLoader(baseUrl + 'billings/client-invoice/get-visit-order');
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
                { data: 'visit_order_id' },
                { data: 'visit_order_number', orderable: false },
                { data: 'visit_order_date' },
                { data: 'visit_order_due_date' },
                { data: 'client_name' },
                { data: 'site_name' },
                {
                    data: 'visit_order_location',
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
                {
                    data: 'partner_name',
                    render: function (data) {
                        if (data) {
                            return data;
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    data: 'download_status',
                    searchable: false,
                    orderable: false,
                    render: function (data) {
                        if (data == "Downloaded") {
                            return '<span class="badge bg-label-primary me-1">' + data + '</span>';
                        } else if (data == "Not Downloaded") {
                            return '<span class="badge bg-label-secondary me-1">' + data + '</span>';
                        } else {
                            return '<span class="badge bg-label-danger me-1">-</span>';
                        }
                    }
                },
                { data: 'visit_order_status' },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    checkboxes: true,
                    render: function (data, type, row) {
                        var isExist = false;
                        for (let i = 0; i < row.client_invoice.length; i++) {
                            if (row.client_invoice[i].client_invoice_id == client_invoice_id_global) {
                                isExist = true;
                            }
                        }
                        return '<input type="checkbox" class="dt-checkboxes form-check-input" name="visit_order[' + data + ']" ' + (isExist ? 'checked' : '') + '>';
                    },
                    checkboxes: {
                        selectAllRender: '<input type="checkbox" class="form-check-input">'
                    }
                },
                {
                    targets: 9,
                    searchable: false,
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
                            return '<span class="badge bg-label-success me-1">Visited</span>';
                        } else if (data == 6) {
                            return '<span class="badge bg-label-primary me-1">Validated</span>';
                        } else {
                            return '<span class="badge bg-label-danger me-1">Unknown</span>';
                        }
                    }
                },
            ],
            order: [[1, 'asc']],
            dom: '<"row"<"col-sm-12 col-md-6"l>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            scrollY: '300px',
            scrollX: true,
            scrollCollapse: true,
            paging: false,
            info: false
        });
    });

    $('#paidSite').on('change', function () {
        dt_visit_order_table_paid.DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: baseUrl + 'billings/client-invoice/get-visit-order',
                data: { site_id: $('#paidSite').val(), method: 'paid', client_invoice_id: client_invoice_id_global },
                beforeSend: function () {
                    window.Helpers.blockUIPageLoader(baseUrl + 'billings/client-invoice/get-visit-order');
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
                { data: 'visit_order_id' },
                { data: 'visit_order_number', orderable: false },
                { data: 'visit_order_date' },
                { data: 'visit_order_due_date' },
                { data: 'client_name' },
                { data: 'site_name' },
                {
                    data: 'visit_order_location',
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
                {
                    data: 'partner_name',
                    render: function (data) {
                        if (data) {
                            return data;
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    data: 'download_status',
                    searchable: false,
                    orderable: false,
                    render: function (data) {
                        if (data == "Downloaded") {
                            return '<span class="badge bg-label-primary me-1">' + data + '</span>';
                        } else if (data == "Not Downloaded") {
                            return '<span class="badge bg-label-secondary me-1">' + data + '</span>';
                        } else {
                            return '<span class="badge bg-label-danger me-1">-</span>';
                        }
                    }
                },
                { data: 'visit_order_status' },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    checkboxes: true,
                    render: function (data, type, row) {
                        var isExist = false;
                        for (let i = 0; i < row.client_invoice.length; i++) {
                            if (row.client_invoice[i].client_invoice_id == client_invoice_id_global) {
                                isExist = true;
                            }
                        }
                        return '<input type="checkbox" class="dt-checkboxes form-check-input" name="visit_order[' + data + ']" ' + (isExist ? 'checked' : '') + '>';
                    },
                    checkboxes: {
                        selectAllRender: '<input type="checkbox" class="form-check-input">'
                    }
                },
                {
                    targets: 9,
                    searchable: false,
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
                            return '<span class="badge bg-label-success me-1">Visited</span>';
                        } else if (data == 6) {
                            return '<span class="badge bg-label-primary me-1">Validated</span>';
                        } else {
                            return '<span class="badge bg-label-danger me-1">Unknown</span>';
                        }
                    }
                },
            ],
            order: [[1, 'asc']],
            dom: '<"row"<"col-sm-12 col-md-6"l>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            scrollY: '300px',
            scrollX: true,
            scrollCollapse: true,
            paging: false,
            info: false
        });
    });

    $(document).on('click', '#dropdownMenuPaid', function () {
        var client_invoice_id = $(this).data('id');
        client_invoice_id_global = client_invoice_id;

        modal_paid_client_invoice.modal('show');
        stepper_paid.to(1);

        // Fungsi untuk Detail order visit
        // get data
        $.ajax({
            url: baseUrl + "billings/client-invoice/show/" + client_invoice_id,
            type: 'GET',
            beforeSend: function (data) {
                window.Helpers.blockUIModalLoader(modal_class_loader);
            },
            success: function (response) {

                $('#paidName').val(response.data.client_invoice_name)
                $('#paid-year-month').val(response.data.client_invoice_year + '-' + ((response.data.client_invoice_month >= 10) ? response.data.client_invoice_month : '0' + response.data.client_invoice_month))
                $('#paidDesc').val(response.data.client_invoice_desc)

                var option = new Option(response.data.site.site_name, response.data.site.site_id, true, true);
                $('#paidSite').append(option).trigger('change');

                modal_class_loader.unblock();
            }
        });

        dt_invoice_file_paid = dt_client_invoice_file_paid_table.DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: baseUrl + 'billings/client-invoice/get-file',
                data: { client_invoice_id: client_invoice_id_global },
                beforeSend: function () {
                    window.Helpers.blockUIModalLoader(modal_class_loader);
                },
                complete: function () {
                    modal_class_loader.unblock();
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
                { data: 'no' },
                { data: 'client_invoice_name' },
                { data: 'client_invoice_file_desc' },
                { data: 'client_invoice_file', orderable: false },
            ],
            columnDefs: [
                {
                    targets: 3,
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var src = "/storage/client_invoice_uploads/" + row.folder_name + '/' + row.client_invoice_file;
                        return '<a href="' + src + '" target="_blank"><img src="' + src + '" width="50px" height="50px" /></a>';
                    },
                },
            ],
            order: [[1, 'asc']],
            dom: '<"row"<"col-sm-12 col-md-6"l>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });

        $('#formPaidClientInvoice').attr('data-id', client_invoice_id);
    })

    $(document).on('click', '#dropdownMenuDeleteFile', function () {
        var client_invoice_file_id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: baseUrl + 'billings/client-invoice/deleteFile/' + client_invoice_file_id,
                    success: function success(response) {
                        dt_invoice_file_edit.draw();
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
                            text: "Internal Server Error",
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    }
                });
            }
        });
    })

    $(document).on('click', '#dropdownMenuDetail', function () {
        var client_invoice_id = $(this).data('id');

        modal_detail_client_invoice.modal('show');
        stepper_detail.to(1);

        // Fungsi untuk Detail order visit
        // get data
        $.ajax({
            url: baseUrl + "billings/client-invoice/show/" + client_invoice_id,
            type: 'GET',
            beforeSend: function (data) {
                window.Helpers.blockUIModalLoader(modal_class_loader);
            },
            success: function (response) {

                $('#edit_number').text(response.data.client_invoice_number);
                $('#edit_name').text(response.data.client_invoice_name);
                $('#edit_month').text(listMonth[response.data.client_invoice_month - 1]);
                $('#edit_year').text(response.data.client_invoice_year);
                $('#edit_site_name').text(response.data.site.site_name);
                $('#edit_desc').text(response.data.client_invoice_desc);

                dt_visit_order_table_detail.DataTable({
                    destroy: true,
                    data: response.data.visit_order,
                    columns: [
                        { data: 'no', orderable: false, searchable: false },
                        { data: 'visit_order_number', orderable: false },
                        { data: 'visit_order_date' },
                        { data: 'visit_order_due_date' },
                        { data: 'client_name' },
                        { data: 'site_name' },
                        {
                            data: 'visit_order_location',
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
                        {
                            data: 'partner_name',
                            render: function (data) {
                                if (data) {
                                    return data;
                                } else {
                                    return '-';
                                }
                            }
                        },
                        {
                            data: 'download_status',
                            searchable: false,
                            orderable: false,
                            render: function (data) {
                                if (data == "Downloaded") {
                                    return '<span class="badge bg-label-primary me-1">' + data + '</span>';
                                } else if (data == "Not Downloaded") {
                                    return '<span class="badge bg-label-secondary me-1">' + data + '</span>';
                                } else {
                                    return '<span class="badge bg-label-danger me-1">-</span>';
                                }
                            }
                        },
                        { data: 'visit_order_status' },
                    ],
                    columnDefs: [
                        {
                            targets: 9,
                            searchable: false,
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
                                    return '<span class="badge bg-label-success me-1">Visited</span>';
                                } else if (data == 6) {
                                    return '<span class="badge bg-label-primary me-1">Validated</span>';
                                } else if (data == 7) {
                                    return '<span class="badge bg-label-primary me-1">Cant Billed</span>';
                                } else {
                                    return '<span class="badge bg-label-danger me-1">Unknown</span>';
                                }
                            }
                        },
                    ],
                    order: [[1, 'asc']],
                    dom: '<"row"<"col-sm-12 col-md-6"l>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    scrollY: '300px',
                    scrollX: true,
                    scrollCollapse: true,
                    paging: false,
                    info: false
                });

                getTimelineData(response.data.history);

                modal_class_loader.unblock();
            }
        });

        dt_client_invoice_file_detail_table.DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: baseUrl + 'billings/client-invoice/get-file',
                data: { client_invoice_id: client_invoice_id },
                beforeSend: function () {
                    window.Helpers.blockUIModalLoader(modal_class_loader);
                },
                complete: function () {
                    modal_class_loader.unblock();
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
                { data: 'no' },
                { data: 'client_invoice_name' },
                { data: 'client_invoice_file_desc' },
                { data: 'client_invoice_file_url', orderable: false },
            ],
            columnDefs: [
                {
                    targets: 3,
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return '<a href="' + data + '" target="_blank"><img src="' + data + '" width="50px" height="50px" /></a>';
                    },
                }
            ],
            order: [[1, 'asc']],
            dom: '<"row"<"col-sm-12 col-md-6"l>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });

    });

    $(document).on('click', '#dropdownMenuEdit', function () {
        var client_invoice_id = $(this).data('id');
        client_invoice_id_global = client_invoice_id;

        modal_edit_client_invoice.modal('show');
        stepper_edit.to(1);

        // Fungsi untuk Detail order visit
        // get data
        $.ajax({
            url: baseUrl + "billings/client-invoice/show/" + client_invoice_id,
            type: 'GET',
            beforeSend: function (data) {
                window.Helpers.blockUIModalLoader(modal_class_loader);
            },
            success: function (response) {

                $('#editName').val(response.data.client_invoice_name)
                $('#edit-year-month').val(response.data.client_invoice_year + '-' + ((response.data.client_invoice_month >= 10) ? response.data.client_invoice_month : '0' + response.data.client_invoice_month))
                $('#editDesc').val(response.data.client_invoice_desc)

                var option = new Option(response.data.site.site_name, response.data.site.site_id, true, true);
                $('#editSite').append(option).trigger('change');

                modal_class_loader.unblock();
            }
        });

        dt_invoice_file_edit = dt_client_invoice_file_edit_table.DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: baseUrl + 'billings/client-invoice/get-file',
                data: { client_invoice_id: client_invoice_id_global },
                beforeSend: function () {
                    window.Helpers.blockUIModalLoader(modal_class_loader);
                },
                complete: function () {
                    modal_class_loader.unblock();
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
                { data: 'no' },
                { data: 'client_invoice_name' },
                { data: 'client_invoice_file_desc' },
                { data: 'client_invoice_file_url', orderable: false },
            ],
            columnDefs: [
                {
                    targets: 3,
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return '<a href="' + data + '" target="_blank"><img src="' + data + '" width="50px" height="50px" /></a>';
                    },
                },
                {
                    targets: 4,
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return '' +
                            '<div class="d-inline-block text-nowrap">' +
                            '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>' +
                            '<div class="dropdown-menu">' +
                            '<a id="dropdownMenuDeleteFile" data-id="' + row.client_invoice_file_id + '" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>' +
                            '</div>' +
                            '</div>';
                    }
                }
            ],
            order: [[1, 'asc']],
            dom: '<"row"<"col-sm-12 col-md-6"l>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });

        $('#formEditClientInvoice').attr('data-id', client_invoice_id);
        $('#formEditUploadFileClientInvoice').attr('data-id', client_invoice_id);
    });

    $(document).on('click', '#dropdownMenuDelete', function () {
        var client_invoice_id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    data: { client_invoice_id: client_invoice_id },
                    type: 'POST',
                    url: baseUrl + 'billings/client-invoice/delete',
                    success: function success(response) {
                        dt_ajax_client_invoice.draw();
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
                            text: "Internal Server Error",
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    }
                });
            }
        });
    })

    var addClientForm = document.getElementById('formAddClientInvoice');
    // add client invoice form validation
    var fv = FormValidation.formValidation(addClientForm, {
        fields: {
            client_invoice_name: {
                validators: {
                    notEmpty: {
                        message: 'Please enter invoice name'
                    }
                }
            },
            site_id: {
                validators: {
                    notEmpty: {
                        message: 'Please enter invoice name'
                    }
                }
            },
            year_month: {
                validators: {
                    notEmpty: {
                        message: 'Please enter invoice name'
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
        var rejected_files_attachment = fileInvoiceDropzoneMultiAdd.getRejectedFiles();

        if (Array.isArray(rejected_files_attachment) && rejected_files_attachment.length) {
            Swal.fire({
                title: 'Check your file attachment!',
                text: 'Remove any wrong file attachment.',
                icon: 'error',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
        } else {
            var form_data = new FormData(addClientForm);

            var product_attachment = fileInvoiceDropzoneMultiAdd.getAcceptedFiles();
            product_attachment.forEach((val, index) => {
                form_data.append('client_invoice_file[]', val); // append attachment file to form data
            });

            var url = "billings/client-invoice/store";

            $.ajax({
                data: form_data,
                url: baseUrl + url,
                type: 'POST',
                processData: false,
                contentType: false,
                success: function success(response) {
                    if (response.status) {
                        modal_add_client_invoice.modal('hide');
                        dt_ajax_client_invoice.draw();
                        fileInvoiceDropzoneMultiAdd.removeAllFiles(true);
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
                        text: "Internal Server Error",
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                }
            });
        }
    });

    var paidClientForm = document.getElementById('formPaidClientInvoice');
    // edit client invoice form validation
    var fvPaid = FormValidation.formValidation(paidClientForm, {
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
        var form_data = new FormData(paidClientForm);
        var url = "billings/client-invoice/paid/" + $('#formPaidClientInvoice').attr('data-id');

        $.ajax({
            data: form_data,
            url: baseUrl + url,
            type: 'POST',
            processData: false,
            contentType: false,
            success: function success(response) {
                if (response.status) {
                    modal_paid_client_invoice.modal('hide');
                    dt_ajax_client_invoice.draw();
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
                    text: "Internal Server Error",
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            }
        });
    });

    var editClientForm = document.getElementById('formEditClientInvoice');
    // edit client invoice form validation
    var fvEdit = FormValidation.formValidation(editClientForm, {
        fields: {
            client_invoice_name: {
                validators: {
                    notEmpty: {
                        message: 'Please enter invoice name'
                    }
                }
            },
            site_id: {
                validators: {
                    notEmpty: {
                        message: 'Please enter invoice name'
                    }
                }
            },
            year_month: {
                validators: {
                    notEmpty: {
                        message: 'Please enter invoice name'
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
        var form_data = new FormData(editClientForm);
        var url = "billings/client-invoice/update/" + $('#formEditClientInvoice').attr('data-id');

        $.ajax({
            data: form_data,
            url: baseUrl + url,
            type: 'POST',
            processData: false,
            contentType: false,
            success: function success(response) {
                if (response.status) {
                    modal_edit_client_invoice.modal('hide');
                    dt_ajax_client_invoice.draw();
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
                    text: "Internal Server Error",
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            }
        });
    });

    var editFileClientForm = document.getElementById('formEditUploadFileClientInvoice');
    // edit client invoice form validation
    var fvEditFile = FormValidation.formValidation(editFileClientForm, {
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
        var rejected_files_attachment = fileInvoiceDropzoneMultiEdit.getRejectedFiles();

        if (Array.isArray(rejected_files_attachment) && rejected_files_attachment.length) {
            Swal.fire({
                title: 'Check your file attachment!',
                text: 'Remove any wrong file attachment.',
                icon: 'error',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
        } else {
            var form_data = new FormData(editFileClientForm);

            var product_attachment = fileInvoiceDropzoneMultiEdit.getAcceptedFiles();
            product_attachment.forEach((val, index) => {
                form_data.append('client_invoice_file[]', val); // append attachment file to form data
            });

            var url = "billings/client-invoice/uploadFile/" + $('#formEditUploadFileClientInvoice').attr('data-id');

            $.ajax({
                data: form_data,
                url: baseUrl + url,
                type: 'POST',
                processData: false,
                contentType: false,
                success: function success(response) {
                    if (response.status) {
                        fileInvoiceDropzoneMultiEdit.removeAllFiles(true);
                        dt_invoice_file_edit.draw();
                        fvEditFile.resetForm(true);
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
                        text: "Internal Server Error",
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                }
            });
        }
    });

    // Mengambil data untuk timeline
    function getTimelineData(response) {
        var timeline_data = response;

        // Bersihkan konten yang ada sebelumnya
        var historyContainer = document.getElementById('history_client_invoice');
        historyContainer.innerHTML = '';

        timeline_data.forEach(function (timeline) {
            var stats = getStatusDot(timeline.client_invoice_history_status);
            var dot = `
            <li class="timeline-item timeline-item-transparent">
            <span class="timeline-point ${stats[0]}"></span>
            <div class="timeline-event">
              <div class="timeline-header mb-sm-0 mb-3">
                <h6 class="mb-0">${stats[1]}</h6>
                <small class="text-muted">${timeline.date_created_format} WIB</small>
              </div>
              <p>
                ${timeline.client_invoice_history_desc}
              </p>
            </div>
          </li>
          `;
            historyContainer.innerHTML += dot;
        });
        var last_dot = `
          <li class="timeline-end-indicator">
            <i class="bx bx-check-circle"></i>
          </li>
        `;
        historyContainer.innerHTML += last_dot;
    }

    // Function to get the corresponding status dot based on the status number
    function getStatusDot(status) {
        switch (status) {
            case 1:
                return ['timeline-point-secondary', 'Open'];
            case 2:
                return ['timeline-point-warning', 'Billed'];
            case 3:
                return ['timeline-point-success', 'Paid'];
            default:
                return ['timeline-point-danger', 'Unknown'];
        }
    }

    // Clearing form data when modal hidden
    modal_add_client_invoice.on('hidden.bs.modal', function () {
        client_invoice_id_global = null;
        fv.resetForm(true);
    });

    modal_edit_client_invoice.on('hidden.bs.modal', function () {
        client_invoice_id_global = null;
        fvEdit.resetForm(true);
        fvEditFile.resetForm(true);
    });
})