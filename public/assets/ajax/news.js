$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#TableNews').DataTable({
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
              text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Berita</span>',
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
            url: "/portfolio/news/",
            type: 'GET'
        },
        columns: [
            {
                data: null,
                name: 'no_urut',
                title: 'No',
                orderable: false,
                searchable: false,
                render: function (data, type, full, meta) {
                    // Mengembalikan nomor urut otomatis berdasarkan index baris
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'thumbnail',
                name: 'thumbnail',
                render: function (data, type, full, meta) {
                    if (data) {
                        // Data sudah berupa URL lengkap dari backend
                        return '<img src="' + data + '" alt="Thumbnail" class="img-fluid" style="width: 30px; height: 30px;">';
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
                data: 'slug',
                name: 'slug'
            },
            {
                data: 'content',
                name: 'content',
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
                data: 'summary',
                name: 'summary',
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
                data: 'author',
                name: 'author'
            },
            {
                data: 'category',
                name: 'category'
            },
            {
                data: 'tags',
                name: 'tags'
            },
            {
                data: 'status',
                name: 'status',
                render: function (data, type, full, meta) {
                    var labelClass = '';
                    var labelText = '';
                    if (data === 'draft') {
                        labelClass = 'badge bg-dark';
                        labelText = 'Draft';
                    } else if (data === 'archived') {
                        labelClass = 'badge bg-secondary';
                        labelText = 'Archived';
                    } else if (data === 'published') {
                        labelClass = 'badge bg-success';
                        labelText = 'Published';
                    } else {
                        labelClass = 'badge bg-light';
                        labelText = data;
                    }
                    return '<span class="' + labelClass + '">' + labelText + '</span>';
                }
            },
            {
                data: 'published_at',
                name: 'published_at'
            },
            {
                data: 'aksi',
                name: 'aksi',
                render: function (data, type, full, meta) {
                  var userPermissions = window.userPermissions || [];
                  var canEdit         = userPermissions.includes("edit_news");
                  var canDelete       = userPermissions.includes("delete_news");

                  var buttons = '<div class="d-flex align-items-center">';

                  buttons += '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>';
                    buttons += '<div class="dropdown-menu dropdown-menu-end m-0">';
                                          if (canEdit) {
                        buttons += '<a href="javascript:;" class="dropdown-item" onclick="ViewData(\'' + full.id + '\')"><i class="ti ti-edit ti-md"></i>Edit</a>';
                      }
                    if (canDelete) {
                      buttons += '<a href="javascript:;" class="dropdown-item delete-record" data-id="' + full.id + '"><i class="ti ti-trash ti-md"></i>Hapus</a>';
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

    // Inisialisasi Select2 setelah modal siap
    function initializeSelect2() {
        // Destroy existing select2 instances jika ada
        if ($('#category_id').hasClass('select2-hidden-accessible')) {
            $('#category_id').select2('destroy');
        }
        if ($('#tags_id').hasClass('select2-hidden-accessible')) {
            $('#tags_id').select2('destroy');
        }

        // Inisialisasi ulang Select2 dengan body sebagai parent
        $('#category_id').select2({
            dropdownParent: $('body'),
            placeholder: 'Pilih Kategori',
            allowClear: true,
            width: '100%'
        });

        $('#tags_id').select2({
            dropdownParent: $('body'),
            placeholder: 'Pilih Tag',
            allowClear: true,
            width: '100%'
        });
    }

    // Inisialisasi Select2 saat pertama kali load
    initializeSelect2();

    // Global function untuk edit news
    window.ViewData = function (id) {

        $('#formNews .form-control, #formNews .form-select').removeClass('is-invalid');
        $('#formNews .text-danger.small').text('');

        // Use debug route if user doesn't have permission
        var ajaxUrl = userPermissions.includes('edit_news')
            ? '/portfolio/news/edit/' + id
            : '/portfolio/news/debug-edit/' + id;

        $.ajax({
            url: ajaxUrl,
            dataType: 'json',
            type: 'GET',
            beforeSend: function(xhr) {
            },
            success: function(response) {
                if (response.success) {
                    var news = response.news;
                    $('#id').val(news.id);
                    $('#title').val(news.title);
                    $('#slug').val(news.slug);

                    // Set content to TinyMCE editor
                    if (tinymce.get('content')) {
                        tinymce.get('content').setContent(news.content || '');
                    } else {
                        $('#content').val(news.content);
                    }

                    $('#summary').val(news.summary);
                    $('#status').val(news.status);

                                    // Set kategori dan tag safely

                try {
                    // Re-inisialisasi Select2 sebelum set nilai
                    initializeSelect2();

                    // Handle category as array (multiple categories)
                    setTimeout(function() {
                        if (news.category_id && Array.isArray(news.category_id)) {
                            var categoryValue = news.category_id.length > 0 ? news.category_id[0] : '';
                            $('#category_id').val(categoryValue).trigger('change');
                        } else if (news.category_id) {
                            $('#category_id').val(news.category_id).trigger('change');
                        } else {
                            $('#category_id').val('').trigger('change');
                        }

                        // Handle tags as array
                        if (news.tags_id && Array.isArray(news.tags_id)) {
                            $('#tags_id').val(news.tags_id).trigger('change');
                        } else if (news.tags_id) {
                            // If tags_id is not array, convert to array
                            $('#tags_id').val([news.tags_id]).trigger('change');
                        } else {
                            $('#tags_id').val([]).trigger('change');
                        }
                    }, 100);
                } catch (e) {
                        // Fallback: set values after a short delay
                        setTimeout(function() {
                            try {
                                // Handle category fallback
                                if (news.category_id && Array.isArray(news.category_id)) {
                                    $('#category_id').val(news.category_id.length > 0 ? news.category_id[0] : '').trigger('change');
                                } else if (news.category_id) {
                                    $('#category_id').val(news.category_id).trigger('change');
                                } else {
                                    $('#category_id').val('').trigger('change');
                                }

                                // Handle tags fallback
                                if (news.tags_id && Array.isArray(news.tags_id)) {
                                    $('#tags_id').val(news.tags_id).trigger('change');
                                } else if (news.tags_id) {
                                    $('#tags_id').val([news.tags_id]).trigger('change');
                                } else {
                                    $('#tags_id').val([]).trigger('change');
                                }
                            } catch (e2) {
                                // Ignore fallback errors
                            }
                        }, 100);
                    }

                    // Format datetime untuk input datetime-local
                    if (news.published_at) {
                        var publishedDate = new Date(news.published_at);
                        var formattedPublished = publishedDate.getFullYear() + '-' +
                            String(publishedDate.getMonth() + 1).padStart(2, '0') + '-' +
                            String(publishedDate.getDate()).padStart(2, '0') + 'T' +
                            String(publishedDate.getHours()).padStart(2, '0') + ':' +
                            String(publishedDate.getMinutes()).padStart(2, '0');
                        $('#published_at').val(formattedPublished);
                    } else {
                        $('#published_at').val('');
                    }

                    if (news.archived_at) {
                        var archivedDate = new Date(news.archived_at);
                        var formattedArchived = archivedDate.getFullYear() + '-' +
                            String(archivedDate.getMonth() + 1).padStart(2, '0') + '-' +
                            String(archivedDate.getDate()).padStart(2, '0') + 'T' +
                            String(archivedDate.getHours()).padStart(2, '0') + ':' +
                            String(archivedDate.getMinutes()).padStart(2, '0');
                        $('#archived_at').val(formattedArchived);
                    } else {
                        $('#archived_at').val('');
                    }

                    $('#modal-judul').text('Edit Berita');

                    $('#tambahModal').modal('show');
                } else {
                    toastr.error('Data news tidak ditemukan.');
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

    $('#formNews').on('submit', function(e){
        e.preventDefault();

        // Tambahkan loader pada tombol submit
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Menyimpan...').prop('disabled', true);

        $('#formNews .form-control, #formNews .form-select').removeClass('is-invalid');
        $('#formNews .text-danger.small').text('');

        // Trigger TinyMCE to save content to textarea
        if (tinymce.get('content')) {
            tinymce.get('content').save();
        }

        var formData = new FormData(this);
        var id = $('#id').val();
        var url = '';
        var method = '';

        if(id){
            url = '/portfolio/news/update/' + id;
            method = 'POST';
            formData.append('_method', 'PUT');
        } else {
            url = '/portfolio/news/store';
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
                    $('#TableNews').DataTable().ajax.reload();
                    toastr.success('Data berhasil disimpan!');
                } else {
                    toastr.error('Terjadi kesalahan, silakan coba lagi!');
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
                  toastr.error('Gagal menyimpan data!');
              }
              // Kembalikan tombol ke kondisi semula
              submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    $('#tambahModal').on('hidden.bs.modal', function () {
        $('#formNews')[0].reset();
        $('#id').val('');
        $('#modal-judul').text('Tambah Berita');
        $('#formNews .form-control, #formNews .form-select').removeClass('is-invalid');
        $('#formNews .text-danger.small').text('');

        // Clear Select2 safely
        try {
            $('#category_id').val('').trigger('change');
            $('#tags_id').val([]).trigger('change');
        } catch (e) {
            // Ignore errors
        }

        // Clear TinyMCE content
        if (tinymce.get('content')) {
            tinymce.get('content').setContent('');
        }
    });

        // Event listener untuk memastikan Select2 terinisialisasi saat modal dibuka
    $('#tambahModal').on('shown.bs.modal', function () {
        // Re-inisialisasi Select2 saat modal dibuka
        initializeSelect2();
    });

    // Event listener untuk delete
    $(document).on('click', '.delete-record', function () {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data news akan dihapus!",
            icon: 'warning',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light ml-3',
                cancelButton: 'btn btn-label-secondary waves-effect waves-light'
            },
            showCancelButton: true,
            cancelButtonText: 'Batal',
            buttonsStyling: false,
            didRender: function () {
                $('.swal2-actions').css('gap', '10px');
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/portfolio/news/delete/' + id,
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
                            $('#TableNews').DataTable().ajax.reload();
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
                            'Terjadi kesalahan saat menghapus data.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
