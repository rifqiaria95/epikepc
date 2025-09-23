<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JenisProgramRequest extends FormRequest
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
                'nama_jenis_program' => 'required|string|max:255|unique:jenis_program,nama_jenis_program',
                'deskripsi_jenis_program'     => 'nullable|string',
                'status' => 'required|in:true,false',
                'gambar_jenis_program' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Validasi untuk update data
            $id = $this->route('id');
            return [
                'nama_jenis_program' => 'required|string|max:255|unique:jenis_program,nama_jenis_program,' . $id,
                'deskripsi_jenis_program'     => 'nullable|string',
                'status' => 'required|in:true,false',
                'gambar_jenis_program' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
        }

        // Default (jika method tidak dikenali)
        return [
            'nama_jenis_program' => 'required|string|max:255',
        ];
    }
}
