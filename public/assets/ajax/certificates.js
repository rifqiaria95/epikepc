/**
 * Certificate Gallery — CMS DataTables
 */
(function ($) {
    'use strict';

    var perms = window.userPermissions || [];
    var can = function (p) { return perms.indexOf(p) !== -1; };

    function clearErrors() {
        $('[id$="-error"]').text('');
    }

    function showErrors(errors) {
        clearErrors();
        $.each(errors || {}, function (field, messages) {
            $('#' + field.replace('.', '-') + '-error, #' + field + '-error').first().text((messages && messages[0]) || '');
        });
    }

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var table = $('#TableCertificates').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.certificateRoutes.index,
            data: function (d) {
                d.status = $('#filter-status').val();
                d.featured = $('#filter-featured').val();
                d.expiry_state = $('#filter-expiry').val();
                d.issued_year = $('#filter-year').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            {
                data: 'image_path',
                orderable: false,
                render: function (url) {
                    return url ? '<img src="' + url + '" alt="" style="height:48px;width:auto;border-radius:4px;">' : '—';
                }
            },
            { data: 'title' },
            { data: 'issuer' },
            { data: 'display_order' },
            { data: 'issued_at' },
            { data: 'expires_at' },
            { data: 'status', orderable: false },
            { data: 'is_featured', orderable: false },
            { data: 'homepage_visible', orderable: false },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function (id, type, row) {
                    var items = '';
                    var statusText = (row.status || '').toString();
                    var isPublished = statusText.indexOf('Published') !== -1 || statusText.indexOf('PUBLISHED') !== -1;

                    if (can('edit_certificates')) {
                        items += '<a href="javascript:;" class="dropdown-item btn-edit" data-id="' + id + '"><i class="ti ti-edit ti-md me-1"></i>Edit</a>';
                    }
                    if (can('publish_certificates')) {
                        if (!isPublished) {
                            items += '<a href="javascript:;" class="dropdown-item btn-publish" data-id="' + id + '"><i class="ti ti-upload ti-md me-1"></i>Publish</a>';
                        } else {
                            items += '<a href="javascript:;" class="dropdown-item btn-unpublish" data-id="' + id + '"><i class="ti ti-download ti-md me-1"></i>Unpublish</a>';
                        }
                        items += '<a href="javascript:;" class="dropdown-item btn-archive" data-id="' + id + '"><i class="ti ti-archive ti-md me-1"></i>Archive</a>';
                    }
                    if (can('delete_certificates')) {
                        items += '<a href="javascript:;" class="dropdown-item btn-delete text-danger" data-id="' + id + '"><i class="ti ti-trash ti-md me-1"></i>Delete</a>';
                    }

                    if (!items) {
                        return '';
                    }

                    return (
                        '<div class="d-flex align-items-center">' +
                        '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<i class="ti ti-dots-vertical ti-md"></i>' +
                        '</a>' +
                        '<div class="dropdown-menu dropdown-menu-end m-0">' + items + '</div>' +
                        '</div>'
                    );
                }
            }
        ],
        order: [[4, 'asc']],
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
        buttons: can('create_certificates') ? [{
            text: '<i class="ti ti-plus me-1"></i>Add Certificate',
            className: 'btn btn-primary',
            action: function () { openModal(); }
        }] : []
    });

    $('#filter-status, #filter-featured, #filter-expiry').on('change', function () { table.ajax.reload(); });
    $('#filter-year').on('keyup change', debounce(function () { table.ajax.reload(); }, 400));

    function debounce(fn, wait) {
        var t;
        return function () {
            clearTimeout(t);
            var args = arguments;
            var ctx = this;
            t = setTimeout(function () { fn.apply(ctx, args); }, wait);
        };
    }

    function openModal(data) {
        clearErrors();
        $('#formCertificate')[0].reset();
        $('#certificate-id').val('');
        $('#image-preview').addClass('d-none').attr('src', '');
        $('#is_featured').prop('checked', false);

        if (data) {
            $('#modal-title').text('Edit Certificate');
            $('#certificate-id').val(data.id);
            $('#title').val(data.title);
            $('#issuer').val(data.issuer);
            $('#certificate_number').val(data.certificate_number || '');
            $('#issued_at').val(data.issued_at ? data.issued_at.substring(0, 10) : '');
            $('#expires_at').val(data.expires_at ? data.expires_at.substring(0, 10) : '');
            $('#credential_url').val(data.credential_url || '');
            $('#display_order').val(data.display_order);
            $('#status').val(data.status);
            $('#image_alt').val(data.image_alt || '');
            $('#description').val(data.description || '');
            $('#is_featured').prop('checked', !!data.is_featured);
            if (data.published_at) {
                $('#published_at').val(data.published_at.substring(0, 16).replace(' ', 'T'));
            }
            if (data.image_url) {
                $('#image-preview').removeClass('d-none').attr('src', data.image_url);
            }
        } else {
            $('#modal-title').text('Add Certificate');
            $('#status').val('DRAFT');
        }

        new bootstrap.Modal(document.getElementById('certificateModal')).show();
    }

    $(document).on('click', '.btn-edit', function () {
        var id = $(this).data('id');
        $.get(window.certificateRoutes.edit + '/' + id, function (res) {
            if (res.success) openModal(res.certificate);
        });
    });

    $(document).on('click', '.btn-delete', function () {
        if (!confirm('Delete this certificate?')) return;
        var id = $(this).data('id');
        $.ajax({
            url: window.certificateRoutes.destroy + '/' + id,
            type: 'DELETE',
            success: function () { table.ajax.reload(null, false); },
            error: function (xhr) { alert(xhr.responseJSON?.message || 'Delete failed'); }
        });
    });

    $(document).on('click', '.btn-publish', function () {
        postAction(window.certificateRoutes.publish + '/' + $(this).data('id') + '/publish');
    });
    $(document).on('click', '.btn-unpublish', function () {
        postAction(window.certificateRoutes.unpublish + '/' + $(this).data('id') + '/unpublish');
    });
    $(document).on('click', '.btn-archive', function () {
        postAction(window.certificateRoutes.archive + '/' + $(this).data('id') + '/archive');
    });

    function postAction(url) {
        $.post(url, function () { table.ajax.reload(null, false); })
            .fail(function (xhr) { alert(xhr.responseJSON?.message || 'Action failed'); });
    }

    $('#image').on('change', function () {
        var file = this.files && this.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#image-preview').removeClass('d-none').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    });

    $('#formCertificate').on('submit', function (e) {
        e.preventDefault();
        clearErrors();
        var id = $('#certificate-id').val();
        var formData = new FormData(this);
        if (!$('#is_featured').is(':checked')) formData.delete('is_featured');
        else formData.set('is_featured', '1');

        var url = id ? window.certificateRoutes.update + '/' + id : window.certificateRoutes.store;
        if (id) formData.append('_method', 'PUT');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                bootstrap.Modal.getInstance(document.getElementById('certificateModal')).hide();
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON?.errors);
                    alert(xhr.responseJSON?.message || 'Validation failed');
                } else {
                    alert(xhr.responseJSON?.message || 'Save failed');
                }
            }
        });
    });
})(jQuery);
