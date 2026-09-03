<?php

namespace App\Models\Career;

use App\Enums\Career\QuestionType;
use App\Models\Career\Concerns\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobVacancyQuestion extends Model
{
    use HasFactory, HasUuidPrimaryKey;

    protected $table = 'job_vacancy_questions';

    protected $fillable = [
        'job_vacancy_id',
        'question',
        'help_text',
        'type',
        'options',
        'is_required',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => QuestionType::class,
            'options' => 'array',
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class, 'job_vacancy_id');
    }

    public function toSnapshot(): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'help_text' => $this->help_text,
            'type' => $this->type->value,
            'options' => $this->options,
            'is_required' => $this->is_required,
            'sort_order' => $this->sort_order,
        ];
    }
}
