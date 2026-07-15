<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConsultationSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
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
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'email' => 'alamat email',
            'phone' => 'nomor telepon',
            'service_name' => 'layanan',
            'message' => 'pesan',
            'source' => 'sumber formulir',
        ];
    }
}
