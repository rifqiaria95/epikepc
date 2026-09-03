@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h4 class="mb-1">{{ $application->reference_number }}</h4>
                <p class="text-muted mb-0">{{ $application->vacancy?->title }} · {{ $application->status->label() }}</p>
            </div>
            <a href="{{ route('career.applications.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Kandidat</h5></div>
                    <div class="card-body">
                        <p><strong>{{ $application->candidate?->full_name }}</strong><br>
                            {{ $application->candidate?->email }} · {{ $application->candidate?->phone }}<br>
                            {{ $application->candidate?->domicile_city }}, {{ $application->candidate?->domicile_province }}</p>
                        <p>Pendidikan: {{ $application->candidate?->highest_education }} {{ $application->candidate?->education_major }}<br>
                            Pengalaman: {{ $application->candidate?->total_experience_years }} tahun</p>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Jawaban screening</h5></div>
                    <div class="card-body">
                        @forelse ($application->answers as $answer)
                            <p><strong>{{ $answer->question_text }}</strong><br>
                                {{ $answer->answer_text ?? implode(', ', $answer->answer_json ?? []) }}</p>
                        @empty
                            <p class="text-muted">Tidak ada pertanyaan.</p>
                        @endforelse
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Dokumen</h5></div>
                    <div class="card-body">
                        @foreach ($application->documents as $document)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    {{ $document->document_type->label() }} — {{ $document->original_name }}
                                    <small class="text-muted d-block">Scan: {{ $document->scan_status->label() }}</small>
                                </div>
                                @can('download', $document)
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('career.applications.documents.download', [$application->id, $document->id]) }}">Unduh</a>
                                @endcan
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Riwayat status</h5></div>
                    <div class="card-body">
                        @foreach ($application->statusHistories as $history)
                            <p class="mb-2">
                                {{ $history->created_at?->format('d M Y H:i') }}:
                                {{ $history->from_status?->label() ?? '—' }} → {{ $history->to_status->label() }}
                                @if ($history->internal_note)
                                    <br><em>Internal:</em> {{ $history->internal_note }}
                                @endif
                                @if ($history->public_message)
                                    <br><em>Publik:</em> {{ $history->public_message }}
                                @endif
                            </p>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Ubah status</h5></div>
                    <div class="card-body">
                        <form id="formTransition">
                            @csrf
                            <select class="form-select mb-2" name="to_status" required>
                                <option value="">Pilih status</option>
                                @foreach ($allowedTargets as $target)
                                    <option value="{{ $target }}">{{ $statuses[$target] ?? $target }}</option>
                                @endforeach
                            </select>
                            <input class="form-control mb-2" name="reason_code" placeholder="Kode alasan (opsional)">
                            <textarea class="form-control mb-2" name="public_message" placeholder="Pesan publik (aman untuk kandidat)"></textarea>
                            <textarea class="form-control mb-2" name="internal_note" placeholder="Catatan internal"></textarea>
                            <button class="btn btn-primary w-100" type="submit">Terapkan</button>
                        </form>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Rekruter</h5></div>
                    <div class="card-body">
                        <form id="formAssign">
                            @csrf
                            <select class="form-select mb-2" name="assigned_recruiter_id">
                                <option value="">Belum ditetapkan</option>
                                @foreach ($recruiters as $recruiter)
                                    <option value="{{ $recruiter->id }}" @selected($application->assigned_recruiter_id === $recruiter->id)>{{ $recruiter->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-primary w-100" type="submit">Simpan</button>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Catatan internal</h5></div>
                    <div class="card-body">
                        @foreach ($application->notes as $note)
                            <div class="border-bottom py-2">
                                <strong>{{ $note->createdBy?->name }}</strong>
                                @if ($note->is_pinned) <span class="badge bg-label-warning">Pinned</span> @endif
                                <p class="mb-1">{{ $note->note }}</p>
                            </div>
                        @endforeach
                        <form class="mt-3" id="formNote">
                            @csrf
                            <textarea class="form-control mb-2" name="note" required></textarea>
                            <button class="btn btn-outline-secondary w-100" type="submit">Tambah catatan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
(function () {
    function post(url, form) {
        return fetch(url, { method: 'POST', headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}, body: new FormData(form) })
            .then(function (r) { return r.json().then(function (j) { if (!r.ok) throw j; return j; }); });
    }
    var t = document.getElementById('formTransition');
    if (t) t.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!confirm('Ubah status lamaran ini?')) return;
        post(@json(route('career.applications.transition', $application->id)), t).then(function () { location.reload(); }).catch(function (err) { alert(err.message || 'Gagal'); });
    });
    var a = document.getElementById('formAssign');
    if (a) a.addEventListener('submit', function (e) {
        e.preventDefault();
        post(@json(route('career.applications.assign', $application->id)), a).then(function () { location.reload(); }).catch(function (err) { alert(err.message || 'Gagal'); });
    });
    var n = document.getElementById('formNote');
    if (n) n.addEventListener('submit', function (e) {
        e.preventDefault();
        post(@json(route('career.applications.notes.store', $application->id)), n).then(function () { location.reload(); }).catch(function (err) { alert(err.message || 'Gagal'); });
    });
})();
</script>
@endsection
