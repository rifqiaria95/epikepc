<?php

namespace App\Http\Requests\Career;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignRecruiterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('assign_applications');
    }

    public function rules(): array
    {
        return [
            'assigned_recruiter_id' => ['nullable', Rule::exists('users', 'id')],
        ];
    }
}
