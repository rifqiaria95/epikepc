<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerusahaanRequest extends FormRequest
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
                'nama_perusahaan'    => 'required|string|max:255|unique:perusahaan,nama_perusahaan',
                'alamat_perusahaan'  => 'required|string',
                'no_telp_perusahaan' => 'required|string|max:15',
                'email_perusahaan'   => 'nullable|email|unique:perusahaan,email_perusahaan',
                'logo_perusahaan'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Validasi untuk update data
            $id = $this->route('id');
            return [
                'nama_perusahaan'    => 'required|string|max:255|unique:perusahaan,nama_perusahaan,' . $id,
                'alamat_perusahaan'  => 'nullable|string',
                'no_telp_perusahaan' => 'nullable|string',
                'email_perusahaan'   => 'nullable|email|unique:perusahaan,email_perusahaan,' . $id,
                'logo_perusahaan'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
        }

        // Default (jika method tidak dikenali)
        return [
            'nama_perusahaan' => 'required|string|max:255',
        ];
    }
}
