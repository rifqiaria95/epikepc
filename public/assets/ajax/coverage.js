$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function toggleAreaFields() {
        var isReference = $('#type').val() === 'reference';
        $('.coverage-area-field').toggle(!isReference);

        if (isReference) {
            $('#kabupaten, #kelurahan').val('');
        }
    }

    $('#type').on('change', toggleAreaFields);

    $('#TableCoverage').DataTable({
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
            searchPlaceholder: 'Cari lokasi...'
        },
        buttons: [
            {
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Location</span>',
                className: 'add-new btn btn-primary waves-effect waves-light',
                attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#coverageModal'
                }
            }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: '/frontend/coverage/',
            type: 'GET'
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, full, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'kabupaten', name: 'kabupaten' },
            { data: 'kelurahan', name: 'kelurahan' },
            { data: 'name', name: 'name' },
            { data: 'type', name: 'type' },
            { data: 'is_active', name: 'is_active' },
            { data: 'created_by_name', name: 'created_by' },
            {
                data: 'aksi',
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    var userPermissions = window.userPermissions || [];
                    var canEdit = userPermissions.includes('edit_coverage');
                    var canDelete = userPermissions.includes('delete_coverage');
                    var buttons = '<div class="d-flex align-items-center">';
                    buttons += '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>';
                    buttons += '<div class="dropdown-menu dropdown-menu-end m-0">';

                    if (canEdit) {
                        buttons += '<a href="javascript:;" class="dropdown-item" onclick="editCoverage(' + full.id + ')"><i class="ti ti-edit ti-md"></i>Edit</a>';
                    }

                    if (canDelete) {
                        buttons += '<a href="javascript:;" class="dropdown-item delete-record" data-id="' + full.id + '"><i class="ti ti-trash ti-md"></i>Delete</a>';
                    }

                    buttons += '</div></div>';
                    return buttons;
                }
            }
        ],
        order: [[3, 'asc']]
    });

    window.editCoverage = function (id) {
        $('#formCoverage .form-control, #formCoverage .form-select').removeClass('is-invalid');
        $('#formCoverage .text-danger.small').text('');

        $.ajax({
            url: '/frontend/coverage/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (!response.success) {
                    toastr.error('Coverage location data not found.');
                    return;
                }

                var location = response.location;
                $('#id').val(location.id);
                $('#type').val(location.type || 'dukuh');
                $('#kabupaten').val(location.kabupaten || '');
                $('#kelurahan').val(location.kelurahan || '');
                $('#name').val(location.name || '');
                $('#sort_order').val(location.sort_order ?? 0);
                $('#is_active').prop('checked', location.is_active !== false);

                toggleAreaFields();
                $('#coverageModalLabel').text('Edit Location Coverage');
                $('#coverageModal').modal('show');
            },
            error: function () {
                toastr.error('An error occurred while fetching data.');
            }
        });
    };

    $('#formCoverage').on('submit', function (e) {
        e.preventDefault();

        var submitBtn = $('#btn-simpan');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Saving...').prop('disabled', true);

        $('#formCoverage .form-control, #formCoverage .form-select').removeClass('is-invalid');
        $('#formCoverage .text-danger.small').text('');

        var formData = new FormData(this);
        formData.set('is_active', $('#is_active').is(':checked') ? '1' : '0');

        var id = $('#id').val();
        var url = id ? '/frontend/coverage/update/' + id : '/frontend/coverage/store';
        var method = 'POST';

        if (id) {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            type: method,
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status === 200) {
                    $('#coverageModal').modal('hide');
                    $('#TableCoverage').DataTable().ajax.reload();
                    toastr.success(response.message || 'Data saved successfully!');
                } else {
                    toastr.error('Something went wrong, please try again.');
                }
                submitBtn.html(originalText).prop('disabled', false);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors || {};
                    $.each(errors, function (key, value) {
                        var fieldKey = key.split('.')[0];
                        $('#' + fieldKey).addClass('is-invalid');
                        $('#' + fieldKey + '-error').text(value[0]);
                    });
                } else {
                    toastr.error('Failed to save data.');
                }
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    $('#coverageModal').on('hidden.bs.modal', function () {
        $('#formCoverage')[0].reset();
        $('#id').val('');
        $('#coverageModalLabel').text('Add Location Coverage');
        $('#is_active').prop('checked', true);
        $('#sort_order').val(0);
        $('#type').val('dukuh');
        toggleAreaFields();
        $('#formCoverage .form-control, #formCoverage .form-select').removeClass('is-invalid');
        $('#formCoverage .text-danger.small').text('');
    });

    $('#coverageModal').on('shown.bs.modal', function () {
        if (!$('#id').val()) {
            toggleAreaFields();
        }
    });

    toggleAreaFields();

    $(document).on('click', '.delete-record', function () {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Coverage location data will be deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Delete',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            }
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: '/frontend/coverage/delete/' + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status === 200) {
                        toastr.success(response.message);
                        $('#TableCoverage').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.errors || 'Failed to delete data.');
                    }
                },
                error: function () {
                    toastr.error('An error occurred while deleting data.');
                }
            });
        });
    });
});
