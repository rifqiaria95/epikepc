<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestimoniRequest extends FormRequest
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
        $rules = [
            'nama'       => 'required|string|max:255',
            'testimoni'  => 'required|string|max:500',
            'instansi'   => 'required|string|max:255',
        ];

        // Jika ini adalah request untuk create (tidak ada ID), gambar wajib diisi
        if (!$this->route('id')) {
            $rules['gambar'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        } else {
            // Jika ini adalah request untuk update, gambar opsional
            $rules['gambar'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        return $rules;
    }
}
