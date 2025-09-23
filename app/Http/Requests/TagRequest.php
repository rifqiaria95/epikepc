<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    /**
     * Menentukan apakah user diizinkan melakukan request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Mendapatkan rules validasi yang berlaku untuk request.
     * Pisahkan validasi untuk insert (store) dan update.
     *
     * @return array
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            // Validasi untuk insert data baru
            return [
                'name' => 'required|string|max:255|unique:tags,name',
                'slug'     => 'nullable|string',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Validasi untuk update data
            $id = $this->route('id');
            return [
                'name' => 'required|string|max:255|unique:tags,name,' . $id,
                'slug'     => 'nullable|string',
            ];
        }

        // Default (jika method tidak dikenali)
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
