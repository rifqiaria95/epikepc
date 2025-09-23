<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DepartemenRequest extends FormRequest
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
        $uniqueRule = Rule::unique('departemen', 'nama_departemen')
        ->where(function ($query) {
            return $query->whereNull('deleted_at');
        });

        if ($this->isMethod('post')) {
            // Validasi untuk insert data baru
            return [
                'nama_departemen' => ['required', 'string', 'max:255', $uniqueRule],
                'id_divisi'       => 'required|integer',
                'deskripsi'       => 'nullable|string',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Validasi untuk update data
            $id = $this->route('id');
            return [
                'nama_departemen' => ['required', 'string', 'max:255', $uniqueRule->ignore($id)],
                'id_divisi'       => 'required|integer',
                'deskripsi'       => 'nullable|string',
            ];
        }

        // Default (jika method tidak dikenali)
        return [
            'nama_departemen' => 'required|string|max:255',
            'id_divisi' => 'required|integer',
        ];
    }
}
