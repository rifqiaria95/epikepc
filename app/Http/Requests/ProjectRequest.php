<?php

namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'latitude' => $this->filled('latitude') ? $this->input('latitude') : null,
            'longitude' => $this->filled('longitude') ? $this->input('longitude') : null,
            'location' => $this->filled('location') ? trim((string) $this->input('location')) : null,
            'status' => $this->input('status', ProjectStatus::Completed->value),
            'project_value' => $this->filled('project_value')
                ? (int) preg_replace('/\D+/', '', (string) $this->input('project_value'))
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'title'              => 'required|string|max:255',
            'excerpt'            => 'nullable|string|max:1000',
            'content'            => 'nullable|string',
            'content_secondary'  => 'nullable|string',
            'challenge_solution' => 'nullable|string',
            'final_result'       => 'nullable|string',
            'client'             => 'nullable|string|max:255',
            'category'           => 'nullable|string|max:255',
            'location'           => 'nullable|string|max:255',
            'latitude'           => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'          => ['nullable', 'numeric', 'between:-180,180'],
            'project_date'       => 'nullable|date',
            'project_value'      => 'nullable|integer|min:0',
            'website_url'        => 'nullable|url|max:500',
            'image'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_secondary'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_tertiary'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_published'       => 'nullable|boolean',
            'status'             => ['required', Rule::enum(ProjectStatus::class)],
            'sort_order'         => 'nullable|integer|min:0|max:9999',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $lat = $this->input('latitude');
            $lng = $this->input('longitude');

            $hasLat = filled($lat);
            $hasLng = filled($lng);

            if ($hasLat xor $hasLng) {
                $validator->errors()->add(
                    'longitude',
                    'Latitude and longitude must both be filled or both left empty.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'title.required'         => 'Project title is required.',
            'title.max'              => 'Title project maksimal 255 karakter.',
            'status.required'        => 'Project status is required.',
            'status.Illuminate\Validation\Rules\Enum' => 'Status must be On Going or Completed.',
            'latitude.between'       => 'Latitude must be between -90 and 90.',
            'longitude.between'      => 'Longitude must be between -180 and 180.',
            'image.image'            => 'File harus berupa gambar.',
            'image.mimes'            => 'Format gambar yang diizinkan: jpg, jpeg, png, webp.',
            'image.max'              => 'Ukuran gambar maksimal 5 MB.',
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
