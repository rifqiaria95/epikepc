<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
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
            'nm_item'        => 'required|string|max:255',
            'id_kategori'    => 'required',
            'id_unit_berat'  => 'required',
            'deskripsi'      => 'nullable|string|max:255',
            'foto_item'      => 'nullable|mimes:jpg,png|max:2048',
        ];
    }
}
