<?php

namespace App\Http\Requests\Career;

use App\Enums\Career\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplicationTransitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('change_application_status')
            || $this->user()?->can('reject_applications');
    }

    public function rules(): array
    {
        return [
            'to_status' => ['required', Rule::in(ApplicationStatus::values())],
            'reason_code' => ['nullable', 'string', 'max:80'],
            'public_message' => ['nullable', 'string', 'max:1000'],
            'internal_note' => ['required_if:to_status,REJECTED', 'nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_status.required' => 'Status tujuan wajib dipilih.',
            'internal_note.required_if' => 'Catatan internal wajib diisi saat menolak lamaran.',
        ];
    }
}
