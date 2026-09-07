<?php

namespace App\Http\Requests\Instagram;

use Illuminate\Foundation\Http\FormRequest;

class InstagramVisibilityRequest extends FormRequest
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
            'is_visible' => ['required', 'boolean'],
        ];
    }
}
