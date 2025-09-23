$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

});

$(document).ready(function () {
    $('#TableJenisProg').DataTable({
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
                            if (item.classList !== undefined && item.classList.contains('pegawai-nm_pegawai')) {
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
                            if (item.classList !== undefined && item.classList.contains('pegawai-nm_pegawai')) {
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
                            if (item.classList !== undefined && item.classList.contains('pegawai-nm_pegawai')) {
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
                            if (item.classList !== undefined && item.classList.contains('pegawai-nm_pegawai')) {
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
                            if (item.classList !== undefined && item.classList.contains('pegawai-nm_pegawai')) {
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
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah jenis program</span>',
                className: 'add-new btn btn-primary waves-effect waves-light',
                attr: {
                    'data-bs-toggle': 'offcanvas',
                    'data-bs-target': '#offcanvasAddJenisProg',

                }
            }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: "/program/jenis-program/",
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
                data: 'nama_jenis_program',
                name: 'nama_jenis_program'
            },
            {
                data: 'deskripsi_jenis_program',
                name: 'deskripsi_jenis_program'
            },
            {
                data: 'gambar_jenis_program',
                name: 'gambar_jenis_program',
                render: function (data, type, full, meta) {
                    if (data) {
                        // Data sudah berupa URL lengkap dari backend
                        return '<img src="' + data + '" alt="Gambar Jenis Program" class="img-fluid" style="width: 30px; height: 30px;" onerror="this.onerror=null; this.src=\'https://via.placeholder.com/50\';" />';
                    } else {
                        return '<span class="badge bg-secondary">No Image</span>';
                    }
                }
            },
            {
                data: 'status',
                name: 'status',
                render: function (data, type, full, meta) {
                    if (data === true || data === "true" || data == 1 || data == "1") {
                        return '<span class="badge bg-label-success">Active</span>';
                    } else {
                        return '<span class="badge bg-label-danger">Inactive</span>';
                    }
                }
            },
            {
                data: 'aksi',
                name: 'aksi',
                render: function (data, type, full, meta) {
                  let userPermissions = window.userPermissions || [];
                  let canEdit         = userPermissions.includes("edit_jenis_program");
                  let canDelete       = userPermissions.includes("delete_jenis_program");

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
            [0, 'desc']
        ],

    });

    // Reset form when add new kategori button is clicked
    $('.card').on('click', '.dt-action-buttons .add-new', function() {
        $('#id').val('');
        $('#formJenisProg')[0].reset();
        $('#offcanvasAddJenisProgLabel').text('Tambah Jenis Program');
        $('.data-submit').text('Submit');
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.text-danger').text('');
    });

    let selectedId = null;

    window.ViewData = function (id) {
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.text-danger').text('');

        $.ajax({
            url: `/program/jenis-program/edit/${id}/`,
            type: "GET",
            success: function (response) {
                if (response.success) {
                    selectedId = id;
                    $("#id").val(response.jenis_program.id);
                    $("#nama_jenis_program").val(response.jenis_program.nama_jenis_program);
                    $("#deskripsi_jenis_program").val(response.jenis_program.deskripsi_jenis_program);
                    $("#gambar_jenis_program").val(response.jenis_program.gambar_jenis_program);
                    $('#offcanvasAddJenisProgLabel').text('Edit Jenis Program');
                    // Ubah tombol submit agar tahu ini update
                    $(".data-submit").text("Update").attr("id", "updateMenu");

                    $("#offcanvasAddJenisProg").offcanvas("show");
                }
            },
            error: function () {
                toastr.error('Gagal mengambil data!');
            }
        });
    }

    // Submit Form: Tambah & Update
    $("#formJenisProg").submit(function (e) {
        e.preventDefault();

        // Tambahkan loader pada tombol submit
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Menyimpan...').prop('disabled', true);

        $('.form-control, .form-select').removeClass('is-invalid');
        $('.text-danger').text('');

        let formData = new FormData(this);
        let id       = $("#id").val();
        let url      = "/program/jenis-program/store";
        let method   = "POST";

        if (id) {
            url = "/program/jenis-program/update/" + id;
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

                    $('#TableJenisProg').DataTable().ajax.reload(null, false);
                    $("#formJenisProg")[0].reset();
                    $("#id").val(""); // Reset ID agar tidak salah update nanti

                    $("#offcanvasAddJenisProg").offcanvas("hide");

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

    $(document).on('click', '.delete-record', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data jenis program akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/program/jenis-program/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            Swal.fire(
                                'Deleted!',
                                'Data Jenis Program Berhasil Dihapus.',
                                'success'
                            );
                            $('#TableJenisProg').DataTable().ajax.reload(null, false);
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
