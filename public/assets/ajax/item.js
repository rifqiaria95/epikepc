$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

});

$(document).ready(function () {
    $('#TableItem').DataTable({
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
        stateDuration: 300,
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
                          if (item.classList !== undefined && item.classList.contains('item-nm_item')) {
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
                          if (item.classList !== undefined && item.classList.contains('item-nm_item')) {
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
                          if (item.classList !== undefined && item.classList.contains('item-nm_item')) {
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
                          if (item.classList !== undefined && item.classList.contains('item-nm_item')) {
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
                          if (item.classList !== undefined && item.classList.contains('item-nm_item')) {
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
              text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Item</span>',
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
            url: "/inventory/item/",
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
              data: 'kd_barcode',
              name: 'kd_barcode'
            },
            {
              data: 'kd_item',
              name: 'kd_item'
            },
            {
              data: 'nm_item',
              name: 'nm_item'
            },
            {
              data: 'kategori',
              name: 'kategori',
            },
            {
              data: 'stok_satuan',
              name: 'stok_satuan'
            },
            {
              data: 'aksi',
              name: 'aksi',
              render: function (data, type, full, meta) {
                let userPermissions = window.userPermissions || [];
                let canEdit         = userPermissions.includes("edit_item");
                let canDelete       = userPermissions.includes("delete_item");

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

    $('#formItem').on('submit', function(e){
      e.preventDefault();

      // Tambahkan loader pada tombol submit
      var submitBtn = $(this).find('button[type="submit"]');
      var originalText = submitBtn.html();
      submitBtn.html('<i class="ti ti-loader ti-spin me-2"></i>Menyimpan...').prop('disabled', true);

      $('#formItem .form-control, #formItem .form-select').removeClass('is-invalid');
      $('#formItem .text-danger.small').text('');

      let formData = new FormData(this);
      let id = $('#id').val();
      let url = '';

      if(id == "" || id == 0){
          url = "/inventory/item/store";
      } else {
          url = "/inventory/item/update/" + id;
          formData.append('_method', 'PUT');
      }

      $.ajax({
          url: url,
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          success: function(response){
              if(response.status == 200){
                  toastr.success('Data berhasil disimpan!');
                  $('#formItem')[0].reset();
                  $('#TableItem').DataTable().ajax.reload(null, false);
                  let modal = bootstrap.Modal.getInstance(document.getElementById('tambahModal'));
                  modal.hide();
              } else {
                  toastr.error('Terjadi kesalahan, silakan coba lagi!');
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

    $('#tambahModal').on('hidden.bs.modal', function () {
        $('#formItem')[0].reset();
        $('#id').val('');
        $('#id_kategori').val(null).trigger('change');
        $('#id_unit_berat').val(null).trigger('change');

        $('#modal-judul').text('Tambah Item');
        $('#formItem .form-control, #formItem .form-select').removeClass('is-invalid');
        $('#formItem .text-danger.small').text('');
    });

    $(document).on('click', '.edit-record', function () {
        let id = $(this).data('id');

        $('#formItem .form-control, #formItem .form-select').removeClass('is-invalid');
        $('#formItem .text-danger.small').text('');

        $.ajax({
            url: '/inventory/item/edit/' + id,
            type: 'GET',
            success: function(response) {
                if (response) {
                    $('#modal-judul').text('Edit Item');
                    $('#id').val(response.id);
                    $('#id_unit_berat').val(response.id_unit_berat).trigger('change');
                    $('#id_kategori').val(response.id_kategori).trigger('change');
                    $('#nm_item').val(response.nm_item);
                    $('#deskripsi').val(response.deskripsi);
                    $('#tambahModal').modal('show');
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat mengambil data.');
            }
        });
    });

    $(document).on('click', '.delete-record', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data item akan dihapus!",
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
                    url: '/inventory/item/delete/' + id,
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
                            $('#TableItem').DataTable().ajax.reload();
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
