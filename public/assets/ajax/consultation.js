$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#TableConsultation').DataTable({
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
            searchPlaceholder: 'Search consultations...'
        },
        buttons: [
            {
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Konsultasi</span>',
                className: 'add-new btn btn-primary waves-effect waves-light',
                attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#consultationModal'
                }
            }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: '/frontend/consultation/',
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
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'service_name', name: 'service_name' },
            { data: 'source', name: 'source' },
            { data: 'status', name: 'status' },
            { data: 'created_by_name', name: 'created_by' },
            {
                data: 'aksi',
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    var userPermissions = window.userPermissions || [];
                    var canEdit = userPermissions.includes('edit_consultation');
                    var canDelete = userPermissions.includes('delete_consultation');
                    var buttons = '<div class="d-flex align-items-center">';
                    buttons += '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>';
                    buttons += '<div class="dropdown-menu dropdown-menu-end m-0">';

                    if (canEdit) {
                        buttons += '<a href="javascript:;" class="dropdown-item" onclick="editConsultation(' + full.id + ')"><i class="ti ti-edit ti-md"></i>Edit</a>';
                    }

                    if (canDelete) {
                        buttons += '<a href="javascript:;" class="dropdown-item delete-record" data-id="' + full.id + '"><i class="ti ti-trash ti-md"></i>Delete</a>';
                    }

                    buttons += '</div></div>';
                    return buttons;
                }
            }
        ],
        order: [[0, 'desc']]
    });

    window.editConsultation = function (id) {
        $('#formConsultation .form-control, #formConsultation .form-select').removeClass('is-invalid');
        $('#formConsultation .text-danger.small').text('');

        $.ajax({
            url: '/frontend/consultation/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (!response.success) {
                    toastr.error('Consultation data not found.');
                    return;
                }

                var consultation = response.consultation;
                $('#id').val(consultation.id);
                $('#name').val(consultation.name || '');
                $('#email').val(consultation.email || '');
                $('#phone').val(consultation.phone || '');
                $('#service_name').val(consultation.service_name || '');
                $('#source').val(consultation.source || 'homepage');
                $('#status').val(consultation.status || 'new');
                $('#message').val(consultation.message || '');
                $('#internal_notes').val(consultation.internal_notes || '');
                $('#consultationModalLabel').text('Edit Permintaan Konsultasi');
                $('#consultationModal').modal('show');
            },
            error: function () {
                toastr.error('An error occurred while fetching data.');
            }
        });
    };

    $('#formConsultation').on('submit', function (e) {
        e.preventDefault();

        var submitBtn = $('#btn-simpan');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Saving...').prop('disabled', true);

        $('#formConsultation .form-control, #formConsultation .form-select').removeClass('is-invalid');
        $('#formConsultation .text-danger.small').text('');

        var formData = new FormData(this);
        var id = $('#id').val();
        var url = id ? '/frontend/consultation/update/' + id : '/frontend/consultation/store';
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
                    $('#consultationModal').modal('hide');
                    $('#TableConsultation').DataTable().ajax.reload();
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

    $('#consultationModal').on('hidden.bs.modal', function () {
        $('#formConsultation')[0].reset();
        $('#id').val('');
        $('#source').val('homepage');
        $('#status').val('new');
        $('#consultationModalLabel').text('Add Consultation Request');
        $('#formConsultation .form-control, #formConsultation .form-select').removeClass('is-invalid');
        $('#formConsultation .text-danger.small').text('');
    });

    $(document).on('click', '.delete-record', function () {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Consultation data will be deleted!',
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
                url: '/frontend/consultation/delete/' + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status === 200) {
                        toastr.success(response.message);
                        $('#TableConsultation').DataTable().ajax.reload();
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
