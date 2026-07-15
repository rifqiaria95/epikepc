$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Counter untuk detail items
    var detailItemCounter = 0;

    // Template untuk detail item
    function getDetailItemTemplate(index) {
        return `
            <div class="card mb-4 detail-item" data-index="${index}" style="box-shadow: none; border: 1px solid #d9dee3;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Item #${index + 1}</h5>
                    <button type="button" class="btn btn-sm btn-danger btn-remove-detail" data-index="${index}">
                        <i class="ti ti-trash me-1"></i>Delete
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-4 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control detail-title" 
                                name="service_details[${index}][title]"
                                placeholder="Enter title service detail" />
                        </div>
                        <div class="col-xl-4 mb-3">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control detail-subtitle" 
                                name="service_details[${index}][subtitle]"
                                placeholder="Enter subtitle service detail" />
                        </div>
                        <div class="col-xl-4 mb-3">
                            <label class="form-label">Price</label>
                            <input type="text" class="form-control detail-price" 
                                name="service_details[${index}][price]"
                                data-price-raw=""
                                placeholder="Enter harga service" />
                        </div>
                        <div class="col-xl-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control detail-description" 
                                name="service_details[${index}][description]"
                                id="detail_description_${index}"
                                placeholder="Enter deskripsi service detail..." rows="10"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Fungsi untuk menambah detail item
    function addDetailItem(data) {
        var index = detailItemCounter++;
        var html = getDetailItemTemplate(index);
        $('#service-details-container').append(html);

        // Inisialisasi TinyMCE untuk field description baru
        if (typeof tinymce !== 'undefined') {
            setTimeout(function() {
                initializeTinyMCEForDetail('detail_description_' + index, data ? data.description : '');
            }, 200);
        }

        // Isi data jika ada (untuk edit) - lakukan sebelum inisialisasi Cleave
        var priceValueToSet = null;
        if (data) {
            var $item = $('#service-details-container .detail-item').last();
            $item.find('.detail-title').val(data.title || '');
            $item.find('.detail-subtitle').val(data.subtitle || '');
            priceValueToSet = data.price || '';
        }

        // Inisialisasi Cleave.js untuk format rupiah pada field price
        setTimeout(function() {
            var $item = $('#service-details-container .detail-item').last();
            var $priceInput = $item.find('.detail-price');
            
            if ($priceInput.length > 0 && typeof Cleave !== 'undefined') {
                var cleavePrice = new Cleave($priceInput[0], {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalMark: ',',
                    delimiter: '.',
                    prefix: 'Rp ',
                    noImmediatePrefix: false,
                    rawValueTrimPrefix: true
                });
                
                // Set nilai jika ada data
                if (priceValueToSet) {
                    cleavePrice.setRawValue(priceValueToSet.toString());
                }
                
                // Save instance cleave untuk digunakan nanti
                $priceInput.data('cleave', cleavePrice);
            }
        }, 100);
    }

    // Fungsi untuk format rupiah
    function formatRupiah(value) {
        if (!value) return '';
        var number = parseFloat(value);
        if (isNaN(number)) return '';
        return 'Rp ' + number.toLocaleString('id-ID');
    }

    // Fungsi untuk mendapatkan nilai angka dari format rupiah
    function getRawPriceValue($input) {
        var cleaveInstance = $input.data('cleave');
        if (cleaveInstance) {
            return cleaveInstance.getRawValue();
        }
        // Fallback: hapus format manual
        var value = $input.val();
        if (value) {
            value = value.replace(/Rp\s?/g, '').replace(/\./g, '').replace(/,/g, '.');
            return parseFloat(value) || 0;
        }
        return 0;
    }

    // Fungsi untuk menghapus detail item
    function removeDetailItem($item) {
        if (!$item || $item.length === 0) {
            return;
        }
        
        // Delete Cleave instance jika ada
        var $priceInput = $item.find('.detail-price');
        if ($priceInput.length > 0) {
            var cleaveInstance = $priceInput.data('cleave');
            if (cleaveInstance && typeof cleaveInstance.destroy === 'function') {
                try {
                    cleaveInstance.destroy();
                } catch (e) {
                    // Silent fail
                }
            }
        }
        
        // Delete TinyMCE instance jika ada - cari berdasarkan textarea di dalam item
        var $textarea = $item.find('.detail-description');
        if ($textarea.length > 0) {
            var editorId = $textarea.attr('id');
            if (editorId && typeof tinymce !== 'undefined') {
                var editor = tinymce.get(editorId);
                if (editor) {
                    try {
                        editor.remove();
                    } catch (e) {
                        // Silent fail
                    }
                }
            }
        }
        
        // Delete item dari DOM
        var domElement = $item[0];
        
        if (!domElement) {
            return;
        }
        
        var parentElement = domElement.parentNode;
        
        // Delete dari DOM menggunakan native method
        if (parentElement) {
            try {
                parentElement.removeChild(domElement);
            } catch (e) {
                // Fallback ke jQuery
                try {
                    $item.remove();
                } catch (e2) {
                    // Last resort: gunakan remove() method modern
                    if (domElement.remove) {
                        domElement.remove();
                    }
                }
            }
        } else {
            $item.remove();
        }
        
        // Re-index semua items setelah penghapusan
        setTimeout(function() {
            reindexDetailItems();
        }, 50);
    }

    // Fungsi untuk re-index detail items
    function reindexDetailItems() {
        $('#service-details-container .detail-item').each(function(index) {
            var $item = $(this);
            var oldIndex = $item.data('index') || $item.attr('data-index');
            var newIndex = index;
            
            // Update data-index
            $item.attr('data-index', newIndex);
            $item.data('index', newIndex);
            
            // Update header text
            $item.find('.card-header h5').text('Detail Item #' + (newIndex + 1));
            
            // Update button data-index
            $item.find('.btn-remove-detail').attr('data-index', newIndex);
            $item.find('.btn-remove-detail').data('index', newIndex);
            
            // Update name attributes
            $item.find('.detail-title').attr('name', 'service_details[' + newIndex + '][title]');
            $item.find('.detail-subtitle').attr('name', 'service_details[' + newIndex + '][subtitle]');
            
            // Update price field dan reinitialize Cleave
            var $priceInput = $item.find('.detail-price');
            var currentPriceValue = getRawPriceValue($priceInput);
            $priceInput.attr('name', 'service_details[' + newIndex + '][price]');
            
            // Reinitialize Cleave dengan nilai yang sama
            if (typeof Cleave !== 'undefined') {
                var oldCleave = $priceInput.data('cleave');
                if (oldCleave) {
                    oldCleave.destroy();
                }
                var cleavePrice = new Cleave($priceInput[0], {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalMark: ',',
                    delimiter: '.',
                    prefix: 'Rp ',
                    noImmediatePrefix: false,
                    rawValueTrimPrefix: true
                });
                if (currentPriceValue) {
                    cleavePrice.setRawValue(currentPriceValue.toString());
                }
                $priceInput.data('cleave', cleavePrice);
            }
            
            // Update TinyMCE editor ID dan name
            var oldEditorId = 'detail_description_' + oldIndex;
            var newEditorId = 'detail_description_' + newIndex;
            
            if (typeof tinymce !== 'undefined' && tinymce.get(oldEditorId)) {
                var content = tinymce.get(oldEditorId).getContent();
                try {
                    tinymce.get(oldEditorId).remove();
                } catch (e) {
                    // Silent fail
                }
                
                $item.find('.detail-description').attr('id', newEditorId);
                $item.find('.detail-description').attr('name', 'service_details[' + newIndex + '][description]');
                
                // Re-initialize TinyMCE dengan ID baru
                setTimeout(function() {
                    initializeTinyMCEForDetail(newEditorId, content);
                }, 100);
            } else {
                $item.find('.detail-description').attr('id', newEditorId);
                $item.find('.detail-description').attr('name', 'service_details[' + newIndex + '][description]');
            }
        });
    }

    // Fungsi untuk inisialisasi TinyMCE untuk detail item
    function initializeTinyMCEForDetail(editorId, content) {
        if (typeof tinymce !== 'undefined' && $('#' + editorId).length > 0) {
            // Remove existing instance if any
            if (tinymce.get(editorId)) {
                tinymce.get(editorId).remove();
            }

            setTimeout(function() {
                tinymce.init({
                    selector: '#' + editorId,
                    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount code fullscreen preview',
                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | code fullscreen preview | removeformat',
                    menubar: false,
                    height: 300,
                    branding: false,
                    content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
                    image_advtab: true,
                    image_caption: true,
                    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
                    noneditable_noneditable_class: 'mceNonEditable',
                    toolbar_mode: 'sliding',
                    contextmenu: 'link image table',
                    z_index: 10000,
                    setup: function(editor) {
                        editor.on('change', function() {
                            editor.save();
                        });
                        editor.on('blur', function() {
                            editor.save();
                        });
                    },
                    init_instance_callback: function(editor) {
                        if (content) {
                            editor.setContent(content);
                        }
                        editor.getContainer().style.transition =
                            'border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out';
                        $(editor.getContainer()).css('z-index', '10000');
                        $(editor.getContainer()).find('.tox-menu, .tox-pop, .tox-toolbar').css('z-index', '10001');
                    }
                });
            }, 50);
        }
    }

    // Event handler untuk tombol tambah detail
    $(document).on('click', '#btn-add-detail', function() {
        addDetailItem();
    });

    // Event handler untuk tombol hapus detail
    $(document).on('click', '.btn-remove-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $button = $(this);
        var $item = $button.closest('.detail-item');
        
        // Jika tidak ditemukan, coba dengan parents
        if ($item.length === 0) {
            $item = $button.parents('.detail-item').first();
        }
        
        if ($item.length === 0) {
            toastr.error('Failed to find detail item to delete');
            return false;
        }
        
        // Save reference langsung ke DOM element untuk memastikan tetap ada
        var itemElement = $item[0];
        var itemDataIndex = $item.attr('data-index') || $item.data('index');
        
        // Set ID unik jika belum ada
        if (!itemElement.id) {
            itemElement.id = 'detail-item-' + itemDataIndex + '-' + Date.now();
        }
        var itemId = itemElement.id;
        
        // Save reference ke jQuery object dan DOM element sebelum async operation
        var $itemToRemove = $item;
        var itemToRemoveElement = itemElement;
        var itemToRemoveId = itemId;
        
        // Cek apakah Swal tersedia
        if (typeof Swal === 'undefined' || typeof Swal.fire !== 'function') {
            removeDetailItem($itemToRemove);
            return false;
        }
        
        // Fungsi untuk menghapus item setelah konfirmasi
        var performRemoval = function() {
            // Coba gunakan reference yang sudah disimpan
            var elementToRemove = document.getElementById(itemToRemoveId);
            
            if (!elementToRemove) {
                elementToRemove = itemToRemoveElement;
            }
            
            if (elementToRemove && document.body.contains(elementToRemove)) {
                removeDetailItem($(elementToRemove));
            } else if ($itemToRemove.length > 0 && $itemToRemove.parent().length > 0) {
                removeDetailItem($itemToRemove);
            } else {
                // Cari lagi berdasarkan data-index
                var $foundItem = $('#service-details-container .detail-item[data-index="' + itemDataIndex + '"]');
                if ($foundItem.length > 0) {
                    removeDetailItem($foundItem);
                } else {
                    toastr.error('Failed to delete detail item');
                }
            }
        };
        
        try {
            // Set z-index untuk Swal agar muncul di atas modal
            if (typeof Swal !== 'undefined') {
                Swal.mixin({
                    customClass: {
                        container: 'swal2-container-custom'
                    }
                });
            }
            
            var swalPromise = Swal.fire({
                title: 'Are you sure?',
                text: "This detail item will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Delete',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary',
                    container: 'swal2-container-custom'
                },
                allowOutsideClick: false,
                allowEscapeKey: true,
                didOpen: function() {
                    // Set z-index secara manual setelah Swal dibuka
                    setTimeout(function() {
                        var swalContainer = document.querySelector('.swal2-container');
                        if (swalContainer) {
                            swalContainer.style.zIndex = '10010';
                            // Pastikan container dipindahkan ke body jika belum
                            if (swalContainer.parentElement !== document.body) {
                                document.body.appendChild(swalContainer);
                            }
                        }
                        var swalPopup = document.querySelector('.swal2-popup');
                        if (swalPopup) {
                            swalPopup.style.zIndex = '10011';
                        }
                        var swalBackdrop = document.querySelector('.swal2-backdrop-show');
                        if (!swalBackdrop) {
                            swalBackdrop = document.querySelector('.swal2-backdrop');
                        }
                        if (swalBackdrop) {
                            swalBackdrop.style.zIndex = '10009';
                        }
                    }, 10);
                }
            });
            
            if (swalPromise && typeof swalPromise.then === 'function') {
                swalPromise.then(function(result) {
                    if (result && result.isConfirmed) {
                        performRemoval();
                    }
                }).catch(function(error) {
                    performRemoval();
                });
            } else {
                performRemoval();
            }
        } catch (error) {
            performRemoval();
        }
        
        return false;
    });

    // Clear detail items saat tombol "Add Service" diklik
    $(document).on('click', '.add-new', function() {
        clearAllDetailItems();
    });

    $('#TableServices').DataTable({
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
              extend: 'collection',
              className: 'btn btn-label-secondary dropdown-toggle mx-4 waves-effect waves-light',
              text: '<i class="ti ti-upload me-2 ti-xs"></i>Export',
              buttons: [
                {
                  extend: 'print',
                  text: '<i class="ti ti-printer me-2" ></i>Print',
                  className: 'dropdown-item',
                  exportOptions: {
                    columns: [1, 2, 3, 4, 5],
                    // prevent avatar to be print
                    format: {
                      body: function (inner, coldex, rowdex) {
                        if (inner.length <= 0) return inner;
                        var el = $.parseHTML(inner);
                        var result = '';
                        $.each(el, function (index, perusahaan) {
                          if (perusahaan.classList !== undefined && perusahaan.classList.contains('perusahaan-nm_item')) {
                            result = result + perusahaan.lastChild.firstChild.textContent;
                          } else if (perusahaan.innerText === undefined) {
                            result = result + perusahaan.textContent;
                          } else result = result + perusahaan.innerText;
                        });
                        return result;
                      }
                    }
                  },
                  customize: function (win) {
                    //customize print view for dark
                    $(win.document.body)
                      .css('color', headingColor)
                      .css('border-color', borderColor)
                      .css('background-color', bodyBg);
                    $(win.document.body)
                      .find('table')
                      .addClass('compact')
                      .css('color', 'inherit')
                      .css('border-color', 'inherit')
                      .css('background-color', 'inherit');
                  }
                },
                {
                  extend: 'csv',
                  text: '<i class="ti ti-file-text me-2" ></i>Csv',
                  className: 'dropdown-item',
                  exportOptions: {
                    columns: [1, 2, 3, 4, 5],
                    // prevent avatar to be display
                    format: {
                      body: function (inner, coldex, rowdex) {
                        if (inner.length <= 0) return inner;
                        var el = $.parseHTML(inner);
                        var result = '';
                        $.each(el, function (index, perusahaan) {
                          if (perusahaan.classList !== undefined && perusahaan.classList.contains('perusahaan-nm_item')) {
                            result = result + perusahaan.lastChild.firstChild.textContent;
                          } else if (perusahaan.innerText === undefined) {
                            result = result + perusahaan.textContent;
                          } else result = result + perusahaan.innerText;
                        });
                        return result;
                      }
                    }
                  }
                },
                {
                  extend: 'excel',
                  text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
                  className: 'dropdown-item',
                  exportOptions: {
                    columns: [1, 2, 3, 4, 5],
                    // prevent avatar to be display
                    format: {
                      body: function (inner, coldex, rowdex) {
                        if (inner.length <= 0) return inner;
                        var el = $.parseHTML(inner);
                        var result = '';
                        $.each(el, function (index, perusahaan) {
                          if (perusahaan.classList !== undefined && perusahaan.classList.contains('perusahaan-nm_item')) {
                            result = result + perusahaan.lastChild.firstChild.textContent;
                          } else if (perusahaan.innerText === undefined) {
                            result = result + perusahaan.textContent;
                          } else result = result + perusahaan.innerText;
                        });
                        return result;
                      }
                    }
                  }
                },
                {
                  extend: 'pdf',
                  text: '<i class="ti ti-file-code-2 me-2"></i>Pdf',
                  className: 'dropdown-item',
                  exportOptions: {
                    columns: [1, 2, 3, 4, 5],
                    // prevent avatar to be display
                    format: {
                      body: function (inner, coldex, rowdex) {
                        if (inner.length <= 0) return inner;
                        var el = $.parseHTML(inner);
                        var result = '';
                        $.each(el, function (index, perusahaan) {
                          if (perusahaan.classList !== undefined && perusahaan.classList.contains('perusahaan-nm_item')) {
                            result = result + perusahaan.lastChild.firstChild.textContent;
                          } else if (perusahaan.innerText === undefined) {
                            result = result + perusahaan.textContent;
                          } else result = result + perusahaan.innerText;
                        });
                        return result;
                      }
                    }
                  }
                },
                {
                  extend: 'copy',
                  text: '<i class="ti ti-copy me-2" ></i>Copy',
                  className: 'dropdown-item',
                  exportOptions: {
                    columns: [1, 2, 3, 4, 5],
                    // prevent avatar to be display
                    format: {
                      body: function (inner, coldex, rowdex) {
                        if (inner.length <= 0) return inner;
                        var el = $.parseHTML(inner);
                        var result = '';
                        $.each(el, function (index, perusahaan) {
                          if (perusahaan.classList !== undefined && perusahaan.classList.contains('perusahaan-nm_item')) {
                            result = result + perusahaan.lastChild.firstChild.textContent;
                          } else if (perusahaan.innerText === undefined) {
                            result = result + perusahaan.textContent;
                          } else result = result + perusahaan.innerText;
                        });
                        return result;
                      }
                    }
                  }
                }
              ]
            },
            {
              text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Service</span>',
              className: 'add-new btn btn-primary waves-effect waves-light',
              attr: {
                'data-bs-toggle': 'modal',
                'data-bs-target': '#tambahModal',
              }
            }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: "/services/service_list/",
            type: 'GET'
        },
        columns: [
            {
                data: null,
                name: 'id',
                title: 'No',
                orderable: false,
                searchable: false,
                render: function (data, type, full, meta) {
                    // Mengembalikan nomor urut otomatis berdasarkan index baris
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'image',
                name: 'image',
                render: function (data, type, full, meta) {
                    if (data) {
                        // Data sudah berupa URL lengkap dari backend
                        return '<img src="' + data + '" alt="Image" class="img-fluid" style="width: 30px; height: 30px;">';
                    } else {
                        return '<span class="badge bg-secondary">No Image</span>';
                    }
                }
            },
            {
                data: 'title',
                name: 'title'
            },
            {
                data: 'subtitle',
                name: 'subtitle'
            },
            {
                data: 'description',
                name: 'description',
                render: function (data, type, full, meta) {
                    // Tampilkan maksimal 3 kata saja, lalu tambahkan "..."
                    if (!data) return '';
                    // Hilangkan tag HTML jika ada
                    var text = $('<div>').html(data).text();
                    var words = text.trim().split(/\s+/);
                    if (words.length > 3) {
                        return words.slice(0, 3).join(' ') + ' ...';
                    } else {
                        return text;
                    }
                }
            },
            {
                data: 'service_type',
                name: 'service_type_id',
                render: function (data, type, full, meta) {
                    if (data && data.name) {
                        return data.name;
                    }
                    return '-';
                }
            },
            {
                data: 'aksi',
                name: 'aksi',
                render: function (data, type, full, meta) {
                  var userPermissions = window.userPermissions || [];
                  var canEdit         = userPermissions.includes("edit_services");
                  var canDelete       = userPermissions.includes("delete_services");

                  var buttons = '<div class="d-flex align-items-center">';

                  buttons += '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>';
                    buttons += '<div class="dropdown-menu dropdown-menu-end m-0">';
                                          if (canEdit) {
                        buttons += '<a href="javascript:;" class="dropdown-item" onclick="ViewData(' + full.id + ')"><i class="ti ti-edit ti-md"></i>Edit</a>';
                      }
                    if (canDelete) {
                      buttons += '<a href="javascript:;" class="dropdown-item delete-record" data-id="' + full.id + '"><i class="ti ti-trash ti-md"></i>Delete</a>';
                    }
                    buttons += '</div>';

                  buttons += '</div>';

                  return buttons;
                }
            }
        ],
        order: [
            [0, 'asc']
        ],

    });

    // Inisialisasi Select2 untuk service_type_id
    if ($('#service_type_id').length) {
        $('#service_type_id').select2({
            dropdownParent: $('body'),
            placeholder: 'Select Service Type',
            allowClear: true,
            width: '100%'
        });
    }

    // Global function untuk edit service
    window.ViewData = function (id) {

        $('#formServices .form-control, #formServices .form-select').removeClass('is-invalid');
        $('#formServices .text-danger.small').text('');

        var ajaxUrl = '/services/service_list/edit/' + id;

        $.ajax({
            url: ajaxUrl,
            dataType: 'json',
            type: 'GET',
            beforeSend: function(xhr) {
            },
            success: function(response) {
                if (response.success) {
                    var service = response.service;
                    $('#id').val(service.id);
                    $('#title').val(service.title);
                    $('#subtitle').val(service.subtitle);
                    $('#description').val(service.description);
                    $('#service_type_id').val(service.service_type_id).trigger('change');

                    // Load service details jika ada
                    clearAllDetailItems();
                    if (service.service_details && service.service_details.length > 0) {
                        service.service_details.forEach(function(detail) {
                            addDetailItem(detail);
                        });
                    } else if (service.service_detail) {
                        // Backward compatibility: jika masih menggunakan service_detail (single)
                        addDetailItem(service.service_detail);
                    }

                    $('#modal-judul').text('Edit Service');

                    $('#tambahModal').modal('show');
                    
                    // Initialize TinyMCE untuk semua detail items setelah modal dibuka
                    $('#tambahModal').one('shown.bs.modal', function() {
                        // Jika tab Service Details aktif, initialize TinyMCE untuk semua items
                        setTimeout(function() {
                            if ($('#navs-top-profile').hasClass('active')) {
                                $('#service-details-container .detail-description').each(function() {
                                    var editorId = $(this).attr('id');
                                    if (editorId && typeof tinymce !== 'undefined' && !tinymce.get(editorId)) {
                                        var content = $(this).val();
                                        initializeTinyMCEForDetail(editorId, content);
                                    }
                                });
                            }
                        }, 300);
                    });
                } else {
                    toastr.error('Service data not found.');
                }
            },
            error: function(xhr, status, error) {
                // Check if response is HTML instead of JSON
                if (xhr.responseText && xhr.responseText.includes('<html>')) {
                    toastr.error('Server error: Receiving HTML instead of JSON response');
                } else {
                    toastr.error('AJAX Error: ' + status + ' - ' + error);
                }
            }
        });
    }

    $('#formServices').on('submit', function(e){
        e.preventDefault();

        // Addkan loader pada tombol submit
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Saving...').prop('disabled', true);

        $('#formServices .form-control, #formServices .form-select').removeClass('is-invalid');
        $('#formServices .text-danger.small').text('');

        // Save semua TinyMCE content sebelum form submission
        if (typeof tinymce !== 'undefined') {
            // Save semua editor detail description
            $('#service-details-container .detail-description').each(function() {
                var editorId = $(this).attr('id');
                if (editorId && tinymce.get(editorId)) {
                    try {
                        tinymce.get(editorId).save();
                    } catch (e) {
                        // Silent fail
                    }
                }
            });
            
            // Juga coba save dengan cara iterasi editors jika tersedia
            if (tinymce.editors && Array.isArray(tinymce.editors)) {
                tinymce.editors.forEach(function(editor) {
                    if (editor && editor.id && editor.id.startsWith('detail_description_')) {
                        try {
                            editor.save();
                        } catch (e) {
                            // Silent fail
                        }
                    }
                });
            }
        }

        // Konversi price dari format rupiah ke angka sebelum submit
        $('#service-details-container .detail-price').each(function() {
            var $priceInput = $(this);
            var rawValue = getRawPriceValue($priceInput);
            // Set nilai raw ke hidden input or langsung ke formData
            $priceInput.attr('data-price-raw', rawValue);
        });

        var formData = new FormData(this);
        
        // Update price values dengan nilai raw (tanpa format)
        $('#service-details-container .detail-price').each(function() {
            var $priceInput = $(this);
            var rawValue = $priceInput.attr('data-price-raw') || getRawPriceValue($priceInput);
            var name = $priceInput.attr('name');
            if (name) {
                formData.set(name, rawValue);
            }
        });
        var id = $('#id').val();
        var url = '';
        var method = '';

        if(id){
            url = '/services/service_list/update/' + id;
            method = 'POST';
            formData.append('_method', 'PUT');
        } else {
            url = '/services/service_list/store';
            method = 'POST';
        }

        $.ajax({
            url: url,
            type: method,
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if (response.status === 200) {
                    $('#tambahModal').modal('hide');
                    $('#TableServices').DataTable().ajax.reload();
                    toastr.success('Data saved successfully!');
                } else {
                    toastr.error('Something went wrong, please try again.');
                }
                // Kembalikan tombol ke kondisi semula
                submitBtn.html(originalText).prop('disabled', false);
            },
            error: function (xhr) {
              if (xhr.status === 422) {
                  var errors = xhr.responseJSON.errors;
                  $.each(errors, function (key, value) {
                      $('#' + key).addClass('is-invalid');
                      $('#' + key + '-error').text(value[0]);
                  });
              } else {
                  toastr.error('Failed to save data.');
              }
              // Kembalikan tombol ke kondisi semula
              submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    $('#tambahModal').on('hidden.bs.modal', function () {
        $('#formServices')[0].reset();
        $('#id').val('');
        $('#modal-judul').text('Add Service');
        $('#formServices .form-control, #formServices .form-select').removeClass('is-invalid');
        $('#formServices .text-danger.small').text('');

        // Clear semua detail items
        clearAllDetailItems();

        // Clear Select2 safely
        try {
            $('#service_type_id').val('').trigger('change');
        } catch (e) {
            // Ignore errors
        }
    });

        // Event listener untuk memastikan Select2 terinisialisasi saat modal dibuka
    $('#tambahModal').on('shown.bs.modal', function () {
        // Ensure modal is moved to body if not already (Bootstrap should do this automatically)
        var $modal = $('#tambahModal');
        if ($modal.parent().is('body') === false) {
            $modal.appendTo('body');
        }
        
        // Set z-index explicitly via JavaScript to ensure it's above header
        $modal.css('z-index', '9999');
        $('.modal-backdrop').css('z-index', '9998');
        
        // Re-inisialisasi Select2 saat modal dibuka jika diperlukan
        if ($('#service_type_id').length && !$('#service_type_id').hasClass('select2-hidden-accessible')) {
            $('#service_type_id').select2({
                dropdownParent: $('body'),
                placeholder: 'Select Service Type',
                allowClear: true,
                width: '100%'
            });
        }

        // Reset detail items counter saat modal dibuka
        if ($('#service-details-container').children().length === 0) {
            detailItemCounter = 0;
        }
    });

    // Fungsi untuk clear semua detail items
    function clearAllDetailItems() {
        // Remove semua Cleave instances
        $('#service-details-container .detail-price').each(function() {
            var $priceInput = $(this);
            var cleaveInstance = $priceInput.data('cleave');
            if (cleaveInstance && typeof cleaveInstance.destroy === 'function') {
                try {
                    cleaveInstance.destroy();
                } catch (e) {
                    // Silent fail
                }
            }
        });
        
        // Remove semua TinyMCE instances dengan cara yang lebih aman
        if (typeof tinymce !== 'undefined') {
            // Cari semua textarea dengan ID yang dimulai dengan detail_description_
            $('#service-details-container .detail-description').each(function() {
                var editorId = $(this).attr('id');
                if (editorId && tinymce.get(editorId)) {
                    try {
                        tinymce.get(editorId).remove();
                    } catch (e) {
                        // Silent fail
                    }
                }
            });
            
            // Juga coba hapus dengan cara iterasi editors jika tersedia
            if (tinymce.editors && Array.isArray(tinymce.editors)) {
                tinymce.editors.forEach(function(editor) {
                    if (editor && editor.id && editor.id.startsWith('detail_description_')) {
                        try {
                            editor.remove();
                        } catch (e) {
                            // Silent fail
                        }
                    }
                });
            }
        }
        
        // Clear container dan reset counter
        $('#service-details-container').empty();
        detailItemCounter = 0;
    }

    // Function to initialize TinyMCE (untuk backward compatibility)
    function initializeTinyMCE() {
        if (typeof tinymce !== 'undefined' && $('#detail_description').length > 0) {
            // Remove existing instance if any
            if (tinymce.get('detail_description')) {
                tinymce.get('detail_description').remove();
            }

            // Wait a bit to ensure element is visible
            setTimeout(function() {
                // Initialize TinyMCE
                tinymce.init({
                    selector: '#detail_description',
                    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount code fullscreen preview',
                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | code fullscreen preview | removeformat',
                    menubar: false,
                    height: 400,
                    branding: false,
                    content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
                    image_advtab: true,
                    image_caption: true,
                    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
                    noneditable_noneditable_class: 'mceNonEditable',
                    toolbar_mode: 'sliding',
                    contextmenu: 'link image table',
                    // Set z-index untuk dropdown dan menu
                    z_index: 10000,
                    setup: function(editor) {
                        editor.on('change', function() {
                            editor.save();
                        });
                        editor.on('blur', function() {
                            editor.save();
                        });
                    },
                    init_instance_callback: function(editor) {
                        editor.getContainer().style.transition =
                            'border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out';
                        
                        // Set z-index untuk semua elemen TinyMCE
                        $(editor.getContainer()).css('z-index', '10000');
                        $(editor.getContainer()).find('.tox-menu, .tox-pop, .tox-toolbar').css('z-index', '10001');
                        
                        // Focus editor untuk memastikan cursor bisa muncul
                        setTimeout(function() {
                            editor.focus();
                        }, 100);
                    }
                });
            }, 50);
        }
    }

    // Initialize TinyMCE dan Cleave untuk semua detail items saat tab "Service Details" aktif
    $(document).on('shown.bs.tab', 'button[data-bs-target="#navs-top-profile"]', function() {
        setTimeout(function() {
            // Initialize TinyMCE
            $('#service-details-container .detail-description').each(function() {
                var editorId = $(this).attr('id');
                if (editorId && typeof tinymce !== 'undefined' && !tinymce.get(editorId)) {
                    var content = $(this).val();
                    initializeTinyMCEForDetail(editorId, content);
                }
            });
            
            // Initialize Cleave untuk format rupiah pada field price
            if (typeof Cleave !== 'undefined') {
                $('#service-details-container .detail-price').each(function() {
                    var $priceInput = $(this);
                    // Cek apakah sudah ada cleave instance
                    if (!$priceInput.data('cleave')) {
                        var currentValue = $priceInput.val();
                        var rawValue = currentValue ? currentValue.replace(/Rp\s?/g, '').replace(/\./g, '').replace(/,/g, '.') : '';
                        
                        var cleavePrice = new Cleave($priceInput[0], {
                            numeral: true,
                            numeralThousandsGroupStyle: 'thousand',
                            numeralDecimalMark: ',',
                            delimiter: '.',
                            prefix: 'Rp ',
                            noImmediatePrefix: false,
                            rawValueTrimPrefix: true
                        });
                        
                        if (rawValue) {
                            cleavePrice.setRawValue(rawValue);
                        }
                        
                        $priceInput.data('cleave', cleavePrice);
                    }
                });
            }
        }, 200);
    });
    
    // Also set z-index when modal is about to show
    $('#tambahModal').on('show.bs.modal', function () {
        var $modal = $('#tambahModal');
        $modal.css('z-index', '9999');
    });

    // Event listener untuk delete
    $(document).on('click', '.delete-record', function () {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Service data will be deleted!",
            icon: 'warning',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light ml-3',
                cancelButton: 'btn btn-label-secondary waves-effect waves-light'
            },
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            buttonsStyling: false,
            didRender: function () {
                $('.swal2-actions').css('gap', '10px');
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/services/service_list/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _method: 'DELETE',
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
                            $('#TableServices').DataTable().ajax.reload();
                        } else {
                            Swal.fire(
                                'Error!',
                                response.errors,
                                'error'
                            );
                        }
                    },
                    error: function () {
                        Swal.fire(
                            'Oops!',
                            'An error occurred while deleting data.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
