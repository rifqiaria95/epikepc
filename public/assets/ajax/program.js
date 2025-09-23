$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

});

if ($.fn.DataTable.isDataTable('#TableProgram')) {
    $('#TableProgram').DataTable().destroy();
}

$('#TableProgram').DataTable({
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
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('pelanggan-nm_pelanggan')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
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
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('program-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
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
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('program-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
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
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('program-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
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
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('program-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            }
          ]
        },
        {
          text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Program</span>',
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
        url: "/program/daftar-program",
        type: 'GET'
    },
    columns: [{
            data: null,
            name: 'id',
            render: function (data, type, full, meta) {
                return meta.row + 1;
            }
        },
        {
            data: 'jenis_program',
            name: 'jenis_program'
        },
        {
            data: 'name',
            name: 'name'
        },
        {
            data: 'description',
            name: 'description',
        },
        {
            data: 'start_date',
            name: 'start_date'
        },
        {
            data: 'end_date',
            name: 'end_date'
        },
        {
            data: 'kuota',
            name: 'kuota'
        },
        {
            data: 'kategori',
            name: 'kategori'
        },
        {
            data: 'sumber_dana',
            name: 'sumber_dana'
        },
        {
            data: 'status',
            name: 'status',
            render: function (data, type, full, meta) {
                return data == 'draft' ? '<span class="badge bg-label-warning">Draft</span>' : data == 'open' ? '<span class="badge bg-label-success">Open</span>' : '<span class="badge bg-label-danger">Closed</span>';
            }
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
                let canEdit         = userPermissions.includes("edit_daftar_program");
                let canDelete       = userPermissions.includes("delete_daftar_program");

                let buttons = '<div class="d-flex align-items-center">';

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

$('.select2').select2({
    dropdownParent: $('#tambahModal')
});

$('#formProgram').on('submit', function(e){
    e.preventDefault();

    // Show loader on button
    let submitBtn = $('#btn-simpan');
    let originalText = submitBtn.html();
    submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Menyimpan...');
    submitBtn.prop('disabled', true);

    let formData = new FormData(this);
    let id = $('#id').val();
    let url = '';

    if(id == "" || id == 0){
        url = "/program/daftar-program/store";
    } else {
        url = "/program/daftar-program/update/" + id;
        formData.append('_method', 'PUT');
    }

    // Bersihkan error sebelumnya
    $('.text-danger').remove();
    $('.is-invalid').removeClass('is-invalid');

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response){
            if(response.status == 200){
                toastr.success('Data berhasil disimpan!');
                $('#formProgram')[0].reset();
                $('#TableProgram').DataTable().ajax.reload(null, false);
                let modal = bootstrap.Modal.getInstance(document.getElementById('tambahModal'));
                modal.hide();
            } else {
                toastr.error('Terjadi kesalahan, silakan coba lagi!');
            }
        },
        error: function(xhr){
            if(xhr.status === 400) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = "Harap periksa kembali inputan Anda!";

                $.each(errors, function(key, value){
                    let inputField = $('[name="' + key + '"]');
                    inputField.addClass('is-invalid');
                    inputField.after('<span class="text-danger">' + value[0] + '</span>');
                    toastr.error(value[0]);
                });

            } else {
                toastr.error('Terjadi kesalahan, silakan coba lagi!');
            }
        },
        complete: function() {
            // Hide loader and restore button
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        }
    });
});

// Function untuk mengisi modal saat update
window.ViewData = function (id) {
    $('#tambahModal').modal('show');

    if (id === 0) {
        // Mode Insert (Tambah Data)
        $('#modal-judul').text('Tambah Pegawai');
        $('#formProgram')[0].reset();
        $('#btn-simpan').val('create');
    } else {
        // Mode Edit (Ambil data dari API)
        $('#modal-judul').text('Edit Pegawai');
        $('#btn-simpan').val('update');

        $.ajax({
            url: '/program/daftar-program/edit/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response) {
                    $('#id').val(response.id);
                    $('#name').val(response.name);
                    $('#description').val(response.description);
                    $('#start_date').val(response.start_date);
                    $('#end_date').val(response.end_date);
                    $('#kuota').val(response.kuota);
                    $('#kategori').val(response.kategori);
                    $('#id_kendaraan').val(response.id_kendaraan);
                    $('#active').val(response.active);
                    $('#jenis_program').val(response.jenis_program_id).trigger('change');
                    $.ajax({
                        url: "/program/get-jenis-program/" + response.jenis_program_id,
                        type: "GET",
                        success: function(kotaResponse) {
                            $('#jenis_program').empty().append('<option selected disabled>Pilih Jenis Program</option>');

                            kotaResponse.forEach(function (data) {
                                $('#jenis_program').append('<option value="' + data.id + '">' + data.nama_jenis_program + '</option>');
                            });

                            $('#jenis_program').val(response.jenis_program_id).trigger('change');
                        }
                    });
                    $('#status').val(response.status);
                    $('#sumber_dana').val(response.sumber_dana);
                }
            },
            error: function() {
                alert('Gagal mengambil data!');
            }
        });
    }

}

$(document).on('click', '.delete-record', function () {
    console.log("Delete diklik!");

    let id = $(this).data('id');

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data role akan dihapus!",
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
                url: '/program/daftar-program/delete/' + id,
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
                        $('#TableProgram').DataTable().ajax.reload();
                    }
                },
                error: function () {
                    Swal.fire('Oops!', 'Terjadi kesalahan saat menghapus data.', 'error');
                }
            });
        }
    });
});

