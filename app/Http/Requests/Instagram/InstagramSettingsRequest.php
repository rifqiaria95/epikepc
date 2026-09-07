<?php

namespace App\Http\Requests\Instagram;

use Illuminate\Foundation\Http\FormRequest;

class InstagramSettingsRequest extends FormRequest
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
        $maxFeed = (int) config('instagram.feed_limit', 6);

        return [
            'enabled' => ['required', 'boolean'],
            'feed_limit' => ['required', 'integer', 'min:1', 'max:'.$maxFeed],
            'eyebrow' => ['required', 'string', 'max:80'],
            'heading' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'cta_label' => ['required', 'string', 'max:80'],
            'view_more_label' => ['required', 'string', 'max:80'],
            'profile_url' => ['nullable', 'url:http,https', 'max:2048'],
        ];
    }
}
