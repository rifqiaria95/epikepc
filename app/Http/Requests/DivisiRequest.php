<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DivisiRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            // Validasi untuk insert data baru
            return [
                'nama_divisi' => 'required|string|max:255|unique:divisi,nama_divisi',
                'deskripsi'     => 'nullable|string',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Validasi untuk update data
            $id = $this->route('id');
            return [
                'nama_divisi' => 'required|string|max:255|unique:divisi,nama_divisi,' . $id,
                'deskripsi'     => 'nullable|string',
            ];
        }

        // Default (jika method tidak dikenali)
        return [
            'nama_divisi' => 'required|string|max:255',
        ];
    }
}
