$(document).ready(function () {
    $('#TableApplications').DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: '/internal/career/applications', type: 'GET' },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'reference_number', name: 'reference_number' },
            { data: 'candidate_name', name: 'candidate.full_name' },
            { data: 'candidate_email', name: 'candidate.email' },
            { data: 'vacancy_title', name: 'vacancy.title' },
            { data: 'status', name: 'status' },
            { data: 'email_verification_status', name: 'email_verification_status' },
            { data: 'recruiter_name', name: 'assignedRecruiter.name' },
            {
                data: 'aksi',
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    return '<a class="btn btn-sm btn-outline-primary" href="/internal/career/applications/' + full.id + '">Detail</a>';
                }
            }
        ]
    });
});
