<?php

namespace App\Http\Requests;

use App\Models\ConsultationRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConsultationRequestStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status', ConsultationRequest::STATUS_NEW),
            'source' => $this->input('source', 'homepage'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc,dns', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30'],
            'service_name' => ['required', 'string', 'max:190'],
            'message' => ['required', 'string', 'max:2000'],
            'source' => ['required', Rule::in(['homepage', 'contact'])],
            'status' => ['required', Rule::in([
                ConsultationRequest::STATUS_NEW,
                ConsultationRequest::STATUS_CONTACTED,
                ConsultationRequest::STATUS_CLOSED,
            ])],
            'internal_notes' => ['nullable', 'string', 'max:4000'],
        ];
    }
}
