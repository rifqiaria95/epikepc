$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#TableRole').DataTable({
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
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Role</span>',
                className: 'add-new btn btn-primary waves-effect waves-light',
                action: function () {  // Gunakan event action agar tetap bisa dipanggil dalam DataTables
                    ViewData(0);
                }
            }
        ],
        processing: true,
        serverSide: true, //aktifkan server-side
        ajax: {
            url: "admin/role/",
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
                name: 'name',
                render: function (data, type, full, meta) {
                    return '<span class="badge bg-label-success">'+ data +'</span>'
                }
            },
            {
                data: 'aksi',
                name: 'aksi',
                render: function (data, type, full, meta) {
                    let userPermissions = window.userPermissions || [];
                    let canEdit         = userPermissions.includes("edit_role");
                    let canDelete       = userPermissions.includes("delete_role");

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

    $("#tambahModal").on("show.bs.modal", function () {
        $("#id").val("");
        $("#formRole")[0].reset();
        
        // Initialize DataTable untuk permissions
        if ($.fn.DataTable.isDataTable('#permissionsTable')) {
            $('#permissionsTable').DataTable().destroy();
        }
        
        $('#permissionsTable').DataTable({
            "pageLength": 5,
            "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
            "searching": true,
            "paging": true,
            "info": true,
            "responsive": true,
            "order": [[0, 'asc']],
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ entries",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entries",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entries",
                "infoFiltered": "(filtered dari _MAX_ total entries)",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                },
                "emptyTable": "Tidak ada data yang tersedia"
            },
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                   '<"row"<"col-sm-12"tr>>' +
                   '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "drawCallback": function() {
                // Restore state checkbox setelah DataTable di-draw ulang
                restoreCheckboxState();
                // Update Select All state setelah DataTable di-draw ulang
                updateSelectAllState();
            }
        });
        
        // Event listener untuk perubahan halaman DataTable
        $('#permissionsTable').on('page.dt', function () {
            setTimeout(function() {
                restoreCheckboxState();
                updateSelectAllState();
            }, 100);
        });
        
        // Event listener untuk pencarian DataTable  
        $('#permissionsTable').on('search.dt', function () {
            setTimeout(function() {
                restoreCheckboxState();
                updateSelectAllState();
            }, 100);
        });
        
        // Reset Select All checkbox saat modal dibuka
        setTimeout(function() {
            updateSelectAllState();
        }, 100);
    });

    // Event listener saat modal ditutup
    $("#tambahModal").on("hide.bs.modal", function () {
        // Cleanup DataTable jika ada
        if ($.fn.DataTable.isDataTable('#permissionsTable')) {
            $('#permissionsTable').off('page.dt search.dt');
            $('#permissionsTable').DataTable().destroy();
        }
        
        // Reset selectedPermissions Set
        selectedPermissions.clear();
        
        // Reset semua checkbox
        $(".permission-checkbox").prop("checked", false);
        $('#selectAll').prop('checked', false).prop('indeterminate', false);
    });

    $(document).on("click", ".dropdown-edit", function () {
        var roleId = $(this).data("id");
        ViewData(roleId);
    });

    $(document).on("click", ".add-new", function () {
        ViewData(0);
    });

    // Variable untuk menyimpan state checkbox permissions
    var selectedPermissions = new Set();
    var isUpdatingSelectAll = false; // Flag untuk mencegah infinite loop

    // Fungsi Select All untuk Permission Checkboxes
    $(document).on('change', '#selectAll', function () {
        var isChecked = $(this).prop('checked');
        isUpdatingSelectAll = true; // Set flag untuk mencegah updateSelectAllState dipanggil
        
        // Cek apakah DataTable sudah diinisialisasi
        if ($.fn.DataTable.isDataTable('#permissionsTable')) {
            // Pengaruhi semua checkbox, tidak hanya yang terlihat
            $('.permission-checkbox').each(function() {
                var permissionId = $(this).val();
                if (isChecked) {
                    selectedPermissions.add(permissionId);
                    $(this).prop('checked', true);
                } else {
                    selectedPermissions.delete(permissionId);
                    $(this).prop('checked', false);
                }
            });
        } else {
            // Fallback untuk tabel biasa
            $('.permission-checkbox').each(function() {
                var permissionId = $(this).val();
                if (isChecked) {
                    selectedPermissions.add(permissionId);
                } else {
                    selectedPermissions.delete(permissionId);
                }
                $(this).prop('checked', isChecked);
            });
        }
        
        isUpdatingSelectAll = false; // Reset flag
    });

    // Event listener untuk individual permission checkboxes
    $(document).on('change', '.permission-checkbox', function () {
        var permissionId = $(this).val();
        var isChecked = $(this).prop('checked');
        
        // Update selectedPermissions Set
        if (isChecked) {
            selectedPermissions.add(permissionId);
        } else {
            selectedPermissions.delete(permissionId);
        }
        
        // Hanya update Select All state jika tidak sedang melakukan bulk update
        if (!isUpdatingSelectAll) {
            updateSelectAllState();
        }
    });

    // Fungsi untuk restore state checkbox berdasarkan selectedPermissions
    function restoreCheckboxState() {
        $('.permission-checkbox').each(function() {
            var permissionId = $(this).val();
            var shouldBeChecked = selectedPermissions.has(permissionId);
            $(this).prop('checked', shouldBeChecked);
        });
    }

    // Fungsi untuk mengupdate state Select All berdasarkan individual checkboxes
    function updateSelectAllState() {
        // Jangan update jika sedang dalam proses bulk update dari Select All
        if (isUpdatingSelectAll) {
            return;
        }
        
        var totalCheckboxes = $('.permission-checkbox').length;
        var checkedCount = selectedPermissions.size;
        
        if (checkedCount === 0) {
            // Tidak ada yang di-check
            $('#selectAll').prop('checked', false).prop('indeterminate', false);
        } else if (checkedCount === totalCheckboxes) {
            // Semua di-check
            $('#selectAll').prop('checked', true).prop('indeterminate', false);
        } else {
            // Sebagian di-check (indeterminate state)
            $('#selectAll').prop('checked', false).prop('indeterminate', true);
        }
    }

    // Fungsi untuk menampilkan data ke dalam modal (Tambah/Edit)
    window.ViewData = function (id) {
        $('#tambahModal').modal('show');

        if (id === 0) {
            // Mode Tambah
            $('#modal-judul').text('Tambah Role');
            $('#formRole')[0].reset();
            $('#btn-update').val('create');
            
            // Reset selectedPermissions Set
            selectedPermissions.clear();
            $(".permission-checkbox").prop("checked", false);
            
            // Reset Select All checkbox
            $('#selectAll').prop('checked', false).prop('indeterminate', false);

        } else {
            // Mode Edit
            $('#modal-judul').text('Edit Role');
            $('#btn-update').val('update');

            $.ajax({
                url: `admin/role/edit/${id}/`,
                type: "GET",
                success: function (response) {

                    if (!response.role) {
                        toastr.error("Role tidak ditemukan.");
                        return;
                    }

                    // Set Nama Role
                    $("#id").val(response.role.id);
                    $("#name").val(response.role.name);

                    // Reset selectedPermissions Set
                    selectedPermissions.clear();

                    // Uncheck semua checkbox dulu
                    $(".permission-checkbox").prop("checked", false);

                    // Centang checkbox sesuai permission yang dimiliki role
                    response.rolePermissions.forEach(function(permissionId) {
                        // Tambahkan ke selectedPermissions Set
                        selectedPermissions.add(permissionId.toString());
                        $("input.permission-checkbox[value='" + permissionId + "']").prop("checked", true);
                    });

                    // Jika DataTable sudah diinisialisasi, refresh dan restore state
                    if ($.fn.DataTable.isDataTable('#permissionsTable')) {
                        $('#permissionsTable').DataTable().draw(false);
                    } else {
                        // Jika belum ada DataTable, restore state secara manual
                        restoreCheckboxState();
                    }

                    // Update Select All checkbox berdasarkan permission yang dipilih  
                    setTimeout(function() {
                        updateSelectAllState();
                    }, 200);

                },
                error: function (xhr) {
                    toastr.error("Gagal mengambil data role.");
                }
            });
        }
    }

    // Submit Form: Tambah & Update
    $(document).ready(function () {
        $("#formRole").on("submit", function (e) {
            e.preventDefault();

            let formData = new FormData();
            
            // Tambahkan field form biasa
            formData.append("name", $("#name").val());
            
            let id = $("#id").val();
            if (id) {
                formData.append("id", id);
            }
            
            // Tambahkan CSRF token
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            
            // Tambahkan semua permission yang dipilih dari selectedPermissions Set
            selectedPermissions.forEach(function(permissionId) {
                formData.append("permissions[]", permissionId);
            });
            
            let url = id ? `admin/role/update/${id}` : "admin/role/store";
            let method = id ? "POST" : "POST"; // Laravel butuh _method untuk PUT

            if (id) {
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
                        $("#formRole")[0].reset();
                        
                        // Reset selectedPermissions Set
                        selectedPermissions.clear();
                        
                        // Reset semua checkbox termasuk Select All
                        $(".permission-checkbox").prop("checked", false);
                        $('#selectAll').prop('checked', false).prop('indeterminate', false);
                        
                        toastr.success(response.message);
                        $("#TableRole").DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr) {
                    console.error("Error response:", xhr.responseJSON);
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = "";
                    $.each(errors, function (key, value) {
                        errorMessages += value + "<br>";
                    });
                    toastr.error(errorMessages);
                }
            });
        });
    });

    $(document).on('click', '.delete-record', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data role akan dihapus!",
            icon: 'warning',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light',
                cancelButton: 'btn btn-label-secondary waves-effect waves-light'
            },
            showCancelButton: true,
            cancelButtonText: 'Batal',
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'admin/role/delete/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                            $('#TableRole').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menghapus data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Oops!', errorMessage, 'error');
                    }
                });
            }
        });
    });

});
