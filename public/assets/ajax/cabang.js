$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

});

$(document).ready(function () {
    $('#TableCabang').DataTable({
        dom:
            '<"row me-2"' +
            '<"col-md-2"<"me-3"l>>' +
            '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-3"fB>>' +
            '>t' +
            '<"row mx-2"' +
            '<"col-sm-12 col-md-6"i>' +
            '<"col-sm-12 col-md-6"p>' +
            '>',
        language: {
            sLengthCabang: '_MENU_',
            search: '',
            searchPlaceholder: 'Search..'
        },
        buttons: [
            {
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Cabang</span>',
                className: 'add-new btn btn-primary waves-effect waves-light mx-4',
                attr: {
                    'data-bs-toggle': 'offcanvas',
                    'data-bs-target': '#offcanvasAddCabang',

                }
            }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: "/company/cabang/",
            type: 'GET'
        },
        columns: [
            {
                data: null,
                name: 'id',
                render: function (data, type, full, meta) {
                    return meta.row + 1;
                }
            },
            {
                data: 'nama_cabang',
                name: 'nama_cabang'
            },
            {
                data: 'perusahaan',
                name: 'perusahaan'
            },
            {
                data: 'alamat_cabang',
                name: 'alamat_cabang'
            },
            {
                data: 'no_telp_cabang',
                name: 'no_telp_cabang'
            },
            {
                data: 'email_cabang',
                name: 'email_cabang'
            },
            {
                data: 'aksi',
                name: 'aksi',
                render: function (data, type, full, meta) {
                  let userPermissions = window.userPermissions || [];
                  let canEdit         = userPermissions.includes("edit_cabang");
                  let canDelete       = userPermissions.includes("delete_cabang");

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

    // Reset form when add new cabang button is clicked
    $('.card').on('click', '.dt-action-buttons .add-new', function() {
        $('#id').val('');
        $('#formCabang')[0].reset();
        $('#id_perusahaan').val(null).trigger('change');
        $('#offcanvasAddCabangLabel').text('Tambah Cabang');
        $('.data-submit').text('Submit');
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.text-danger').text('');
    });

    let selectedId = null;

    // Fungsi untuk menampilkan data ke dalam offcanvas (Edit)
    window.ViewData = function (id) {
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.text-danger').text('');
        // Mode Edit (Ambil data dari API)
        $.ajax({
            url: `company/cabang/edit/${id}/`, // Pastikan route ini benar
            type: "GET",
            success: function (response) {
                if (response.success) {
                    selectedId = id;
                    $("#id").val(id);
                    $("#nama_cabang").val(response.cabang.nama_cabang);
                    $("#id_perusahaan").val(response.cabang.id_perusahaan).trigger('change');
                    $("#alamat_cabang").val(response.cabang.alamat_cabang);
                    $("#no_telp_cabang").val(response.cabang.no_telp_cabang);
                    $("#email_cabang").val(response.cabang.email_cabang);
                    $('#offcanvasAddCabangLabel').text('Edit Cabang');

                    // Ubah tombol submit agar tahu ini update
                    $(".data-submit").text("Update");

                    $("#offcanvasAddCabang").offcanvas("show");
                }
            },
            error: function () {
                toastr.error('Gagal mengambil data!');
            }
        });
    };

    // Submit Form: Tambah & Update
    $("#formCabang").submit(function (e) {
        e.preventDefault();

        // Tambahkan loader pada tombol submit
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Menyimpan...').prop('disabled', true);

        $('.form-control, .form-select').removeClass('is-invalid');
        $('.text-danger').text('');

        let formData = new FormData(this);
        let id       = $("#id").val();
        let url      = "/company/cabang/store";
        let method   = "POST";

        if (id) {
            url = "/company/cabang/update/" + id;
            formData.append("_method", "PUT");
        }

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);

                    $('#TableCabang').DataTable().ajax.reload(null, false);
                    $("#formCabang")[0].reset();
                    $("#id").val("");
                    $("#offcanvasAddCabang").offcanvas("hide");
                    $('#id_perusahaan').val(null).trigger('change');

                    // Reset tombol ke mode Tambah
                    $(".data-submit").text("Submit").removeAttr("id");
                    selectedId = null;
                }
                // Kembalikan tombol ke kondisi semula
                submitBtn.html(originalText).prop('disabled', false);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
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

    // Filter by perusahaan
    $('#filter_perusahaan').on('change', function() {
        let perusahaanId = $(this).val();
        let table = $('#TableCabang').DataTable();

        if (perusahaanId) {
            // Filter berdasarkan perusahaan
            table.column(2).search(perusahaanId).draw();
        } else {
            // Tampilkan semua data
            table.column(2).search('').draw();
        }
    });

    $(document).on('click', '.delete-record', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data cabang akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/company/cabang/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            Swal.fire(
                                'Deleted!',
                                'Data Cabang Berhasil Dihapus.',
                                'success'
                            );
                            $('#TableCabang').DataTable().ajax.reload(null, false);
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
