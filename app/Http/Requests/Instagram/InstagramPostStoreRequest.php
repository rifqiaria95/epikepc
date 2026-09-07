<?php

namespace App\Http\Requests\Instagram;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstagramPostStoreRequest extends FormRequest
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
        $publishNow = $this->boolean('publish_now');
        $mediaType = strtoupper((string) $this->input('media_type', 'IMAGE'));

        $fileRules = ['required', 'file', 'max:'.(int) config('instagram.publish_max_kb', 102400)];

        if ($mediaType === 'IMAGE') {
            $fileRules[] = 'image';
            $fileRules[] = 'mimes:jpeg,jpg';
        } else {
            $fileRules[] = 'mimetypes:video/mp4,video/quicktime';
        }

        return [
            'media_type' => ['required', Rule::in(['IMAGE', 'VIDEO', 'REELS'])],
            'caption' => ['nullable', 'string', 'max:2200'],
            'media' => $fileRules,
            'publish_now' => ['sometimes', 'boolean'],
            'scheduled_at' => [
                Rule::requiredIf(! $publishNow),
                'nullable',
                'date',
                'after:'.now()->addMinute()->toDateTimeString(),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'media.mimes' => 'Instagram feed images must be JPEG.',
            'media.mimetypes' => 'Instagram videos must be MP4 or MOV.',
            'scheduled_at.after' => 'Schedule time must be at least 1 minute from now.',
        ];
    }
}
