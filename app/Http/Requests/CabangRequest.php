<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CabangRequest extends FormRequest
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
        return [
            'nama_cabang'    => 'required|string|max:255',
            'id_perusahaan'  => 'required|exists:perusahaan,id',
            'alamat_cabang'  => 'nullable|string',
            'no_telp_cabang' => 'nullable|string|max:20',
            'email_cabang'   => 'nullable|email|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'nama_cabang.required'   => 'Nama cabang wajib diisi.',
            'nama_cabang.string'     => 'Nama cabang harus berupa teks.',
            'nama_cabang.max'        => 'Nama cabang maksimal 255 karakter.',
            'id_perusahaan.required' => 'Perusahaan wajib dipilih.',
            'id_perusahaan.exists'   => 'Perusahaan yang dipilih tidak valid.',
            'alamat_cabang.string'   => 'Alamat harus berupa teks.',
            'no_telp_cabang.string'  => 'Telepon harus berupa teks.',
            'no_telp_cabang.max'     => 'Telepon maksimal 20 karakter.',
            'email_cabang.email'     => 'Email harus berupa format email yang valid.',
            'email_cabang.max'       => 'Email maksimal 255 karakter.',
        ];
    }
} 