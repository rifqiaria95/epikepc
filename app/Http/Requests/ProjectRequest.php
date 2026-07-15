<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'             => 'required|string|max:255',
            'excerpt'           => 'nullable|string|max:1000',
            'content'           => 'nullable|string',
            'content_secondary' => 'nullable|string',
            'challenge_solution'=> 'nullable|string',
            'final_result'      => 'nullable|string',
            'client'            => 'nullable|string|max:255',
            'category'          => 'nullable|string|max:255',
            'project_date'      => 'nullable|date',
            'website_url'       => 'nullable|url|max:500',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_secondary'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_tertiary'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_published'      => 'nullable|boolean',
            'sort_order'        => 'nullable|integer|min:0|max:9999',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'Project title is required.',
            'title.max'         => 'Title project maksimal 255 karakter.',
            'image.image'       => 'File harus berupa gambar.',
            'image.mimes'       => 'Format gambar yang diizinkan: jpg, jpeg, png, webp.',
            'image.max'         => 'Ukuran gambar maksimal 5 MB.',
            'image_secondary.image'  => 'File harus berupa gambar.',
            'image_secondary.mimes'  => 'Format gambar yang diizinkan: jpg, jpeg, png, webp.',
            'image_secondary.max'    => 'Ukuran gambar maksimal 5 MB.',
            'image_tertiary.image'   => 'File harus berupa gambar.',
            'image_tertiary.mimes'   => 'Format gambar yang diizinkan: jpg, jpeg, png, webp.',
            'image_tertiary.max'     => 'Ukuran gambar maksimal 5 MB.',
            'website_url.url'        => 'URL website tidak valid.',
            'project_date.date'      => 'Format tanggal tidak valid.',
            'sort_order.integer'     => 'Sort order harus berupa angka.',
            'sort_order.min'         => 'Sort order minimal 0.',
            'sort_order.max'         => 'Sort order maksimal 9999.',
        ];
    }
}
