$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Inisialisasi DataTable
    initializeDataTable();

    // Inisialisasi Select2
    $('.select2').select2({
        dropdownParent: $('#tambahModal')
    });

    // Event handler untuk form submit
    $('#formProgram').on('submit', function(e){
        e.preventDefault();
        handleFormSubmit();
    });

    // Event handler untuk delete
    $(document).on('click', '.delete-record', function () {
        handleDelete();
    });
});

// Function untuk inisialisasi DataTable
function initializeDataTable() {
    try {
        // Pastikan DataTable library sudah dimuat
        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTable library not loaded');
            return;
        }

        // Pastikan elemen table ada
        var table = $('#TableProgramReq');
        if (!table.length) {
            console.error('Table element not found');
            return;
        }

        // Destroy DataTable jika sudah ada
        if ($.fn.DataTable.isDataTable('#TableProgramReq')) {
            $('#TableProgramReq').DataTable().destroy();
        }

        // Inisialisasi DataTable baru
        $('#TableProgramReq').DataTable({
            dom: '<"row me-2"' +
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
                                columns: [1, 2, 3, 4, 5]
                            },
                            customize: function (win) {
                                $(win.document.body)
                                    .css('color', '#566a7f')
                                    .css('border-color', '#d9dee3')
                                    .css('background-color', '#fff');
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
                                columns: [1, 2, 3, 4, 5]
                            }
                        },
                        {
                            extend: 'excel',
                            text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
                            className: 'dropdown-item',
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5]
                            }
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="ti ti-file-code-2 me-2"></i>Pdf',
                            className: 'dropdown-item',
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5]
                            }
                        },
                        {
                            extend: 'copy',
                            text: '<i class="ti ti-copy me-2" ></i>Copy',
                            className: 'dropdown-item',
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5]
                            }
                        }
                    ]
                },
                {
                    text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Program</span>',
                    className: 'add-new btn btn-primary waves-effect waves-light',
                    attr: {
                        'data-bs-toggle': 'modal',
                        'data-bs-target': '#tambahModal'
                    }
                }
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: "/program/program-requirement",
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
                    data: 'program',
                    name: 'program'
                },
                {
                    data: 'field',
                    name: 'field'
                },
                {
                    data: 'operator',
                    name: 'operator'
                },
                {
                    data: 'value',
                    name: 'value'
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    render: function (data, type, full, meta) {
                        let userPermissions = window.userPermissions || [];
                        let canEdit = userPermissions.includes("edit_program_requirement");
                        let canDelete = userPermissions.includes("delete_program_requirement");

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
            ]
        });
    } catch (error) {
        console.error('Error initializing DataTable:', error);
    }
}

// Function untuk handle form submit
function handleFormSubmit() {
    let formData = new FormData($('#formProgram')[0]);
    let id = $('#id').val();
    let url = '';

    if(id == "" || id == 0){
        url = "/program/program-requirement/store";
    } else {
        url = "/program/program-requirement/update/" + id;
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
                $('#TableProgramReq').DataTable().ajax.reload(null, false);
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
}

// Function untuk handle delete
function handleDelete() {
    console.log("Delete diklik!");

    let id = $(this).data('id');

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data registrasi program akan dihapus!",
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
                url: '/program/program-requirement/delete/' + id,
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
                        $('#TableProgramReq').DataTable().ajax.reload();
                    }
                },
                error: function () {
                    Swal.fire('Oops!', 'Terjadi kesalahan saat menghapus data.', 'error');
                }
            });
        }
    });
}

// Function untuk mengisi modal saat update
window.ViewData = function (id) {
    $('#tambahModal').modal('show');

    if (id === 0) {
        // Mode Insert (Tambah Data)
        $('#modal-judul').text('Tambah Program Requirement');
        $('#formProgram')[0].reset();
        $('#btn-simpan').val('create');
    } else {
        // Mode Edit (Ambil data dari API)
        $('#modal-judul').text('Edit Program Requirement');
        $('#btn-simpan').val('update');

        $.ajax({
            url: '/program/program-requirement/edit/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response) {
                    $('#id').val(response.id);
                    $('#program_id').val(response.program_id).trigger('change');
                    $('#field').val(response.field);
                    $('#operator').val(response.operator);
                    $('#value').val(response.value);
                    // Set program_id langsung karena dropdown sudah terisi
                    $('#program_id').val(response.program_id).trigger('change');
                }
            },
            error: function() {
                alert('Gagal mengambil data!');
            }
        });
    }
}

