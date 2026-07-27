$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ─── DataTable ───────────────────────────────────────────────────────────
    var table = $('#TableProject').DataTable({
        dom:
            '<"row me-2"' +
            '<"col-md-2"<"me-3"l>>' +
            '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>' +
            '>t' +
            '<"row mx-2"' +
            '<"col-sm-12 col-md-6"i>' +
            '<"col-sm-12 col-md-6"p>' +
            '>',
        language: {
            sLengthMenu: '_MENU_',
            search: '',
            searchPlaceholder: 'Search...'
        },
        buttons: [
            {
                extend: 'collection',
                className: 'btn btn-label-secondary dropdown-toggle mx-4 waves-effect waves-light',
                text: '<i class="ti ti-upload me-2 ti-xs"></i>Export',
                buttons: [
                    {
                        extend: 'print',
                        text: '<i class="ti ti-printer me-2"></i>Print',
                        className: 'dropdown-item',
                        exportOptions: { columns: [0, 2, 3, 4, 5, 6] }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="ti ti-file-text me-2"></i>CSV',
                        className: 'dropdown-item',
                        exportOptions: { columns: [0, 2, 3, 4, 5, 6] }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
                        className: 'dropdown-item',
                        exportOptions: { columns: [0, 2, 3, 4, 5, 6] }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="ti ti-file-code-2 me-2"></i>PDF',
                        className: 'dropdown-item',
                        exportOptions: { columns: [0, 2, 3, 4, 5, 6] }
                    },
                    {
                        extend: 'copy',
                        text: '<i class="ti ti-copy me-2"></i>Copy',
                        className: 'dropdown-item',
                        exportOptions: { columns: [0, 2, 3, 4, 5, 6] }
                    }
                ]
            },
            {
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Project</span>',
                className: 'add-new btn btn-primary waves-effect waves-light',
                attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#tambahModal'
                }
            }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: '/frontend/project',
            type: 'GET'
        },
        columns: [
            {
                data: null,
                name: 'no',
                title: '#',
                orderable: false,
                searchable: false,
                render: function (data, type, full, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'image',
                name: 'image',
                orderable: false,
                searchable: false,
                render: function (data) {
                    if (data) {
                        return '<img src="' + data + '" alt="Project" style="width:50px;height:50px;object-fit:cover;border-radius:4px;" onerror="this.src=\'https://via.placeholder.com/50\'">';
                    }
                    return '<span class="text-muted">-</span>';
                }
            },
            { data: 'title', name: 'title' },
            {
                data: 'category',
                name: 'category',
                render: function (data) {
                    return data
                        ? '<span class="badge bg-label-primary">' + data + '</span>'
                        : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'client',
                name: 'client',
                render: function (data) { return data || '-'; }
            },
            {
                data: 'status',
                name: 'status',
                orderable: true,
                searchable: true
            },
            {
                data: 'location',
                name: 'location',
                render: function (data) { return data || '-'; }
            },
            {
                data: 'is_published',
                name: 'is_published',
                orderable: false,
                searchable: false
            },
            {
                data: 'project_date',
                name: 'project_date',
                render: function (data) { return data || '-'; }
            },
            {
                data: 'aksi',
                name: 'aksi',
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    var userPermissions = window.userPermissions || [];
                    var canEdit   = userPermissions.includes('edit_project');
                    var canDelete = userPermissions.includes('delete_project');

                    var html = '<div class="d-flex align-items-center">';
                    html += '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>';
                    html += '<div class="dropdown-menu dropdown-menu-end m-0">';
                    if (canEdit) {
                        html += '<a href="javascript:;" class="dropdown-item" onclick="editProject(' + full.id + ')"><i class="ti ti-edit ti-md me-1"></i>Edit</a>';
                    }
                    if (canDelete) {
                        html += '<a href="javascript:;" class="dropdown-item delete-project" data-id="' + full.id + '"><i class="ti ti-trash ti-md me-1"></i>Delete</a>';
                    }
                    html += '</div></div>';
                    return html;
                }
            }
        ],
        order: [[0, 'asc']]
    });

    // ─── Image preview helper ─────────────────────────────────────────────────
    function previewImage(inputId, previewId) {
        $('#' + inputId).on('change', function () {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#' + previewId).html(
                        '<img src="' + e.target.result + '" alt="Preview" style="max-width:100%;max-height:120px;border-radius:4px;margin-top:6px;">'
                    );
                };
                reader.readAsDataURL(file);
            } else {
                $('#' + previewId).html('');
            }
        });
    }

    previewImage('image', 'image-preview');
    previewImage('image_secondary', 'image_secondary-preview');
    previewImage('image_tertiary', 'image_tertiary-preview');

    // ─── Form submit (store / update) ────────────────────────────────────────
    $('#formProject').on('submit', function (e) {
        e.preventDefault();

        var submitBtn  = $('#btn-simpan');
        var origText   = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-1"></i>Saving...').prop('disabled', true);

        clearErrors();
        saveTinyMCE();

        var id      = $('#id').val();
        var formData = new FormData(this);

        // Ensure is_published value is correctly set
        formData.set('is_published', $('#is_published').is(':checked') ? 1 : 0);

        var url, method;
        if (id) {
            url    = '/frontend/project/update/' + id;
            method = 'POST';
            formData.append('_method', 'PUT');
        } else {
            url    = '/frontend/project/store';
            method = 'POST';
        }

        $.ajax({
            url: url,
            type: method,
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status === 200) {
                    $('#tambahModal').modal('hide');
                    table.ajax.reload(null, false);
                    toastr.success(response.message || 'Data saved successfully!');
                } else {
                    toastr.error('Something went wrong, please try again.');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        var inputKey = key.replace(/\./g, '_');
                        $('#' + inputKey).addClass('is-invalid');
                        $('#' + inputKey + '-error').text(value[0]);
                        if (key === 'content' || key === 'content_secondary') {
                            setTinyMCEError(key, true);
                        }
                    });
                    toastr.warning('Please review the form fields.');
                } else {
                    toastr.error('Failed to save data.');
                }
            },
            complete: function () {
                submitBtn.html(origText).prop('disabled', false);
            }
        });
    });

    // ─── Reset modal on close ────────────────────────────────────────────────
    $('#tambahModal').on('hidden.bs.modal', function () {
        $('#formProject')[0].reset();
        $('#id').val('');
        $('#modal-judul').text('Add Project');
        $('#is_published').prop('checked', false);
        $('#status').val('completed');
        $('#location').val('');
        $('#latitude').val('');
        $('#longitude').val('');
        $('#project_value').val('');

        ['image-preview', 'image_secondary-preview', 'image_tertiary-preview'].forEach(function (id) {
            $('#' + id).html('');
        });

        clearErrors();
        clearTinyMCE();

        // Reset to first tab
        $('#tambahModal .nav-link').first().tab('show');
    });

    // ─── Delete ──────────────────────────────────────────────────────────────
    $(document).on('click', '.delete-project', function () {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Project data will be deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Delete!',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger waves-effect waves-light ms-2',
                cancelButton: 'btn btn-label-secondary waves-effect waves-light'
            },
            buttonsStyling: false,
            didRender: function () {
                $('.swal2-actions').css('gap', '10px');
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/frontend/project/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                customClass: {
                                    confirmButton: 'btn btn-success waves-effect waves-light'
                                }
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Oops!', 'An error occurred while deleting data.', 'error');
                    }
                });
            }
        });
    });

    // ─── Helpers ─────────────────────────────────────────────────────────────
    function clearErrors() {
        $('#formProject .form-control, #formProject .form-select').removeClass('is-invalid');
        $('#formProject .text-danger.small').text('');
        setTinyMCEError('content', false);
        setTinyMCEError('content_secondary', false);
    }
});

