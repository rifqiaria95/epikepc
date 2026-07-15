<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyJourneyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'section_subtitle'        => 'required|string|max:255',
            'section_title'           => 'required|string|max:255',
            'section_title_highlight' => 'required|string|max:255',
            'section_description'     => 'nullable|string',
            'video_url'               => 'nullable|string|max:500',
            'video_poster'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'video_poster_tag'        => 'nullable|string|max:255',
            'video_poster_title'      => 'nullable|string|max:255',
            'video_established'       => 'nullable|string|max:255',
            'video_location'          => 'nullable|string|max:255',
            'video_caption'           => 'nullable|string|max:255',
            'video_duration'          => 'nullable|string|max:50',
            'timeline_subtitle'       => 'required|string|max:255',
            'timeline_title'          => 'required|string|max:255',
            'is_active'               => 'nullable|boolean',
        ];
    }
}
