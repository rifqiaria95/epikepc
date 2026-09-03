<?php

namespace App\Models\Career;

use App\Enums\Career\ApplicationStatus;
use App\Models\Career\Concerns\HasUuidPrimaryKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplicationStatusHistory extends Model
{
    use HasUuidPrimaryKey;

    public $timestamps = false;

    protected $table = 'job_application_status_histories';

    protected $fillable = [
        'job_application_id',
        'from_status',
        'to_status',
        'reason_code',
        'public_message',
        'internal_note',
        'changed_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => ApplicationStatus::class,
            'to_status' => ApplicationStatus::class,
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function () {
            throw new \RuntimeException('Status history is immutable and cannot be updated.');
        });

        static::deleting(function () {
            throw new \RuntimeException('Status history is immutable and cannot be deleted.');
        });
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
