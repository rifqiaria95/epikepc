$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

});

if ($.fn.DataTable.isDataTable('#TablePelanggan')) {
    $('#TablePelanggan').DataTable().destroy();
}

$('#TablePelanggan').DataTable({
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
                      if (item.classList !== undefined && item.classList.contains('pelanggan-nm_pelanggan')) {
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
                      if (item.classList !== undefined && item.classList.contains('pelanggan-nm_pelanggan')) {
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
                      if (item.classList !== undefined && item.classList.contains('pelanggan-nm_pelanggan')) {
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
                      if (item.classList !== undefined && item.classList.contains('pelanggan-nm_pelanggan')) {
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
          text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Pelanggan</span>',
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
        url: "/pelanggan/",
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
            data: 'nm_pelanggan',
            name: 'nm_pelanggan'
        },
        {
            data: 'alamat_pelanggan',
            name: 'alamat_pelanggan'
        },
        {
            data: 'plat_nomor',
            name: 'plat_nomor',
            render: function (data, type, full, meta) {
                return '<span class="badge bg-label-primary">'+ data +'</span>'
            }
        },
        {
            data: 'no_hp_pelanggan',
            name: 'no_hp_pelanggan'
        },
        {
            data: 'deskripsi',
            name: 'deskripsi'
        },
        {
            data: 'status',
            name: 'status',
            render: function (data, type, full, meta) {
                var $inactive = '<span class="badge bg-label-danger">Inactive</span>';
                var $aktif = '<span class="badge bg-label-success">Active</span>';
                if (data == 0) {
                    return $inactive;
                } else if (data == 1) {
                    return $aktif;
                }
                return '';
            }
        },
        {
            data: 'aksi',
            name: 'aksi',
            render: function (data, type, full, meta) {
                let userPermissions = window.userPermissions || [];
                let canEdit         = userPermissions.includes("edit pelanggan");
                let canDelete       = userPermissions.includes("delete pelanggan");

                let buttons = '<div class="d-flex align-items-center">';

                buttons += '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>';
                    buttons += '<div class="dropdown-menu dropdown-menu-end m-0">';
                    if (canEdit) {
                      buttons += '<a href="javascript:;" class="dropdown-item" onclick="ViewData(' + full.id + ')"><i class="ti ti-edit ti-md"></i>Edit</a>';
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

$('#pelanggan-provinsi').on('change', function() {
    var id_provinsi = $(this).val();

    $('#pelanggan-kota').empty().append('<option selected disabled>Loading...</option>');

    $.ajax({
        url: "/pelanggan/get-kota/" + id_provinsi,
        type: "GET",
        xhrFields: { withCredentials: true },
        success: function (response) {

            $('#pelanggan-kota').empty().append('<option selected disabled>Pilih Kota</option>');

            response.forEach(function (data) { // Gunakan response langsung
                $('#pelanggan-kota').append('<option value="' + data.id_kota + '">' + data.name + '</option>');
            });
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});

$('#formPelanggan').on('submit', function(e){
    e.preventDefault();

    let formData = new FormData(this);
    let id = $('#id').val();
    let url = '';

    if(id == "" || id == 0){
        url = "/pelanggan/store";
    } else {
        url = "/pelanggan/update/" + id;
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
                $('#formPelanggan')[0].reset();
                $('#TablePelanggan').DataTable().ajax.reload(null, false);
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
        }
    });
});

// Function untuk mengisi modal saat update
window.ViewData = function (id) {
    $('#tambahModal').modal('show');

    if (id === 0) {
        // Mode Insert (Tambah Data)
        $('#modal-judul').text('Tambah Pegawai');
        $('#formPelanggan')[0].reset();
        $('#btn-simpan').val('create');
    } else {
        // Mode Edit (Ambil data dari API)
        $('#modal-judul').text('Edit Pegawai');
        $('#btn-simpan').val('update');

        $.ajax({
            url: '/pelanggan/edit/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response) {
                    $('#id').val(response.id);
                    $('#nm_pelanggan').val(response.nm_pelanggan);
                    $('#alamat_pelanggan').val(response.alamat_pelanggan);
                    $('#no_hp_pelanggan').val(response.no_hp_pelanggan);
                    $('#plat_nomor').val(response.plat_nomor);
                    $('#deskripsi').val(response.deskripsi);
                    $('#id_kendaraan').val(response.id_kendaraan);
                    $('#active').val(response.active);
                    $('#pelanggan-provinsi').val(response.id_provinsi).trigger('change');
                    $.ajax({
                        url: "/pelanggan/get-kota/" + response.id_provinsi,
                        type: "GET",
                        success: function(kotaResponse) {
                            $('#pelanggan-kota').empty().append('<option selected disabled>Pilih Kota</option>');

                            kotaResponse.forEach(function (data) {
                                $('#pelanggan-kota').append('<option value="' + data.id_kota + '">' + data.name + '</option>');
                            });

                            $('#pelanggan-kota').val(response.id_kota).trigger('change');
                        }
                    });
                    $('#status').val(response.status);
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
                url: '/pelanggan/delete/' + id,
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
                        $('#TablePelanggan').DataTable().ajax.reload();
                    }
                },
                error: function () {
                    Swal.fire('Oops!', 'Terjadi kesalahan saat menghapus data.', 'error');
                }
            });
        }
    });
});

