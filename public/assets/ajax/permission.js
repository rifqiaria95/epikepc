$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#TablePermission').DataTable({
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
                text: '<i class="ti ti-trash me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Hapus Terpilih</span>',
                className: 'btn btn-danger waves-effect waves-light delete-selected me-3',
                action: function () {
                    deleteSelectedRecords();
                }
            },
            {
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Permission</span>',
                className: 'add-new btn btn-primary waves-effect waves-light',
                action: function () {
                    ViewData(0);
                }
            }
        ],
        processing: true,
        serverSide: true, //aktifkan server-side
        ajax: {
            url: "admin/permission/",
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
            { data: 'name', name: 'name' },
            {
                data: 'menu_groups',
                name: 'menu_groups',
                render: function (data) {
                    if (!Array.isArray(data) || data.length === 0) return '-';
                    return data.join(', '); // Gabungkan array menjadi teks
                }
            },
            {
                data: 'menu_details',
                name: 'menu_details',
                render: function (data) {
                    if (!Array.isArray(data) || data.length === 0) return '-';
                    return data.join(', '); // Gabungkan array menjadi teks
                }
            },
            {
                data: 'assigned_to',
                name: 'assigned_to',
                render: function (data) {
                    if (!Array.isArray(data) || data.length === 0) {
                        return '<span class="badge bg-label-dark">No roles assigned</span>';
                    }
                    return data.map(function(role) {
                        let badgeClass = 'badge bg-label-dark';
                        if (role === 'superadmin') {
                            badgeClass = 'badge bg-label-success';
                        } else if (role === 'admin') {
                            badgeClass = 'badge bg-label-primary';
                        } else if (role === 'user') {
                            badgeClass = 'badge bg-label-info';
                        }
                        return `<span class="badge ${badgeClass}">${role}</span>`;
                    }).join(' ');
                }
            },
            {
                data: 'aksi',
                name: 'aksi',
                render: function (data, type, full, meta) {
                  let userPermissions = window.userPermissions || [];
                  let canEdit         = userPermissions.includes("edit_permission");
                  let canDelete       = userPermissions.includes("delete_permission");

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
            [1, 'desc']
        ],
        orderClasses: false
    });



    // Event handler untuk select all checkbox
    $('#TablePermission').on('change', '#select-all', function() {
        let isChecked = $(this).prop('checked');
        $('#TablePermission tbody input[type="checkbox"]').prop('checked', isChecked);
        toggleDeleteButton();
    });

    // Event handler untuk checkbox individual
    $('#TablePermission').on('change', 'tbody input[type="checkbox"]', function() {
        toggleDeleteButton();
    });

    // Fungsi untuk menampilkan/menyembunyikan tombol delete batch
    function toggleDeleteButton() {
        let checkedCount = $('#TablePermission tbody input[type="checkbox"]:checked').length;
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
        dropdownParent: $('#tambahModal')
    });

    // Event listener untuk menu_groups yang akan mengupdate menu_details
    $('#menu_groups').on('change', function() {
        let menuGroupId = $(this).val();
        let menuDetailsSelect = $('#menu_details');
        
        // Reset menu_details select
        menuDetailsSelect.empty().append('<option selected disabled>Pilih Menu Details</option>');
        
        if (menuGroupId && menuGroupId !== '') {
            // Show loading state
            menuDetailsSelect.prop('disabled', true);
            
            // Ambil menu details berdasarkan menu group yang dipilih
            $.ajax({
                url: 'admin/permission/get-menu-details-by-group',
                type: 'GET',
                data: {
                    menu_group_id: menuGroupId
                },
                success: function(response) {
                    if (response && response.length > 0) {
                        $.each(response, function(index, item) {
                            menuDetailsSelect.append(`<option value="${item.id}">${item.name}</option>`);
                        });
                    } else {
                        menuDetailsSelect.append('<option disabled>Tidak ada menu details untuk group ini</option>');
                    }
                    menuDetailsSelect.prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching menu details:', error);
                    menuDetailsSelect.append('<option disabled>Error loading menu details</option>');
                    menuDetailsSelect.prop('disabled', false);
                    toastr.error('Gagal mengambil data menu details!');
                }
            });
        } else {
            menuDetailsSelect.prop('disabled', false);
        }
    });

    window.ViewData = function (id) {
        $('#tambahModal').modal('show');

        if (id === 0) {
            // Mode Tambah Data
            $("#modal-judul").text("Tambah Permission");
            $("#formPermission")[0].reset();
            $("#id").val("");
            $("#btn-simpan").val("create");

            // Reset Select2
            $("#menu_groups").val("").trigger("change");
            $("#menu_details").val("").trigger("change");
            
            // Reset menu_details options
            $("#menu_details").empty().append('<option selected disabled>Pilih Menu Details</option>');

        } else {
            // Mode Edit (Ambil data dari API)
            $('#modal-judul').text('Edit Permission');
            $('#btn-simpan').val('update');

            $.ajax({
                url: `admin/permission/edit/${id}/`,
                type: "GET",
                success: function (response) {
                    console.log(response);
                    if (response.success) {
                        $("#id").val(response.permission.id);
                        $("#name").val(response.permission.name);
                        
                        // Set menu_groups terlebih dahulu
                        $("#menu_groups").val(response.permission.menu_groups).trigger("change");
                        
                        // Tunggu sebentar agar event change menu_groups selesai, lalu set menu_details
                        setTimeout(function() {
                            if (response.permission.menu_details && response.permission.menu_details.length > 0) {
                                $("#menu_details").val(response.permission.menu_details).trigger("change");
                            }
                        }, 1000); // Increase timeout to ensure menu details are loaded
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching permission data:', error);
                    toastr.error('Gagal mengambil data!');
                }
            });
        }
    }

    // Submit Form: Tambah & Update
    $("#formPermission").on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        let url    = ($("#id").val()) ? `admin/permission/update/${$("#id").val()}` : "admin/permission/store";
        let method = ($("#id").val()) ? "POST" : "POST";

        if ($("#id").val()) {
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
                    $("#tambahModal").modal("hide");
                    $("#formPermission")[0].reset();
                    $("#id").val("");

                    $("#menu_groups").prop("selectedIndex", 0).trigger("change");
                    $("#menu_details").prop("selectedIndex", 0).trigger("change");
                    
                    // Reset menu_details options
                    $("#menu_details").empty().append('<option selected disabled>Pilih Menu Details</option>');

                    toastr.success(response.message);
                    $("#TablePermission").DataTable().ajax.reload(null, false);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessages = "";
                $.each(errors, function (key, value) {
                    errorMessages += value + "<br>";
                });
                toastr.error(errorMessages);
            }
        });
    });

    $(document).on('click', '.delete-record', function () {
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
                    url: 'admin/permission/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            toastr.success(response.message);
                            $('#TablePermission').DataTable().ajax.reload(null, false);
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
        let table = $('#TablePermission').DataTable();
        let selectedIds = [];
        
        // Ambil semua checkbox yang dicentang
        $('#TablePermission tbody input[type="checkbox"]:checked').each(function() {
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
            text: `Anda akan menghapus ${selectedIds.length} data permission yang dipilih!`,
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
                    url: 'admin/permission/delete-batch',
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
