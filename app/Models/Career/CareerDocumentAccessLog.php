<?php

namespace App\Models\Career;

use App\Models\Career\Concerns\HasUuidPrimaryKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerDocumentAccessLog extends Model
{
    use HasUuidPrimaryKey;

    public $timestamps = false;

    protected $table = 'career_document_access_logs';

    protected $fillable = [
        'job_application_document_id',
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(JobApplicationDocument::class, 'job_application_document_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
