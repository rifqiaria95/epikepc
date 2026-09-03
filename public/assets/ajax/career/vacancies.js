$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    var permissions = window.userPermissions || [];

    $('#TableVacancies').DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: '/internal/career/vacancies', type: 'GET' },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'code', name: 'code' },
            { data: 'title', name: 'title' },
            { data: 'department', name: 'department' },
            { data: 'location', name: 'location_city' },
            { data: 'status', name: 'status' },
            { data: 'applications_count', name: 'applications_count' },
            {
                data: 'aksi',
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    var html = '<div class="dropdown"><a class="btn btn-icon btn-text-secondary" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></a><div class="dropdown-menu">';
                    if (permissions.includes('edit_vacancies')) html += '<a class="dropdown-item" href="javascript:;" onclick="editVacancy(\'' + full.id + '\')">Edit</a>';
                    if (permissions.includes('publish_vacancies')) html += '<a class="dropdown-item" href="javascript:;" onclick="careerAction(\'/internal/career/vacancies/' + full.id + '/publish\')">Publish</a>';
                    if (permissions.includes('close_vacancies')) html += '<a class="dropdown-item" href="javascript:;" onclick="careerAction(\'/internal/career/vacancies/' + full.id + '/close\')">Tutup</a>';
                    if (permissions.includes('archive_vacancies')) html += '<a class="dropdown-item" href="javascript:;" onclick="careerAction(\'/internal/career/vacancies/' + full.id + '/archive\')">Arsip</a>';
                    if (permissions.includes('create_vacancies')) html += '<a class="dropdown-item" href="javascript:;" onclick="careerAction(\'/internal/career/vacancies/' + full.id + '/duplicate\')">Duplikasi</a>';
                    html += '</div></div>';
                    return html;
                }
            }
        ]
    });

    if (permissions.includes('create_vacancies')) {
        new $.fn.dataTable.Buttons($('#TableVacancies').DataTable(), {
            buttons: [{
                text: 'Tambah lowongan',
                className: 'btn btn-primary',
                action: function () { resetVacancyForm(); $('#vacancyModal').modal('show'); }
            }]
        });
    }

    $('#addQuestion').on('click', function () { addQuestionRow(); });

    $('#formVacancy').on('submit', function (e) {
        e.preventDefault();
        var id = $('#vacancy_id').val();
        var url = id ? '/internal/career/vacancies/update/' + id : '/internal/career/vacancies/store';
        var form = $(this);
        var data = form.serialize() + (id ? '&_method=PUT' : '');
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function (res) {
                $('#vacancyModal').modal('hide');
                $('#TableVacancies').DataTable().ajax.reload();
                if (window.toastr) toastr.success(res.message);
            },
            error: function (xhr) {
                var errors = (xhr.responseJSON && xhr.responseJSON.errors) || {};
                alert(Object.values(errors).flat().join('\n') || (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal menyimpan');
            }
        });
    });
});

function resetVacancyForm() {
    $('#formVacancy')[0].reset();
    $('#vacancy_id').val('');
    $('#questionsWrap').empty();
}

function addQuestionRow(data) {
    data = data || {};
    var types = window.careerQuestionTypes || {};
    var options = Object.keys(types).map(function (k) {
        return '<option value="' + k + '"' + (data.type === k ? ' selected' : '') + '>' + types[k] + '</option>';
    }).join('');
    var idx = Date.now() + Math.floor(Math.random() * 1000);
    var html = '<div class="border p-3 mb-2 question-row">' +
        '<input type="hidden" name="questions[' + idx + '][id]" value="' + (data.id || '') + '">' +
        '<input class="form-control mb-2" name="questions[' + idx + '][question]" placeholder="Pertanyaan" value="' + (data.question || '') + '">' +
        '<select class="form-select mb-2" name="questions[' + idx + '][type]">' + options + '</select>' +
        '<input class="form-control mb-2" name="questions[' + idx + '][help_text]" placeholder="Teks bantuan" value="' + (data.help_text || '') + '">' +
        '<input class="form-control mb-2" name="questions[' + idx + '][options_text]" placeholder="Opsi (pisahkan koma)">' +
        '<label class="form-check"><input class="form-check-input" type="checkbox" name="questions[' + idx + '][is_required]" value="1"' + (data.is_required !== false ? ' checked' : '') + '> Wajib</label>' +
        '</div>';
    $('#questionsWrap').append(html);
}

function editVacancy(id) {
    $.get('/internal/career/vacancies/edit/' + id, function (res) {
        var v = res.vacancy;
        $('#vacancy_id').val(v.id);
        $('#title').val(v.title);
        $('#department').val(v.department);
        $('#location_city').val(v.location_city);
        $('#location_province').val(v.location_province);
        $('#employment_type').val(v.employment_type);
        $('#work_arrangement').val(v.work_arrangement);
        $('#experience_level').val(v.experience_level);
        $('#summary').val(v.summary);
        $('#description').val(v.description);
        $('#responsibilities').val(v.responsibilities);
        $('#qualifications').val(v.qualifications);
        $('#preferred_qualifications').val(v.preferred_qualifications);
        $('#minimum_education').val(v.minimum_education);
        $('#minimum_experience_years').val(v.minimum_experience_years);
        $('#headcount').val(v.headcount);
        $('#requires_site_travel').prop('checked', !!v.requires_site_travel);
        $('#allows_salary_expectation').prop('checked', !!v.allows_salary_expectation);
        $('#questionsWrap').empty();
        (v.questions || []).forEach(addQuestionRow);
        $('#vacancyModal').modal('show');
    });
}

function careerAction(url) {
    if (!confirm('Lanjutkan tindakan ini?')) return;
    $.post(url, { _token: $('meta[name="csrf-token"]').attr('content') }, function (res) {
        $('#TableVacancies').DataTable().ajax.reload();
        if (window.toastr) toastr.success(res.message);
    }).fail(function (xhr) {
        alert((xhr.responseJSON && xhr.responseJSON.message) || 'Gagal');
    });
}
