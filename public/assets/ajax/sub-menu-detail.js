$(document).ready(function () {
    $('.select2').select2({
        dropdownParent: $('#offcanvasAddMenu')
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

});

$(document).ready(function () {
    $('#TableSubMenuDetails').DataTable({
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
            sLengthMenu: '_MENU_',
            search: '',
            searchPlaceholder: 'Search..'
        },
        buttons: [
            {
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Menu</span>',
                className: 'add-new btn btn-primary waves-effect waves-light mx-4',
                attr: {
                    'data-bs-toggle': 'offcanvas',
                    'data-bs-target': '#offcanvasAddMenu',

                }
            }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: "admin/sub-menu-detail/",
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
                data: 'name',
                name: 'name'
            },
            {
                data: 'route',
                name: 'route',
                render: function (data, type, full, meta) {
                    return '<span class="badge bg-label-success">'+ data +'</span>'
                }
            },
            {
                data: 'order',
                name: 'order'
            },
            {
                data: 'menu_group_id',
                name: 'menu_group_id'
            },
            {
                data: 'menu_detail_id',
                name: 'menu_detail_id'
            },
            {
                data: 'status',
                name: 'status',
                render: function (data, type, full, meta) {
                    if(full.status == 1) {
                        return '<span class="badge bg-label-primary">Active</span>'
                    } else {
                        return '<span class="badge bg-label-danger">Inactive</span>'
                    }
                }
            },
            {
                data: 'aksi',
                name: 'aksi',
                render: function (data, type, full, meta) {
                    let userPermissions = window.userPermissions || [];
                    let canEdit         = userPermissions.includes("edit_menu_detail");
                    let canDelete       = userPermissions.includes("delete_menu_detail");

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

    let selectedId = null;

    // Fungsi untuk menampilkan data ke dalam offcanvas (Edit)
    window.ViewData = function (id) {
        $('#tambahModal').modal('show');

        if (id === 0) {
            // Mode Insert (Tambah Data)
            $('#modal-judul').text('Tambah Item');
            $('#formSubMenuDetails')[0].reset();
            $('#btn-simpan').val('create');
        } else {
            // Mode Edit (Ambil data dari API)
            $('#modal-judul').text('Edit Item');
            $('#btn-simpan').val('update');

            $.ajax({
                url: `admin/sub-menu-detail/edit/${id}/`, // Pastikan route ini benar
                type: "GET",
                success: function (response) {
                    if (response.success) {
                        selectedId = id;
                        $("#nama_menu").val(response.menuDetail.name);
                        $("#menu_group_id").val(response.menuDetail.menu_group_id);
                        $("#icon").val(response.menuDetail.icon);
                        $("#route").val(response.menuDetail.route);
                        $("#order").val(response.menuDetail.order);
                        $("#status").val(response.menuDetail.status);

                        // Ubah tombol submit agar tahu ini update
                        $(".data-submit").text("Update").attr("id", "updateMenu");

                        $("#offcanvasAddMenu").offcanvas("show");
                    }
                },
                error: function () {
                    toastr.error('Gagal mengambil data!');
                }
            });
        }
    };

    // Submit Form: Tambah & Update
    $("#formSubMenuDetails").submit(function (e) {
        e.preventDefault();

        // Tambahkan loader pada tombol submit
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Menyimpan...').prop('disabled', true);

        let formData = new FormData(this);
        let url      = selectedId ? `admin/sub-menu-detail/update/${selectedId}` : "admin/sub-menu-detail/store";

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);

                    $('#TableSubMenuDetails').DataTable().ajax.reload(null, false);
                    $("#formSubMenuDetails")[0].reset();
                    $("#offcanvasAddMenu").offcanvas("hide");

                    // Reset tombol ke mode Tambah
                    $(".data-submit").text("Submit").removeAttr("id");
                    selectedId = null;
                }
                // Kembalikan tombol ke kondisi semula
                submitBtn.html(originalText).prop('disabled', false);
            },
            error: function () {
                toastr.error('Gagal menyimpan data!');
                // Kembalikan tombol ke kondisi semula
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.delete-record', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data menu_details akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'admin/sub-menu-detail/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            Swal.fire(
                                'Deleted!',
                                'Data Pegawai Berhasil Dihapus.',
                                'success'
                            );
                            $('#datatable').DataTable().ajax.reload();
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
