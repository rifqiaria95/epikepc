$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

});

$(document).ready(function () {
    $('#TableMGroup').DataTable({
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
                extend: 'collection',
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
            url: "admin/menu-group/",
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
                data: 'jenis_menu',
                name: 'jenis_menu',
                render: function (data, type, full, meta) {
                    if(full.jenis_menu == 1) {
                        return '<span class="badge bg-label-primary">Master</span>'
                    } else if (full.jenis_menu == 2) {
                        return '<span class="badge bg-label-secondary">Transaksi</span>'
                    } else {
                        return '<span class="badge bg-label-info">Settings</span>'
                    }
                }
            },
            {
                data: 'icon',
                name: 'icon'
            },
            {
                data: 'order',
                name: 'order'
            },
            {
                data: 'aksi',
                name: 'aksi',
                render: function (data, type, full, meta) {
                  let userPermissions = window.userPermissions || [];
                  let canEdit         = userPermissions.includes("edit_menu_group");
                  let canDelete       = userPermissions.includes("delete_menu_group");

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

    // Fungsi untuk menampilkan data ke dalam offcanvas (Edit)
    window.ViewData = function (id) {
        $.ajax({
            url: `admin/menu-group/edit/${id}/`, // Pastikan route ini benar
            type: "GET",
            success: function (response) {
                if (response.success) {
                    selectedId = id;
                    $("#id").val(response.menu.id);
                    $("#nama_menu").val(response.menu.name);
                    $("#jenis_menu").val(response.menu.jenis_menu);
                    $("#icon").val(response.menu.icon);
                    $("#order").val(response.menu.order);

                    // Ubah tombol submit agar tahu ini update
                    $(".data-submit").text("Update").attr("id", "updateMenu");

                    $("#offcanvasAddMenu").offcanvas("show");
                }
            },
            error: function () {
                toastr.error('Gagal mengambil data!');
            }
        });
    };

    // Submit Form: Tambah & Update
    $("#formMenu").submit(function (e) {
        e.preventDefault();

        // Tambahkan loader pada tombol submit
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Menyimpan...').prop('disabled', true);

        let formData = new FormData(this);
        let id = $("#id").val();
        let url = "admin/menu-group/store";
        let method = "POST";

        if (id) {
            url = "admin/menu-group/update/" + id;
            formData.append("_method", "PUT"); // Tambahkan method PUT untuk Laravel
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

                    $('#TableMGroup').DataTable().ajax.reload(null, false);
                    $("#formMenu")[0].reset();
                    $("#id").val(""); // Reset ID agar tidak salah update nanti

                    let offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById("offcanvasAddMenu"));
                    if (offcanvas) offcanvas.hide();

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
            text: "Data menu akan dihapus!",
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
                    url: 'admin/menu-group/delete/' + id,
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
                            $('#TableMGroup').DataTable().ajax.reload();
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
