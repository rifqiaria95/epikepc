<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsRequest extends FormRequest
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
            'title'        => 'required|string|max:255',
            'slug'         => 'required|string|max:255',
            'content'      => 'required|string',
            'summary'      => 'nullable|string',
            'thumbnail'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status'       => 'required|string|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'archived_at'  => 'nullable|date',
            'category_id'  => 'required|exists:kategori,id',
            'tags_id'      => 'required|exists:tags,id',
            'updated_by'   => 'nullable|exists:users,id',
            'deleted_by'   => 'nullable|exists:users,id',
            'created_by'   => 'nullable|exists:users,id',
            'path'         => 'nullable|string',
        ];
    }
}
