<?php

namespace App\Http\Requests\Career;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationNoteStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create_application_notes');
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'max:4000'],
            'is_pinned' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => 'Catatan wajib diisi.',
        ];
    }
}
