<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceTypeRequest extends FormRequest
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
                'name' => 'required|string|max:255|unique:service_type,name',
                'type' => 'required|in:it,design',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Validasi untuk update data
            $id = $this->route('id');
            return [
                'name' => 'required|string|max:255|unique:service_type,name,' . $id,
                'type' => 'required|in:it,design',
            ];
        }

        // Default (jika method tidak dikenali)
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:it,design',
        ];
    }
}
