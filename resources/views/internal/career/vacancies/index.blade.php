@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('internal.partials.stat-cards', ['stats' => $stats])
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">Lowongan</h5>
                    <p class="mb-0 text-muted">Kelola draft, publikasi, penutupan, dan pertanyaan screening.</p>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table id="TableVacancies" class="table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Judul</th>
                            <th>Departemen</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Lamaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="modal fade" id="vacancyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <form id="formVacancy">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title">Lowongan</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="vacancy_id">
                            <div class="row g-3">
                                <div class="col-md-8"><label class="form-label">Judul</label><input class="form-control" name="title" id="title"></div>
                                <div class="col-md-4"><label class="form-label">Departemen</label><input class="form-control" name="department" id="department"></div>
                                <div class="col-md-4"><label class="form-label">Kota</label><input class="form-control" name="location_city" id="location_city"></div>
                                <div class="col-md-4"><label class="form-label">Provinsi</label><input class="form-control" name="location_province" id="location_province"></div>
                                <div class="col-md-4"><label class="form-label">Tipe kerja</label>
                                    <select class="form-select" name="employment_type" id="employment_type">
                                        @foreach ($employmentTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4"><label class="form-label">Pengaturan kerja</label>
                                    <select class="form-select" name="work_arrangement" id="work_arrangement">
                                        @foreach ($workArrangements as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4"><label class="form-label">Level</label>
                                    <select class="form-select" name="experience_level" id="experience_level">
                                        @foreach ($experienceLevels as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4"><label class="form-label">Tutup</label><input class="form-control" type="datetime-local" name="closes_at" id="closes_at"></div>
                                <div class="col-12"><label class="form-label">Ringkasan</label><textarea class="form-control" name="summary" id="summary" rows="2"></textarea></div>
                                <div class="col-12"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description" id="description" rows="4"></textarea></div>
                                <div class="col-12"><label class="form-label">Tanggung jawab</label><textarea class="form-control" name="responsibilities" id="responsibilities" rows="3"></textarea></div>
                                <div class="col-12"><label class="form-label">Kualifikasi</label><textarea class="form-control" name="qualifications" id="qualifications" rows="3"></textarea></div>
                                <div class="col-12"><label class="form-label">Kualifikasi tambahan</label><textarea class="form-control" name="preferred_qualifications" id="preferred_qualifications" rows="2"></textarea></div>
                                <div class="col-md-4"><label class="form-label">Pendidikan minimum</label><input class="form-control" name="minimum_education" id="minimum_education"></div>
                                <div class="col-md-4"><label class="form-label">Pengalaman minimum (tahun)</label><input class="form-control" type="number" name="minimum_experience_years" id="minimum_experience_years"></div>
                                <div class="col-md-4"><label class="form-label">Headcount</label><input class="form-control" type="number" name="headcount" id="headcount" value="1"></div>
                                <div class="col-md-6"><label class="form-check"><input class="form-check-input" type="checkbox" name="requires_site_travel" id="requires_site_travel" value="1"> Wajib ke lokasi proyek</label></div>
                                <div class="col-md-6"><label class="form-check"><input class="form-check-input" type="checkbox" name="allows_salary_expectation" id="allows_salary_expectation" value="1"> Izinkan ekspektasi gaji</label></div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Pertanyaan screening</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addQuestion">Tambah pertanyaan</button>
                            </div>
                            <div id="questionsWrap" class="mt-3"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>window.userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name'));</script>
    <script>window.careerQuestionTypes = @json($questionTypes);</script>
    <script src="{{ asset('assets/ajax/career/vacancies.js') }}"></script>
@endsection
