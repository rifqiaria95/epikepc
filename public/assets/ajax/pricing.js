$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var featureItemCounter = 0;
    var priceCleave = null;

    function getRawPriceValue() {
        if (priceCleave) {
            return priceCleave.getRawValue();
        }

        var value = $('#price').val() || '';
        return value.replace(/Rp\s?/g, '').replace(/\./g, '').replace(/,/g, '.');
    }

    function initPriceInput(value) {
        if (typeof Cleave === 'undefined' || !$('#price').length) {
            return;
        }

        if (priceCleave) {
            priceCleave.destroy();
            priceCleave = null;
        }

        priceCleave = new Cleave('#price', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: ',',
            delimiter: '.',
            prefix: 'Rp ',
            noImmediatePrefix: false,
            rawValueTrimPrefix: true
        });

        if (value) {
            priceCleave.setRawValue(value.toString());
        }
    }

    function getFeatureItemTemplate(index) {
        return `
            <div class="card mb-3 feature-item" data-index="${index}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Fitur #${index + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger btn-remove-feature" data-index="${index}">
                        <i class="ti ti-trash me-1"></i>Delete
                    </button>
                </div>
                <div class="card-body">
                    <label class="form-label">Feature Name</label>
                    <input type="text" class="form-control feature-text"
                        name="pricing_features[${index}][feature]"
                        placeholder="e.g. Advanced Analytics">
                </div>
            </div>
        `;
    }

    function addFeatureItem(data) {
        var index = featureItemCounter++;
        $('#pricing-features-container').append(getFeatureItemTemplate(index));

        if (data && data.feature) {
            $('#pricing-features-container .feature-item').last().find('.feature-text').val(data.feature);
        }
    }

    function reindexFeatureItems() {
        $('#pricing-features-container .feature-item').each(function (itemIndex) {
            var $item = $(this);
            $item.attr('data-index', itemIndex);
            $item.data('index', itemIndex);
            $item.find('.card-header h6').text('Fitur #' + (itemIndex + 1));
            $item.find('.btn-remove-feature').attr('data-index', itemIndex);
            $item.find('.feature-text').attr('name', 'pricing_features[' + itemIndex + '][feature]');
        });
    }

    function clearAllFeatureItems() {
        $('#pricing-features-container').empty();
        featureItemCounter = 0;
    }

    $('#btn-add-feature').on('click', function () {
        addFeatureItem();
    });

    $(document).on('click', '.btn-remove-feature', function () {
        $(this).closest('.feature-item').remove();
        reindexFeatureItems();
    });

    $(document).on('click', '.add-new', function () {
        clearAllFeatureItems();
    });

    $('#TablePricing').DataTable({
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
            searchPlaceholder: 'Search..'
        },
        buttons: [
            {
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Pricing</span>',
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
            url: '/frontend/pricing/',
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
            { data: 'price', name: 'price' },
            { data: 'billing_period', name: 'billing_period' },
            { data: 'is_popular', name: 'is_popular' },
            { data: 'is_active', name: 'is_active' },
            { data: 'features_count', name: 'pricing_features_count' },
            { data: 'created_by_name', name: 'created_by' },
            {
                data: 'aksi',
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    var userPermissions = window.userPermissions || [];
                    var canEdit = userPermissions.includes('edit_pricing');
                    var canDelete = userPermissions.includes('delete_pricing');
                    var buttons = '<div class="d-flex align-items-center">';
                    buttons += '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>';
                    buttons += '<div class="dropdown-menu dropdown-menu-end m-0">';

                    if (canEdit) {
                        buttons += '<a href="javascript:;" class="dropdown-item" onclick="editPricing(' + full.id + ')"><i class="ti ti-edit ti-md"></i>Edit</a>';
                    }

                    if (canDelete) {
                        buttons += '<a href="javascript:;" class="dropdown-item delete-record" data-id="' + full.id + '"><i class="ti ti-trash ti-md"></i>Delete</a>';
                    }

                    buttons += '</div></div>';
                    return buttons;
                }
            }
        ],
        order: [[0, 'asc']]
    });

    window.editPricing = function (id) {
        $('#formPricing .form-control, #formPricing .form-select').removeClass('is-invalid');
        $('#formPricing .text-danger.small').text('');

        $.ajax({
            url: '/frontend/pricing/edit/' + id,
            type: 'GET',
            success: function (response) {
                if (!response.success) {
                    toastr.error('Pricing data not found.');
                    return;
                }

                var pricing = response.pricing;
                $('#id').val(pricing.id);
                $('#name').val(pricing.name || '');
                $('#billing_period').val(pricing.billing_period || 'month');
                $('#sort_order').val(pricing.sort_order ?? 0);
                $('#description').val(pricing.description || '');
                $('#button_url').val(pricing.button_url || '');
                $('#is_popular').prop('checked', !!pricing.is_popular);
                $('#is_active').prop('checked', pricing.is_active !== false);

                initPriceInput(pricing.price);

                clearAllFeatureItems();
                if (pricing.pricing_features && pricing.pricing_features.length) {
                    pricing.pricing_features.forEach(function (feature) {
                        addFeatureItem(feature);
                    });
                }

                $('#modal-judul').text('Edit Pricing');
                $('#tambahModal').modal('show');
            },
            error: function () {
                toastr.error('An error occurred while fetching data.');
            }
        });
    };

    $('#formPricing').on('submit', function (e) {
        e.preventDefault();

        var submitBtn = $('#btn-simpan');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Saving...').prop('disabled', true);

        $('#formPricing .form-control, #formPricing .form-select').removeClass('is-invalid');
        $('#formPricing .text-danger.small').text('');

        var formData = new FormData(this);
        formData.set('price', getRawPriceValue());
        formData.set('is_popular', $('#is_popular').is(':checked') ? '1' : '0');
        formData.set('is_active', $('#is_active').is(':checked') ? '1' : '0');

        var id = $('#id').val();
        var url = id ? '/frontend/pricing/update/' + id : '/frontend/pricing/store';
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
                    $('#tambahModal').modal('hide');
                    $('#TablePricing').DataTable().ajax.reload();
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

    $('#tambahModal').on('hidden.bs.modal', function () {
        $('#formPricing')[0].reset();
        $('#id').val('');
        $('#modal-judul').text('Add Pricing');
        $('#is_active').prop('checked', true);
        $('#sort_order').val(0);
        clearAllFeatureItems();
        initPriceInput('');
        $('#formPricing .form-control, #formPricing .form-select').removeClass('is-invalid');
        $('#formPricing .text-danger.small').text('');
    });

    $('#tambahModal').on('shown.bs.modal', function () {
        if (!$('#id').val()) {
            initPriceInput('');
        }
    });

    initPriceInput('');

    $(document).on('click', '.delete-record', function () {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Pricing data will be deleted!',
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
                url: '/frontend/pricing/delete/' + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status === 200) {
                        toastr.success(response.message);
                        $('#TablePricing').DataTable().ajax.reload();
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
