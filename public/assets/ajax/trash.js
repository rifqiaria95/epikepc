$(document).ready(function () {
    $('#deletedTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/deleted/data',
            type: 'GET'
        },
        columns: [
            {
                data: 'nama',
                name: 'nama'
            },
            {
                data: 'kategori',
                name: 'kategori'
            },
            {
                data: 'deleted_at',
                name: 'deleted_at'
            },
            {
                data: 'aksi',
                name: 'aksi',
                orderable: false,
                searchable: false
            }
        ],
        order: [
            [0, 'desc']
        ],
    });

    $(document).on('click', '.restore-record', function () {
        let id = $(this).data('id');
        let kategori = $(this).data('kategori');

        Swal.fire({
            title: 'Kembalikan data?',
            text: "Data akan dikembalikan!",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, restore!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/deleted/restore',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id,
                        kategori: kategori
                    },
                    success: function (response) {
                        Swal.fire('Restored!', response.message, 'success');
                        $('#deletedTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        Swal.fire('Oops!', 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.delete-record', function () {
        let id = $(this).data('id');
        let kategori = $(this).data('kategori');

        Swal.fire({
            title: 'Hapus data?',
            text: "Data akan dihapus!",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/deleted/delete',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id,
                        kategori: kategori
                    },
                    success: function (response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        $('#deletedTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        Swal.fire('Oops!', 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    });
});
