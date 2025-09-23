<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgramRegistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Jika request dari external (registration-program), gunakan validasi yang berbeda
        if (request()->is('registration-program*')) {
            return [
                'program_id'    => 'required|exists:programs,id',
                'alasan'        => 'required|string|max:255',
            ];
        }
        
        // Validasi untuk internal admin
        return [
            'user_id'       => 'required|exists:users,id',
            'program_id'    => 'required|exists:programs,id',
            'alasan'        => 'required|string|max:255',
            'status'        => 'required|string|max:255',
            'notes_admin'   => 'nullable|string|max:255',
            'registered_at' => 'nullable|date',
        ];
    }
}
