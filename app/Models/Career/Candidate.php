<?php

namespace App\Models\Career;

use App\Models\Career\Concerns\HasUuidPrimaryKey;
use Database\Factories\Career\CandidateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory, HasUuidPrimaryKey;

    protected static function newFactory(): CandidateFactory
    {
        return CandidateFactory::new();
    }

    protected $table = 'candidates';

    protected $fillable = [
        'full_name',
        'email',
        'normalized_email',
        'phone',
        'normalized_phone',
        'domicile_city',
        'domicile_province',
        'highest_education',
        'education_major',
        'institution_name',
        'graduation_year',
        'total_experience_years',
        'current_or_last_company',
        'current_or_last_title',
        'linkedin_url',
        'portfolio_url',
    ];

    protected function casts(): array
    {
        return [
            'graduation_year' => 'integer',
            'total_experience_years' => 'decimal:1',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public static function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }

    public static function normalizePhone(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        return $digits;
    }
}
