<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgramRequest extends FormRequest
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
        $userId = $this->route('id');
        
        return [
            'jenis_program_id' => 'required|exists:jenis_program,id',
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date',
            'kuota'            => 'required|integer',
            'kategori'         => 'nullable|string',
            'sumber_dana'      => 'nullable|string',
            'status'           => 'required|in:draft,open,closed',
        ];
    }
}
