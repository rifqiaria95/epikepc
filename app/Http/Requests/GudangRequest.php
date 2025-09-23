<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GudangRequest extends FormRequest
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
                'nama_gudang'    => 'required|string|max:255|unique:gudang,nama_gudang',
                'alamat_gudang'  => 'required|string',
                'no_telp_gudang' => 'required|string|max:15',
                'email_gudang'   => 'nullable|email|unique:gudang,email_gudang',
                'foto_gudang'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Validasi untuk update data
            $id = $this->route('id');
            return [
                'nama_gudang'    => 'required|string|max:255|unique:gudang,nama_gudang,' . $id,
                'alamat_gudang'  => 'nullable|string',
                'no_telp_gudang' => 'nullable|string',
                'email_gudang'   => 'nullable|email|unique:gudang,email_gudang,' . $id,
                'foto_gudang'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
        }

        // Default (jika method tidak dikenali)
        return [
            'nama_kategori' => 'required|string|max:255',
        ];
    }
}
