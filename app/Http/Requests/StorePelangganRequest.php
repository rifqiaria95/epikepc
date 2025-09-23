<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePelangganRequest extends FormRequest
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
    public function rules()
    {
        return [
            'nm_pelanggan'     => 'required|string|max:255',
            'alamat_pelanggan' => 'required|string',
            'no_hp_pelanggan'  => 'required|string|max:15',
            'plat_nomor'       => 'nullable|string|max:15',
            'deskripsi'        => 'nullable|string',
            'status'           => 'required|boolean',
            'id_provinsi'      => 'required|exists:indonesia_provinces,id_provinsi',
            'id_kota'          => 'required|exists:indonesia_cities,id_kota',
        ];
    }

}
