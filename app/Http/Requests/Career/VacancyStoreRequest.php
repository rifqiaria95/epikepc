<?php

namespace App\Http\Requests\Career;

use App\Enums\Career\EmploymentType;
use App\Enums\Career\ExperienceLevel;
use App\Enums\Career\QuestionType;
use App\Enums\Career\WorkArrangement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VacancyStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create_vacancies') || $this->user()?->can('edit_vacancies');
    }

    protected function prepareForValidation(): void
    {
        $questions = $this->input('questions', []);

        if (is_array($questions)) {
            foreach ($questions as $i => $row) {
                if (! empty($row['options_text']) && empty($row['options'])) {
                    $questions[$i]['options'] = array_values(array_filter(array_map('trim', explode(',', (string) $row['options_text']))));
                }
            }
            $this->merge(['questions' => $questions]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:190'],
            'department' => ['required', 'string', 'max:120'],
            'location_city' => ['required', 'string', 'max:120'],
            'location_province' => ['required', 'string', 'max:120'],
            'employment_type' => ['required', Rule::in(EmploymentType::values())],
            'work_arrangement' => ['required', Rule::in(WorkArrangement::values())],
            'experience_level' => ['required', Rule::in(ExperienceLevel::values())],
            'summary' => ['required', 'string', 'max:2000'],
            'description' => ['required', 'string'],
            'responsibilities' => ['required', 'string'],
            'qualifications' => ['required', 'string'],
            'preferred_qualifications' => ['nullable', 'string'],
            'minimum_education' => ['nullable', 'string', 'max:80'],
            'minimum_experience_years' => ['nullable', 'integer', 'min:0', 'max:40'],
            'headcount' => ['nullable', 'integer', 'min:1', 'max:99'],
            'requires_site_travel' => ['sometimes', 'boolean'],
            'allows_salary_expectation' => ['sometimes', 'boolean'],
            'closes_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'questions' => ['nullable', 'array'],
            'questions.*.id' => ['nullable', 'uuid'],
            'questions.*.question' => ['required_with:questions', 'string', 'max:255'],
            'questions.*.help_text' => ['nullable', 'string', 'max:500'],
            'questions.*.type' => ['required_with:questions', Rule::in(QuestionType::values())],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.is_required' => ['sometimes', 'boolean'],
            'questions.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul lowongan wajib diisi.',
            'department.required' => 'Departemen wajib diisi.',
            'summary.required' => 'Ringkasan wajib diisi.',
            'description.required' => 'Deskripsi wajib diisi.',
            'responsibilities.required' => 'Tanggung jawab wajib diisi.',
            'qualifications.required' => 'Kualifikasi wajib diisi.',
        ];
    }
}
