<?php

namespace App\Http\Requests\Instagram;

use Illuminate\Foundation\Http\FormRequest;

class InstagramReorderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_instagram') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['integer', 'distinct', 'exists:instagram_media,id'],
        ];
    }
}
