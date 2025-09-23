$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

});

$(document).ready(function () {
    $('#TablePegawai').DataTable({
        dom:
            '<"row me-2"' +
            '<"col-md-2"<"me-3"l>>' +
            '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>' +
            '>t' +
            '<"row mx-2"' +
            '<"col-sm-12 col-md-6"i>' +
            '<"col-sm-12 col-md-6"p>' +
            '>',
        deferRender: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        stateSave: true,
        stateDuration: 300, // 5 minutes
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
              text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Pegawai</span>',
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
            url: "/hrd/pegawai/",
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
                data: 'foto_pegawai',
                name: 'foto_pegawai',
                render: function (data) {
                    if (data) {
                        // Data sudah berupa URL lengkap dari backend
                        return '<img src="' + data + '" alt="Avatar" class="img-thumbnail" width="40" height="40" style="border-radius:50px;" onerror="this.onerror=null; this.src=\'/assets/img/avatars/11.png\';" />';
                    } else {
                        return '<img src="/assets/img/avatars/11.png" alt="Avatar" class="img-thumbnail" width="40" height="40" style="border-radius:50px;" />';
                    }
                }
            },
            {
                data: 'nm_pegawai',
                name: 'nm_pegawai'
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'perusahaan.nama_perusahaan',
                name: 'perusahaan.nama_perusahaan'
            },
            {
                data: 'no_telp_pegawai',
                name: 'no_telp_pegawai'
            },
            {
                data: 'status',
                name: 'status',
                render: function (data, type, full, meta) {
                    var $inactive = '<span class="badge bg-label-danger">Inactive</span>';
                    var $aktif = '<span class="badge bg-label-success">Active</span>';
                    if (data == 'inactive') {
                        return $inactive;
                    } else if (data == 'active') {
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
                    let canEdit         = userPermissions.includes("edit_pegawai");
                    let canDelete       = userPermissions.includes("delete_pegawai");
                    let canShow         = userPermissions.includes("show_pegawai");

                    let buttons = '<div class="d-flex align-items-center">';

                    buttons += '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>';
                    buttons += '<div class="dropdown-menu dropdown-menu-end m-0">';
                    if (canShow) {
                      buttons += '<a href="javascript:;" class="dropdown-item" onclick="ViewData(' + full.id + ')"><i class="ti ti-eye ti-md"></i>Lihat Detail</a>';
                    }
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

    $('#pegawai-provinsi').on('change', function() {
        var id_provinsi = $(this).val();

        $('#pegawai-kota').empty().append('<option selected disabled>Loading...</option>');

        $.ajax({
            url: "/hrd/pegawai/get-kota/" + id_provinsi,
            type: "GET",
            xhrFields: { withCredentials: true },
            success: function (response) {

                $('#pegawai-kota').empty().append('<option selected disabled>Pilih Kota</option>');

                response.forEach(function (data) { // Gunakan response langsung
                    $('#pegawai-kota').append('<option value="' + data.id_kota + '">' + data.name + '</option>');
                });
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    $(document).ready(function(){
        $('#formPegawai').on('submit', function(e){
            e.preventDefault();

            // Tambahkan loader pada tombol submit
            var submitBtn = $(this).find('button[type="submit"]');
            var originalText = submitBtn.html();
            submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Menyimpan...').prop('disabled', true);

            let formData = new FormData(this);
            let id = $('#id').val();
            let url = '';

            if(id == "" || id == 0){
                url = "/hrd/pegawai/store";
            } else {
                url = "/hrd/pegawai/update/" + id;
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
                        $('#formPegawai')[0].reset();
                        $('#TablePegawai').DataTable().ajax.reload(null, false);
                        let modal = bootstrap.Modal.getInstance(document.getElementById('tambahModal'));
                        modal.hide();
                    } else {
                        toastr.error('Terjadi kesalahan, silakan coba lagi!');
                    }
                    // Kembalikan tombol ke kondisi semula
                    submitBtn.html(originalText).prop('disabled', false);
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
                    // Kembalikan tombol ke kondisi semula
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Function untuk mengisi modal saat update
        window.ViewData = function (id) {
            $('#tambahModal').modal('show');

            if (id === 0) {
                // Mode Insert (Tambah Data)
                $('#modal-judul').text('Tambah Pegawai');
                $('#formPegawai')[0].reset();
                $('#btn-simpan').val('create');

                // Reset hierarki dropdowns
                $('#pegawai-cabang').empty().append('<option value="" selected disabled>Pilih Cabang</option>');
                $('#pegawai-departemen').empty().append('<option value="" selected disabled>Pilih Departemen</option>');
            } else {
                // Mode Edit (Ambil data dari API)
                $('#modal-judul').text('Edit Pegawai');
                $('#btn-simpan').val('update');

                $.ajax({
                    url: '/hrd/pegawai/edit/' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        if (response) {
                            $('#id').val(response.id);
                            $('#user_id').val(response.user_id);
                            $('#nm_pegawai').val(response.nm_pegawai);
                            $('#jenis_kelamin').val(response.jenis_kelamin);
                            $('#tgl_lahir_pegawai').val(response.tgl_lahir_pegawai);
                            $('#email').val(response.user.email);
                            $('#no_telp_pegawai').val(response.no_telp_pegawai);
                            $('#no_ktp_pegawai').val(response.no_ktp_pegawai);
                            $('#pegawai-perusahaan').val(response.id_perusahaan).trigger('change');
                            $('#pegawai-divisi').val(response.id_divisi).trigger('change');

                            // Populate cabang dropdown
                            if (response.id_perusahaan) {
                                $.ajax({
                                    url: `/hrd/pegawai/get-cabang/${response.id_perusahaan}`,
                                    type: 'GET',
                                    success: function(cabangResponse) {
                                        $('#pegawai-cabang').empty().append('<option value="" selected disabled>Pilih Cabang</option>');
                                        if (cabangResponse.length > 0) {
                                            cabangResponse.forEach(function(cabang) {
                                                $('#pegawai-cabang').append(`<option value="${cabang.id}">${cabang.nama_cabang}</option>`);
                                            });
                                            $('#pegawai-cabang').val(response.id_cabang);
                                        }
                                    }
                                });
                            }

                            // Populate departemen dropdown
                            if (response.id_divisi) {
                                $.ajax({
                                    url: `/hrd/pegawai/get-departemen/${response.id_divisi}`,
                                    type: 'GET',
                                    success: function(departemenResponse) {
                                        $('#pegawai-departemen').empty().append('<option value="" selected disabled>Pilih Departemen</option>');
                                        if (departemenResponse.length > 0) {
                                            departemenResponse.forEach(function(departemen) {
                                                $('#pegawai-departemen').append(`<option value="${departemen.id}">${departemen.nama_departemen}</option>`);
                                            });
                                            $('#pegawai-departemen').val(response.id_departemen);
                                        }
                                    }
                                });
                            }
                            $('#pegawai-jabatan').val(response.id_jabatan);
                            $('#no_sim_pegawai').val(response.no_sim_pegawai);
                            $('#no_npwp_pegawai').val(response.no_npwp_pegawai);
                            $('#active').val(response.status);
                            $('#alamat_pegawai').val(response.alamat_pegawai);
                            $('#pegawai-provinsi').val(response.id_provinsi).trigger('change');
                            $.ajax({
                                url: "/hrd/pegawai/get-kota/" + response.id_provinsi,
                                type: "GET",
                                success: function(kotaResponse) {
                                    $('#pegawai-kota').empty().append('<option selected disabled>Pilih Kota</option>');

                                    kotaResponse.forEach(function (data) {
                                        $('#pegawai-kota').append('<option value="' + data.id_kota + '">' + data.name + '</option>');
                                    });

                                    $('#pegawai-kota').val(response.id_kota).trigger('change');
                                }
                            });
                            $('#tgl_masuk_pegawai').val(response.tgl_masuk_pegawai);
                            $('#tgl_keluar_pegawai').val(response.tgl_keluar_pegawai);
                            $('#jabatan_pegawai').val(response.jabatan_pegawai);
                            $('#gaji_pegawai').val(response.gaji_pegawai);
                        }
                    },
                    error: function() {
                        alert('Gagal mengambil data!');
                    }
                });
            }
        }

    });

    $(document).on('click', '.delete-record', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data pegawai akan dihapus!",
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
                    url: '/hrd/pegawai/delete/' + id,
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
                            $('#TablePegawai').DataTable().ajax.reload(); // Reload DataTable
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

    // Reset hierarki dropdowns when modal is closed
    $('#tambahModal').on('hidden.bs.modal', function() {
        // Reset cabang dropdown
        $('#pegawai-cabang').empty().append('<option value="" selected disabled>Pilih Cabang</option>');

        // Reset departemen dropdown
        $('#pegawai-departemen').empty().append('<option value="" selected disabled>Pilih Departemen</option>');
    });

});
