@extends('layouts.frontend.main')

@section('title', 'Lamar '.$vacancy->title.' | EPIKEPC')
@section('page', 'careers')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/contacts.min.css') }}" />
    <style>
        .career-apply { padding: 40px 0 80px; }
        .career-apply .field, .career-apply select.field, .career-apply textarea.field {
            width: 100%; border: 1px solid #d9dee8; background: #fff; color: #000810; padding: 14px 16px; font-size: 16px;
        }
        .career-apply .field:focus { border-color: #ffdf08; outline: none; }
        .career-apply label { display: block; font-weight: 600; color: #253C74; margin-bottom: 6px; }
        .career-apply .hint { font-size: 13px; color: #6b7280; margin-top: 4px; }
        .career-apply .error { color: #b42318; font-size: 13px; margin-top: 4px; }
        .career-apply fieldset { border: 1px solid #e5e9f2; padding: 24px; margin-bottom: 28px; }
        .career-apply legend { font-size: 20px; color: #202C38; padding: 0 8px; }
        .career-apply .req { color: #b42318; }
        .career-hp { position: absolute; left: -9999px; }
        .career-error-summary { background: #fff4e5; border: 1px solid #f5c27a; padding: 16px; margin-bottom: 24px; }
        .career-vacancy-box { background: #f7f9fc; border: 1px solid #e5e9f2; padding: 20px; margin-bottom: 28px; }
        .is-invalid { border-color: #b42318 !important; }
    </style>
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Lamar posisi',
        'title' => $vacancy->title,
        'items' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Karir', 'url' => route('frontend.careers.index')],
            ['label' => $vacancy->title, 'url' => route('frontend.careers.show', $vacancy->slug)],
            ['label' => 'Lamar'],
        ],
    ])
@endsection

@section('content')
    @php
        $errorsBag = $errors->getBag('default');
    @endphp
    <main class="career-apply">
        <div class="container">
            <div class="career-vacancy-box">
                <strong>{{ $vacancy->title }}</strong>
                <div>{{ $vacancy->code }} · {{ $vacancy->department }} · {{ $vacancy->locationLabel() }}</div>
            </div>

            @if ($errors->any())
                <div class="career-error-summary" role="alert" tabindex="-1" id="career-error-summary">
                    <strong>Periksa kembali isian berikut:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="career-apply-form" method="POST" action="{{ route('frontend.careers.apply.store', $vacancy->slug) }}" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="career-hp" aria-hidden="true">
                    <label for="website">Website</label>
                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                </div>

                <fieldset>
                    <legend>1. Data diri</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="full_name">Nama lengkap sesuai identitas resmi <span class="req">*</span></label>
                            <input class="field @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name') }}" required autocomplete="name">
                            @error('full_name') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email">Email <span class="req">*</span></label>
                            <input class="field @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone">Nomor WhatsApp aktif <span class="req">*</span></label>
                            <input class="field @error('phone') is-invalid @enderror" id="phone" name="phone" type="tel" value="{{ old('phone') }}" required autocomplete="tel">
                            @error('phone') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="domicile_city">Kota/kabupaten domisili <span class="req">*</span></label>
                            <input class="field @error('domicile_city') is-invalid @enderror" id="domicile_city" name="domicile_city" value="{{ old('domicile_city') }}" required>
                            @error('domicile_city') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="domicile_province">Provinsi domisili <span class="req">*</span></label>
                            <input class="field @error('domicile_province') is-invalid @enderror" id="domicile_province" name="domicile_province" value="{{ old('domicile_province') }}" required>
                            @error('domicile_province') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>2. Pendidikan dan pengalaman</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="highest_education">Pendidikan terakhir <span class="req">*</span></label>
                            <select class="field @error('highest_education') is-invalid @enderror" id="highest_education" name="highest_education" required>
                                <option value="">Pilih</option>
                                @foreach (['SMA/SMK','D3','S1','S2','S3'] as $edu)
                                    <option value="{{ $edu }}" @selected(old('highest_education') === $edu)>{{ $edu }}</option>
                                @endforeach
                            </select>
                            @error('highest_education') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="education_major">Jurusan <span class="req">*</span></label>
                            <input class="field @error('education_major') is-invalid @enderror" id="education_major" name="education_major" value="{{ old('education_major') }}" required>
                            @error('education_major') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="institution_name">Institusi</label>
                            <input class="field" id="institution_name" name="institution_name" value="{{ old('institution_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="graduation_year">Tahun lulus</label>
                            <input class="field @error('graduation_year') is-invalid @enderror" id="graduation_year" name="graduation_year" type="number" value="{{ old('graduation_year') }}">
                            @error('graduation_year') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="total_experience_years">Total pengalaman (tahun) <span class="req">*</span></label>
                            <input class="field @error('total_experience_years') is-invalid @enderror" id="total_experience_years" name="total_experience_years" type="number" step="0.5" min="0" value="{{ old('total_experience_years') }}" required>
                            @error('total_experience_years') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="current_or_last_company">Perusahaan terakhir</label>
                            <input class="field" id="current_or_last_company" name="current_or_last_company" value="{{ old('current_or_last_company') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="current_or_last_title">Jabatan terakhir</label>
                            <input class="field" id="current_or_last_title" name="current_or_last_title" value="{{ old('current_or_last_title') }}">
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>3. Ketersediaan dan preferensi kerja</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="availability_type">Ketersediaan bergabung <span class="req">*</span></label>
                            <select class="field @error('availability_type') is-invalid @enderror" id="availability_type" name="availability_type" required>
                                <option value="">Pilih</option>
                                @foreach (\App\Enums\Career\AvailabilityType::options() as $value => $label)
                                    <option value="{{ $value }}" @selected(old('availability_type') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('availability_type') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="available_from">Tanggal mulai tersedia</label>
                            <input class="field @error('available_from') is-invalid @enderror" id="available_from" name="available_from" type="date" value="{{ old('available_from') }}">
                            @error('available_from') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="willing_to_relocate">Bersedia relokasi</label>
                            <select class="field" id="willing_to_relocate" name="willing_to_relocate">
                                <option value="">Tidak diisi</option>
                                <option value="1" @selected(old('willing_to_relocate') === '1')>Ya</option>
                                <option value="0" @selected(old('willing_to_relocate') === '0')>Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="willing_to_travel_to_site">Bersedia ke lokasi proyek</label>
                            <select class="field" id="willing_to_travel_to_site" name="willing_to_travel_to_site">
                                <option value="">Tidak diisi</option>
                                <option value="1" @selected(old('willing_to_travel_to_site') === '1')>Ya</option>
                                <option value="0" @selected(old('willing_to_travel_to_site') === '0')>Tidak</option>
                            </select>
                        </div>
                        @if ($vacancy->allows_salary_expectation)
                            <div class="col-md-6">
                                <label for="expected_salary_amount">Ekspektasi gaji (IDR)</label>
                                <input class="field" id="expected_salary_amount" name="expected_salary_amount" inputmode="numeric" value="{{ old('expected_salary_amount') }}">
                            </div>
                        @endif
                        <div class="col-md-6">
                            <label for="latest_salary_amount">Gaji terakhir (opsional, IDR)</label>
                            <input class="field" id="latest_salary_amount" name="latest_salary_amount" inputmode="numeric" value="{{ old('latest_salary_amount') }}">
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>4. Tautan dan dokumen</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="linkedin_url">LinkedIn</label>
                            <input class="field @error('linkedin_url') is-invalid @enderror" id="linkedin_url" name="linkedin_url" type="url" value="{{ old('linkedin_url') }}">
                            @error('linkedin_url') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="portfolio_url">Portofolio</label>
                            <input class="field @error('portfolio_url') is-invalid @enderror" id="portfolio_url" name="portfolio_url" type="url" value="{{ old('portfolio_url') }}">
                            @error('portfolio_url') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label for="cover_letter">Surat lamaran</label>
                            <textarea class="field" id="cover_letter" name="cover_letter" rows="5">{{ old('cover_letter') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="referral_source">Sumber informasi</label>
                            <select class="field" id="referral_source" name="referral_source">
                                <option value="">Pilih</option>
                                @foreach (\App\Enums\Career\ReferralSource::options() as $value => $label)
                                    <option value="{{ $value }}" @selected(old('referral_source') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="referral_detail">Detail sumber</label>
                            <input class="field" id="referral_detail" name="referral_detail" value="{{ old('referral_detail') }}">
                        </div>
                        <div class="col-12">
                            <label for="cv">CV / Resume <span class="req">*</span></label>
                            <input class="field @error('cv') is-invalid @enderror" id="cv" name="cv" type="file" accept=".pdf,.doc,.docx,application/pdf" required>
                            <p class="hint">Format PDF, DOC, atau DOCX. Maksimal {{ number_format($maxCvKb / 1024, 0) }} MB.</p>
                            @error('cv') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </fieldset>

                @if ($vacancy->questions->isNotEmpty())
                    <fieldset>
                        <legend>5. Pertanyaan khusus lowongan</legend>
                        <div class="row g-3">
                            @foreach ($vacancy->questions as $question)
                                <div class="col-12">
                                    <label for="answer_{{ $question->id }}">
                                        {{ $question->question }}
                                        @if ($question->is_required) <span class="req">*</span> @endif
                                    </label>
                                    @if ($question->help_text)
                                        <p class="hint">{{ $question->help_text }}</p>
                                    @endif
                                    @php $oldAnswer = old('answers.'.$question->id); @endphp
                                    @if ($question->type === \App\Enums\Career\QuestionType::Textarea)
                                        <textarea class="field" id="answer_{{ $question->id }}" name="answers[{{ $question->id }}]" rows="4" @required($question->is_required)>{{ $oldAnswer }}</textarea>
                                    @elseif ($question->type === \App\Enums\Career\QuestionType::Boolean)
                                        <select class="field" id="answer_{{ $question->id }}" name="answers[{{ $question->id }}]" @required($question->is_required)>
                                            <option value="">Pilih</option>
                                            <option value="1" @selected($oldAnswer === '1')>Ya</option>
                                            <option value="0" @selected($oldAnswer === '0')>Tidak</option>
                                        </select>
                                    @elseif ($question->type === \App\Enums\Career\QuestionType::SingleChoice)
                                        <select class="field" id="answer_{{ $question->id }}" name="answers[{{ $question->id }}]" @required($question->is_required)>
                                            <option value="">Pilih</option>
                                            @foreach ($question->options ?? [] as $option)
                                                <option value="{{ $option }}" @selected($oldAnswer === $option)>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    @elseif ($question->type === \App\Enums\Career\QuestionType::MultipleChoice)
                                        @foreach ($question->options ?? [] as $option)
                                            <label class="d-block">
                                                <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option }}" @checked(is_array($oldAnswer) && in_array($option, $oldAnswer, true))>
                                                {{ $option }}
                                            </label>
                                        @endforeach
                                    @elseif ($question->type === \App\Enums\Career\QuestionType::Number)
                                        <input class="field" id="answer_{{ $question->id }}" type="number" name="answers[{{ $question->id }}]" value="{{ $oldAnswer }}" @required($question->is_required)>
                                    @else
                                        <input class="field" id="answer_{{ $question->id }}" name="answers[{{ $question->id }}]" value="{{ $oldAnswer }}" @required($question->is_required)>
                                    @endif
                                    @error('answers.'.$question->id) <div class="error">{{ $message }}</div> @enderror
                                </div>
                            @endforeach
                        </div>
                    </fieldset>
                @endif

                <fieldset>
                    <legend>6. Persetujuan</legend>
                    <p>Data dikumpulkan untuk proses rekrutmen EPIKEPC, disimpan sesuai kebijakan retensi {{ $retentionMonths }} bulan, dan tidak menjamin kelanjutan proses. Hanya kandidat yang lolos seleksi awal yang akan dihubungi.</p>
                    <p><a href="{{ $privacyUrl }}" target="_blank" rel="noopener">Baca pemberitahuan privasi</a>.</p>
                    <label>
                        <input type="checkbox" name="privacy_consent" value="1" @checked(old('privacy_consent')) required>
                        Saya menyetujui pemrosesan data sesuai pemberitahuan privasi. <span class="req">*</span>
                    </label>
                    @error('privacy_consent') <div class="error">{{ $message }}</div> @enderror
                    <label class="d-block mt-2">
                        <input type="checkbox" name="accuracy_declaration" value="1" @checked(old('accuracy_declaration')) required>
                        Saya menyatakan data yang disampaikan akurat. <span class="req">*</span>
                    </label>
                    @error('accuracy_declaration') <div class="error">{{ $message }}</div> @enderror
                </fieldset>

                <button class="btn btn--submit btn--static" id="career-submit" type="submit">Kirim lamaran</button>
            </form>
        </div>
    </main>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('career-apply-form');
    var button = document.getElementById('career-submit');
    if (!form || !button) return;
    var firstInvalid = form.querySelector('.is-invalid, .error');
    if (firstInvalid) {
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        var input = form.querySelector('.is-invalid');
        if (input) input.focus();
    }
    form.addEventListener('submit', function () {
        button.disabled = true;
        button.textContent = 'Mengirim...';
    });
})();
</script>
@endpush
