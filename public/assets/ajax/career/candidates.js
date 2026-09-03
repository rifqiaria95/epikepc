$(document).ready(function () {
    $('#TableCandidates').DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: '/internal/career/candidates', type: 'GET' },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'full_name', name: 'full_name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'domicile_city', name: 'domicile_city' },
            { data: 'highest_education', name: 'highest_education' },
            { data: 'total_experience_years', name: 'total_experience_years' },
            { data: 'applications_count', name: 'applications_count' },
            {
                data: 'aksi',
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    return '<a class="btn btn-sm btn-outline-primary" href="/internal/career/candidates/' + full.id + '">Detail</a>';
                }
            }
        ]
    });
});
