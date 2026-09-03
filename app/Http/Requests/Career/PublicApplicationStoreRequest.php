<?php

namespace App\Http\Requests\Career;

use App\Enums\Career\AvailabilityType;
use App\Enums\Career\ReferralSource;
use App\Models\Career\Candidate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PublicApplicationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => is_string($this->email) ? trim($this->email) : $this->email,
            'full_name' => is_string($this->full_name) ? trim($this->full_name) : $this->full_name,
            'phone' => is_string($this->phone) ? trim($this->phone) : $this->phone,
            'privacy_consent' => $this->boolean('privacy_consent'),
            'accuracy_declaration' => $this->boolean('accuracy_declaration'),
            'willing_to_relocate' => $this->nullableBool($this->input('willing_to_relocate')),
            'willing_to_travel_to_site' => $this->nullableBool($this->input('willing_to_travel_to_site')),
        ]);

        if ($this->exists('latest_salary_amount')) {
            $this->merge(['latest_salary_amount' => $this->normalizeMoney($this->input('latest_salary_amount'))]);
        }

        if ($this->exists('expected_salary_amount')) {
            $this->merge(['expected_salary_amount' => $this->normalizeMoney($this->input('expected_salary_amount'))]);
        }
    }

    public function rules(): array
    {
        $vacancy = $this->route('vacancy');
        $allowsSalary = (bool) ($vacancy?->allows_salary_expectation);

        return [
            'full_name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+\s().-]{8,40}$/'],
            'domicile_city' => ['required', 'string', 'max:120'],
            'domicile_province' => ['required', 'string', 'max:120'],
            'highest_education' => ['required', 'string', 'max:80'],
            'education_major' => ['required', 'string', 'max:160'],
            'institution_name' => ['nullable', 'string', 'max:190'],
            'graduation_year' => ['nullable', 'integer', 'min:1970', 'max:'.((int) date('Y') + 1)],
            'total_experience_years' => ['required', 'numeric', 'min:0', 'max:60'],
            'current_or_last_company' => ['nullable', 'string', 'max:190'],
            'current_or_last_title' => ['nullable', 'string', 'max:190'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'cover_letter' => ['nullable', 'string', 'max:5000'],
            'latest_salary_amount' => ['nullable', 'integer', 'min:0'],
            'expected_salary_amount' => [$allowsSalary ? 'nullable' : 'prohibited', 'nullable', 'integer', 'min:0'],
            'availability_type' => ['required', Rule::in(AvailabilityType::values())],
            'available_from' => ['nullable', 'required_if:availability_type,'.AvailabilityType::Custom->value, 'date', 'after_or_equal:today'],
            'willing_to_relocate' => ['nullable', 'boolean'],
            'willing_to_travel_to_site' => ['nullable', 'boolean'],
            'referral_source' => ['nullable', Rule::in(ReferralSource::values())],
            'referral_detail' => ['nullable', 'string', 'max:255'],
            'cv' => [
                'required',
                'file',
                'max:'.(int) config('career.documents.max_cv_kilobytes', 5120),
                'mimes:'.implode(',', config('career.documents.allowed_cv_mimes', ['pdf', 'doc', 'docx'])),
            ],
            'privacy_consent' => ['accepted'],
            'accuracy_declaration' => ['accepted'],
            'answers' => ['nullable', 'array'],
            'website' => ['nullable', 'size:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Format nomor WhatsApp tidak valid.',
            'domicile_city.required' => 'Kota/kabupaten domisili wajib diisi.',
            'domicile_province.required' => 'Provinsi domisili wajib diisi.',
            'highest_education.required' => 'Pendidikan terakhir wajib diisi.',
            'education_major.required' => 'Jurusan pendidikan wajib diisi.',
            'total_experience_years.required' => 'Total pengalaman kerja wajib diisi.',
            'linkedin_url.url' => 'Link LinkedIn harus berupa URL yang valid.',
            'portfolio_url.url' => 'Link portofolio harus berupa URL yang valid.',
            'availability_type.required' => 'Ketersediaan bergabung wajib dipilih.',
            'available_from.required_if' => 'Tanggal mulai tersedia wajib diisi.',
            'available_from.after_or_equal' => 'Tanggal mulai tersedia tidak valid.',
            'cv.required' => 'CV wajib diunggah dalam format PDF, DOC, atau DOCX.',
            'cv.mimes' => 'CV wajib diunggah dalam format PDF, DOC, atau DOCX.',
            'cv.max' => 'Ukuran CV maksimal 5 MB.',
            'privacy_consent.accepted' => 'Anda harus menyetujui pemberitahuan privasi.',
            'accuracy_declaration.accepted' => 'Anda harus menyatakan keakuratan data.',
            'website.size' => 'Pengiriman tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'full_name' => 'nama lengkap',
            'email' => 'alamat email',
            'phone' => 'nomor WhatsApp',
            'cv' => 'CV',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->filled('website')) {
                $validator->errors()->add('website', 'Pengiriman tidak valid.');
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function candidatePayload(): array
    {
        $data = $this->safe()->except(['cv', 'privacy_consent', 'accuracy_declaration', 'website', 'answers']);
        $data['normalized_email'] = Candidate::normalizeEmail((string) $this->input('email'));

        return $data;
    }

    protected function nullableBool(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    protected function normalizeMoney(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return (int) $value;
        }

        $digits = preg_replace('/[^\d]/', '', (string) $value);

        return $digits === '' ? null : (int) $digits;
    }
}
