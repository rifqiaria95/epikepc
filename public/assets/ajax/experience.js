$(document).ready(function () {
    $('.select2').select2({
        dropdownParent: $('#tambahModal')
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

});

$(document).ready(function () {
    $('#TableExperience').DataTable({
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
              text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Pengalaman</span>',
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
            url: "portfolio/experience/",
            type: 'GET'
        },
        columns: [{
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
              data: 'image',
              name: 'image',
              render: function (data, type, full, meta) {
                  if (data) {
                    // Data sudah berupa URL lengkap dari backend
                    return '<img src="' + data + '" alt="Image" class="img-fluid" style="width: 30px; height: 30px;" onerror="this.onerror=null; this.src=\'https://via.placeholder.com/50\';" />';
                  } else {
                    return '<img src="https://via.placeholder.com/50" alt="Image" class="img-fluid" style="width: 30px; height: 30px;">';
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
              data: 'company',
              name: 'company'
            },
            {
              data: 'description',
              name: 'description',
                render: function (data, type, full, meta) {
                    // Tampilkan maksimal 3 kata saja, lalu tambahkan "..."
                    if (!data) return '';
                    // Hilangkan tag HTML jika ada
                    let text = $('<div>').html(data).text();
                    let words = text.trim().split(/\s+/);
                    if (words.length > 3) {
                        return words.slice(0, 3).join(' ') + ' ...';
                    } else {
                        return text;
                    }
                }
            },
            {
              data: 'year',
              name: 'year'
            },
            {
              data: 'created_by',
              name: 'created_by'
            },
            {
                data: 'aksi',
                name: 'aksi',
                render: function (data, type, full, meta) {
                  let userPermissions = window.userPermissions || [];
                  let canEdit         = userPermissions.includes("edit_experience");
                  let canDelete       = userPermissions.includes("delete_experience");

                  let buttons = '<div class="d-flex align-items-center">';

                  buttons += '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>';
                    buttons += '<div class="dropdown-menu dropdown-menu-end m-0">';
                    if (canEdit) {
                      buttons += '<a href="javascript:;" class="dropdown-item" onclick="editExperience(' + full.id + ')"><i class="ti ti-edit ti-md"></i>Edit</a>';
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

    $('.select2').select2({
        dropdownParent: $('#tambahModal')
    });

    $('#formExperience').on('submit', function(e){
        e.preventDefault();

        // Show loader on button
        let submitBtn = $('#btn-simpan');
        let originalText = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Menyimpan...');
        submitBtn.prop('disabled', true);

        // Clear previous errors
        $('#formExperience .form-control, #formExperience .form-select').removeClass('is-invalid');
        $('#formExperience .text-danger.small').text('');
        setTinyMCEError(false);

        // Trigger TinyMCE to save content to textarea
        if (tinymce.get('description')) {
            tinymce.get('description').save();
        }

        let formData = new FormData(this);
        let id = $('#id').val();
        let url = '';
        let method = '';

        if(id){
            url = '/portfolio/experience/update/' + id;
            method = 'POST';
            formData.append('_method', 'PUT');
        } else {
            url = '/portfolio/experience/store';
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
                    $('#TableExperience').DataTable().ajax.reload();
                    toastr.success('Data berhasil disimpan!');
                } else {
                    toastr.error('Terjadi kesalahan, silakan coba lagi!');
                }
            },
            error: function (xhr) {
              if (xhr.status === 422) {
                  let errors = xhr.responseJSON.errors;
                  // Clear previous errors
                  $('#formExperience .form-control, #formExperience .form-select').removeClass('is-invalid');
                  $('#formExperience .text-danger.small').text('');
                  setTinyMCEError(false);

                  $.each(errors, function (key, value) {
                      $('#' + key).addClass('is-invalid');
                      $('#' + key + '-error').text(value[0]);

                      // Handle TinyMCE error styling
                      if (key === 'description') {
                          setTinyMCEError(true);
                      }
                  });
              } else {
                  toastr.error('Gagal menyimpan data!');
              }
            },
            complete: function() {
                // Hide loader and restore button
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });

    $('#tambahModal').on('hidden.bs.modal', function () {
        $('#formExperience')[0].reset();
        $('#id').val('');
        $('#modal-judul').text('Tambah Pengalaman');

        // Clear errors
        $('#formExperience .form-control, #formExperience .form-select').removeClass('is-invalid');
        $('#formExperience .text-danger.small').text('');
        setTinyMCEError(false);

        // Clear TinyMCE content
        if (tinymce.get('description')) {
            tinymce.get('description').setContent('');
        }
    });
});

function editExperience(id) {
    // Clear previous errors
    $('#formExperience .form-control, #formExperience .form-select').removeClass('is-invalid');
    $('#formExperience .text-danger.small').text('');
    setTinyMCEError(false);

    $.ajax({
        url: '/portfolio/experience/edit/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                let experience = response.experience;
                $('#id').val(experience.id);
                $('#title').val(experience.title);
                $('#subtitle').val(experience.subtitle);

                // Set content to TinyMCE editor
                if (tinymce.get('description')) {
                    tinymce.get('description').setContent(experience.description || '');
                } else {
                    $('#description').val(experience.description);
                }

                $('#company').val(experience.company);
                $('#year').val(experience.year);


                $('#modal-judul').text('Edit Pengalaman');
                $('#tambahModal').modal('show');
            } else {
                toastr.error('Data experience tidak ditemukan.');
            }
        },
        error: function() {
            toastr.error('Terjadi kesalahan saat mengambil data.');
        }
    });
}

$(document).on('click', '.delete-record', function () {
    let id = $(this).data('id');

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data experience akan dihapus!",
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
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/portfolio/experience/delete/' + id,
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
                        $('#TableExperience').DataTable().ajax.reload();
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
