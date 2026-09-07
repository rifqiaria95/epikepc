<?php

namespace App\Http\Requests\Instagram;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstagramHighlightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $creating = $this->isMethod('post');

        return [
            'title' => ['required', 'string', 'max:80'],
            'cover_url' => [
                Rule::requiredIf(fn () => $creating && ! $this->hasFile('cover')),
                'nullable',
                'string',
                'max:2048',
            ],
            'permalink' => ['required', 'url', 'max:2048', 'regex:/^https:\/\/(www\.)?instagram\.com\//i'],
            'is_visible' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:999'],
            'cover' => [
                Rule::requiredIf(fn () => $creating && ! filled($this->input('cover_url'))),
                'nullable',
                'image',
                'max:4096',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'permalink.regex' => 'Permalink must be an Instagram URL.',
            'cover_url.required' => 'Provide a cover image URL or upload a cover image.',
            'cover.required' => 'Provide a cover image URL or upload a cover image.',
        ];
    }
}
