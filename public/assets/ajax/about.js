$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const userPermissions = window.userPermissions || [];
    const canEditJourney = userPermissions.includes('edit_profile');
    const canCreateMilestone = userPermissions.includes('create_profile');
    const canEditMilestone = userPermissions.includes('edit_profile');
    const canDeleteMilestone = userPermissions.includes('delete_profile');

    if (!canEditJourney) {
        $('#formJourney :input').prop('disabled', true);
        $('#btn-save-journey').hide();
    }

    function clearFormErrors(formSelector) {
        $(formSelector + ' .form-control, ' + formSelector + ' .form-select').removeClass('is-invalid');
        $(formSelector + ' .text-danger.small').text('');
    }

    /* ── Company Journey Form ── */
    $('#formJourney').on('submit', function (e) {
        e.preventDefault();

        if (!canEditJourney) {
            return;
        }

        const submitBtn = $('#btn-save-journey');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-1"></i>Saving...').prop('disabled', true);
        clearFormErrors('#formJourney');

        const formData = new FormData(this);
        formData.set('is_active', $('#is_active').is(':checked') ? '1' : '0');
        formData.append('_method', 'PUT');

        $.ajax({
            url: '/internal/profile/about/journey',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status === 200) {
                    toastr.success(response.message || 'Settings saved successfully.');
                } else {
                    toastr.error('An error occurred, please try again.');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors || {};
                    $.each(errors, function (key, value) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '-error').text(value[0]);
                    });
                } else {
                    toastr.error('Failed to save journey settings.');
                }
            },
            complete: function () {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    /* ── Milestones DataTable ── */
    const milestoneButtons = [];

    if (canCreateMilestone) {
        milestoneButtons.push({
            text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Milestone</span>',
            className: 'add-milestone btn btn-primary waves-effect waves-light',
            action: function () {
                openMilestoneModal();
            }
        });
    }

    const milestonesTable = $('#TableMilestones').DataTable({
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
            searchPlaceholder: 'Search milestones...'
        },
        buttons: milestoneButtons,
        processing: true,
        serverSide: true,
        ajax: {
            url: '/internal/profile/about',
            type: 'GET',
            data: function (d) {
                d.type = 'milestones';
            }
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
            {
                data: 'aksi',
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    let buttons = '<div class="d-flex align-items-center">';
                    buttons += '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>';
                    buttons += '<div class="dropdown-menu dropdown-menu-end m-0">';

                    if (canEditMilestone) {
                        buttons += '<a href="javascript:;" class="dropdown-item" onclick="editMilestone(' + full.id + ')"><i class="ti ti-edit ti-md"></i>Edit</a>';
                    }
                    if (canDeleteMilestone) {
                        buttons += '<a href="javascript:;" class="dropdown-item delete-milestone" data-id="' + full.id + '"><i class="ti ti-trash ti-md"></i>Delete</a>';
                    }

                    buttons += '</div></div>';
                    return buttons;
                }
            },
            { data: 'year', name: 'year' },
            { data: 'title', name: 'title' },
            { data: 'description', name: 'description' },
            { data: 'sort_order', name: 'sort_order' },
            { data: 'is_active', name: 'is_active' }
        ],
        order: [[5, 'asc']]
    });

    function openMilestoneModal() {
        clearFormErrors('#formMilestone');
        $('#formMilestone')[0].reset();
        $('#milestone_id').val('');
        $('#milestone_is_active').prop('checked', true);
        $('#milestone-modal-title').text('Add Milestone');
        $('#milestoneModal').modal('show');
    }

    window.editMilestone = function (id) {
        clearFormErrors('#formMilestone');

        $.ajax({
            url: '/internal/profile/about/milestones/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (!response.success) {
                    toastr.error('Milestone data not found.');
                    return;
                }

                const milestone = response.milestone;
                $('#milestone_id').val(milestone.id);
                $('#year').val(milestone.year);
                $('#milestone_title').val(milestone.title);
                $('#milestone_description').val(milestone.description);
                $('#sort_order').val(milestone.sort_order);
                $('#milestone_is_active').prop('checked', !!milestone.is_active);
                $('#milestone-modal-title').text('Edit Milestone');
                $('#milestoneModal').modal('show');
            },
            error: function () {
                toastr.error('An error occurred while fetching data.');
            }
        });
    };

    $('#formMilestone').on('submit', function (e) {
        e.preventDefault();

        const submitBtn = $('#btn-save-milestone');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-1"></i>Saving...').prop('disabled', true);
        clearFormErrors('#formMilestone');

        const id = $('#milestone_id').val();
        const formData = new FormData(this);
        formData.set('is_active', $('#milestone_is_active').is(':checked') ? '1' : '0');

        let url = '/internal/profile/about/milestones/store';
        let method = 'POST';

        if (id) {
            url = '/internal/profile/about/milestones/update/' + id;
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
                    $('#milestoneModal').modal('hide');
                    milestonesTable.ajax.reload(null, false);
                    toastr.success(response.message || 'Milestone saved successfully.');
                } else {
                    toastr.error('An error occurred, please try again.');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors || {};
                    $.each(errors, function (key, value) {
                        const fieldMap = {
                            title: '#milestone_title',
                            description: '#milestone_description'
                        };
                        const selector = fieldMap[key] || ('#' + key);
                        $(selector).addClass('is-invalid');
                        $('#' + key + '-error').text(value[0]);
                    });
                } else {
                    toastr.error('Failed to save milestone.');
                }
            },
            complete: function () {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    $('#milestoneModal').on('hidden.bs.modal', function () {
        $('#formMilestone')[0].reset();
        $('#milestone_id').val('');
        clearFormErrors('#formMilestone');
    });

    $(document).on('click', '.delete-milestone', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This milestone will be deleted.',
            icon: 'warning',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light ml-3',
                cancelButton: 'btn btn-label-secondary waves-effect waves-light'
            },
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            buttonsStyling: false
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: '/internal/profile/about/milestones/delete/' + id,
                type: 'DELETE',
                success: function (response) {
                    if (response.status === 200) {
                        toastr.success(response.message);
                        milestonesTable.ajax.reload(null, false);
                    } else {
                        toastr.error('Failed to delete milestone.');
                    }
                },
                error: function () {
                    toastr.error('An error occurred while deleting data.');
                }
            });
        });
    });
});
