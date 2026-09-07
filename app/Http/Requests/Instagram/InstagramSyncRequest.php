<?php

namespace App\Http\Requests\Instagram;

use Illuminate\Foundation\Http\FormRequest;

class InstagramSyncRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sync_instagram') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'scope' => ['nullable', 'in:all,feed,stories'],
        ];
    }
}
