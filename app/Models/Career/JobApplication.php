<?php

namespace App\Models\Career;

use App\Enums\Career\ApplicationStatus;
use App\Enums\Career\AvailabilityType;
use App\Enums\Career\EmailVerificationStatus;
use App\Enums\Career\ReferralSource;
use App\Models\Career\Concerns\HasUuidPrimaryKey;
use App\Models\User;
use Database\Factories\Career\JobApplicationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use HasFactory, HasUuidPrimaryKey, SoftDeletes;

    protected static function newFactory(): JobApplicationFactory
    {
        return JobApplicationFactory::new();
    }

    protected $table = 'job_applications';

    protected $fillable = [
        'reference_number',
        'job_vacancy_id',
        'candidate_id',
        'status',
        'email_verification_status',
        'cover_letter',
        'latest_salary_amount',
        'expected_salary_amount',
        'salary_currency',
        'availability_type',
        'available_from',
        'willing_to_relocate',
        'willing_to_travel_to_site',
        'referral_source',
        'referral_detail',
        'assigned_recruiter_id',
        'consent_version',
        'consent_at',
        'accuracy_declared',
        'question_snapshot',
        'submitted_at',
        'verified_at',
        'withdrawn_at',
        'hired_at',
        'rejected_at',
    ];

    protected $hidden = [
        // Never expose internal fields via accidental serialization to public
    ];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'email_verification_status' => EmailVerificationStatus::class,
            'availability_type' => AvailabilityType::class,
            'referral_source' => ReferralSource::class,
            'latest_salary_amount' => 'integer',
            'expected_salary_amount' => 'integer',
            'available_from' => 'date',
            'willing_to_relocate' => 'boolean',
            'willing_to_travel_to_site' => 'boolean',
            'accuracy_declared' => 'boolean',
            'question_snapshot' => 'array',
            'consent_at' => 'datetime',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'withdrawn_at' => 'datetime',
            'hired_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class, 'job_vacancy_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function assignedRecruiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_recruiter_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(JobApplicationAnswer::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(JobApplicationDocument::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(JobApplicationStatusHistory::class)->orderBy('created_at');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(JobApplicationNote::class)->orderByDesc('is_pinned')->orderByDesc('created_at');
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(CareerAccessToken::class);
    }

    public function scopeReadyForScreening(Builder $query): Builder
    {
        return $query
            ->where('email_verification_status', EmailVerificationStatus::Verified->value)
            ->where('status', '!=', ApplicationStatus::PendingVerification->value);
    }

    public function scopeForCmsListing(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'reference_number',
                'job_vacancy_id',
                'candidate_id',
                'status',
                'email_verification_status',
                'assigned_recruiter_id',
                'submitted_at',
                'verified_at',
                'created_at',
            ])
            ->with([
                'vacancy:id,title,code,department',
                'candidate:id,full_name,email,phone,highest_education,total_experience_years',
                'assignedRecruiter:id,name',
            ]);
    }

    public function toPublicStatusPayload(): array
    {
        $latestPublic = $this->statusHistories()
            ->whereNotNull('public_message')
            ->orderByDesc('created_at')
            ->value('public_message');

        return [
            'reference_number' => $this->reference_number,
            'vacancy_title' => $this->vacancy?->title,
            'submitted_at' => optional($this->submitted_at)?->toIso8601String(),
            'public_status' => $this->status->publicLabel(),
            'public_status_code' => $this->status->value,
            'public_message' => $latestPublic,
        ];
    }
}
