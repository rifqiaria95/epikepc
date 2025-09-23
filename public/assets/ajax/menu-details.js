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
    $('#TableMDetails').DataTable({
        dom:
            '<"row me-2"' +
            '<"col-md-2"<"me-3"l>>' +
            // Tambahkan flex dan gap agar search dan button terpisah rapi
            '<"col-md-10"' +
                '<"dt-action-buttons d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-3 gap-2"' +
                    // Search box
                    'f' +
                    // Button Hapus Terpilih
                    '<"ms-md-3 me-2" B>' +
                '>' +
            '>' +
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
                text: '<i class="ti ti-trash me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Hapus Terpilih</span>',
                className: 'btn btn-danger waves-effect waves-light delete-selected me-3',
                action: function () {
                    deleteSelectedRecords();
                }
            },
            {
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Menu</span>',
                className: 'add-new btn btn-primary waves-effect waves-light ms-2',
                attr: {
                    'data-bs-toggle': 'offcanvas',
                    'data-bs-target': '#offcanvasAddMenu',
                }
            }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: "admin/menu-detail/",
            type: 'GET'
        },
        columns: [
            {
                data: null,
                name: 'checkbox',
                orderable: false,
                searchable: false,
                render: function (data, type, full, meta) {
                    return '<input type="checkbox" class="form-check-input dt-checkboxes" value="' + full.id + '">';
                }
            },
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
            [1, 'asc']
        ],

    });

    // Event handler untuk select all checkbox
    $('#TableMDetails').on('change', '#select-all', function() {
        let isChecked = $(this).prop('checked');
        $('#TableMDetails tbody input[type="checkbox"]').prop('checked', isChecked);
        toggleDeleteButton();
    });

    // Event handler untuk checkbox individual
    $('#TableMDetails').on('change', 'tbody input[type="checkbox"]', function() {
        toggleDeleteButton();
    });

    // Fungsi untuk menampilkan/menyembunyikan tombol delete batch
    function toggleDeleteButton() {
        let checkedCount = $('#TableMDetails tbody input[type="checkbox"]:checked').length;
        let deleteButton = $('.delete-selected');
        
        if (checkedCount > 0) {
            deleteButton.addClass('show');
            deleteButton.find('span').text(`Hapus Terpilih (${checkedCount})`);
        } else {
            deleteButton.removeClass('show');
            deleteButton.find('span').text('Hapus Terpilih');
        }
    }

    $('.select2').select2({
        dropdownParent: $('#offcanvasAddMenu')
    });

    let selectedId = null;

    // Fungsi untuk menampilkan data ke dalam offcanvas (Edit)
    window.ViewData = function (id) {
        $('#offcanvasAddMenu').offcanvas('show');

        if (id === 0) {
            // Mode Insert (Tambah Data)
            $('#modal-judul').text('Tambah Item');
            $('#formMenuDetails')[0].reset();
            $('#btn-simpan').val('create');
        } else {
            // Mode Edit (Ambil data dari API)
            $('#modal-judul').text('Edit Item');
            $('#btn-simpan').val('update');

            $.ajax({
                url: `admin/menu-detail/edit/${id}/`, // Pastikan route ini benar
                type: "GET",
                success: function (response) {
                    if (response.success) {
                        selectedId = id;
                        $("#nama_menu").val(response.menuDetail.name);
                        $("#menu_group_id").val(response.menuDetail.menu_group_id).trigger('change');
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
    $("#formMenuDetails").submit(function (e) {
        e.preventDefault();

        // Tambahkan loader pada tombol submit
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Menyimpan...').prop('disabled', true);

        let formData = new FormData(this);
        let url      = selectedId ? `admin/menu-detail/update/${selectedId}` : "admin/menu-detail/store";

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);

                    $('#TableMDetails').DataTable().ajax.reload(null, false);
                    $("#formMenuDetails")[0].reset();
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
                    url: 'admin/menu-detail/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            toastr.success(response.message);
                            $('#TableMDetails').DataTable().ajax.reload(null, false);
                        } else {
                            toastr.error(response.errors);
                        }
                    },
                    error: function () {
                        toastr.error('Terjadi kesalahan saat menghapus data.');
                    }
                });
            }
        });
    });

    // Fungsi untuk menghapus records yang dipilih
    function deleteSelectedRecords() {
        let table = $('#TableMDetails').DataTable();
        let selectedIds = [];
        
        // Ambil semua checkbox yang dicentang
        $('#TableMDetails tbody input[type="checkbox"]:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Silakan pilih data yang akan dihapus terlebih dahulu.',
                customClass: {
                    confirmButton: 'btn btn-warning waves-effect waves-light'
                }
            });
            return;
        }

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Anda akan menghapus ${selectedIds.length} data menu detail yang dipilih!`,
            icon: 'warning',
            customClass: {
                confirmButton: 'btn btn-danger waves-effect waves-light ml-3',
                cancelButton: 'btn btn-label-secondary waves-effect waves-light'
            },
            showCancelButton: true,
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, Hapus!',
            buttonsStyling: false,
            didRender: function () {
                $('.swal2-actions').css('gap', '10px');
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'admin/menu-detail/delete-batch',
                    type: 'DELETE',
                    data: {
                        ids: selectedIds,
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            toastr.success(response.message);
                            
                            // Reload table dan reset selection
                            table.ajax.reload(null, false);
                            $('#select-all').prop('checked', false);
                            toggleDeleteButton();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menghapus data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                });
            }
        });
    }

    // Event handler untuk tombol delete selected (jika tidak menggunakan DataTable buttons)
    $(document).on('click', '.delete-selected', function() {
        deleteSelectedRecords();
    });
});
