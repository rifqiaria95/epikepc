<?php

namespace App\Models\Career;

use App\Enums\Career\QuestionType;
use App\Models\Career\Concerns\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplicationAnswer extends Model
{
    use HasFactory, HasUuidPrimaryKey;

    protected $table = 'job_application_answers';

    protected $fillable = [
        'job_application_id',
        'job_vacancy_question_id',
        'question_text',
        'question_type',
        'question_options',
        'answer_text',
        'answer_json',
    ];

    protected function casts(): array
    {
        return [
            'question_type' => QuestionType::class,
            'question_options' => 'array',
            'answer_json' => 'array',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(JobVacancyQuestion::class, 'job_vacancy_question_id');
    }
}
