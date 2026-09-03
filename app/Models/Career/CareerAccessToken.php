<?php

namespace App\Models\Career;

use App\Enums\Career\CareerTokenPurpose;
use App\Models\Career\Concerns\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerAccessToken extends Model
{
    use HasUuidPrimaryKey;

    protected $table = 'career_access_tokens';

    protected $fillable = [
        'job_application_id',
        'purpose',
        'token_hash',
        'expires_at',
        'consumed_at',
        'revoked_at',
        'use_count',
        'created_ip',
    ];

    protected $hidden = [
        'token_hash',
    ];

    protected function casts(): array
    {
        return [
            'purpose' => CareerTokenPurpose::class,
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
            'revoked_at' => 'datetime',
            'use_count' => 'integer',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function isUsable(): bool
    {
        if ($this->revoked_at !== null) {
            return false;
        }

        if ($this->expires_at->isPast()) {
            return false;
        }

        if ($this->purpose === CareerTokenPurpose::EmailVerification && $this->consumed_at !== null) {
            return false;
        }

        return true;
    }
}