// ─── Edit Project ─────────────────────────────────────────────────────────────
function editProject(id) {
    $('#formProject .form-control, #formProject .form-select').removeClass('is-invalid');
    $('#formProject .text-danger.small').text('');

    $.ajax({
        url: '/frontend/project/edit/' + id,
        type: 'GET',
        success: function (response) {
            if (response.success) {
                var p = response.project;

                $('#id').val(p.id);
                $('#title').val(p.title || '');
                $('#excerpt').val(p.excerpt || '');
                $('#category').val(p.category || '');
                $('#client').val(p.client || '');
                $('#project_date').val(p.project_date_raw || '');
                $('#website_url').val(p.website_url || '');
                $('#sort_order').val(p.sort_order ?? 0);
                $('#location').val(p.location || '');
                $('#latitude').val(p.latitude ?? '');
                $('#longitude').val(p.longitude ?? '');
                $('#project_value').val(p.project_value ?? '');
                $('#status').val(p.status || 'completed');
                $('#is_published').prop('checked', p.is_published == true || p.is_published == 1);

                setTinyMCEContent('content', p.content);
                setTinyMCEContent('content_secondary', p.content_secondary);

                $('#challenge_solution').val(p.challenge_solution || '');
                $('#final_result').val(p.final_result || '');

                // Show current image previews
                if (p.image_url) {
                    $('#image-preview').html('<img src="' + p.image_url + '" alt="Current" style="max-width:100%;max-height:120px;border-radius:4px;margin-top:6px;">');
                }
                if (p.image_secondary_url) {
                    $('#image_secondary-preview').html('<img src="' + p.image_secondary_url + '" alt="Current" style="max-width:100%;max-height:120px;border-radius:4px;margin-top:6px;">');
                }
                if (p.image_tertiary_url) {
                    $('#image_tertiary-preview').html('<img src="' + p.image_tertiary_url + '" alt="Current" style="max-width:100%;max-height:120px;border-radius:4px;margin-top:6px;">');
                }

                $('#modal-judul').text('Edit Project');
                $('#tambahModal').modal('show');

                // Switch back to first tab
                $('#tambahModal .nav-link').first().tab('show');
            } else {
                toastr.error('Project data not found.');
            }
        },
        error: function () {
            toastr.error('An error occurred while fetching data.');
        }
    });
}
