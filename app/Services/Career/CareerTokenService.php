<?php

namespace App\Services\Career;

use App\Enums\Career\CareerTokenPurpose;
use App\Exceptions\Career\TokenException;
use App\Models\Career\CareerAccessToken;
use App\Models\Career\JobApplication;
use Illuminate\Support\Str;

class CareerTokenService
{
    public function issue(JobApplication $application, CareerTokenPurpose $purpose, ?string $ip = null): array
    {
        $plaintext = Str::random(64);
        $ttl = match ($purpose) {
            CareerTokenPurpose::EmailVerification => now()->addHours((int) config('career.tokens.verification_ttl_hours')),
            CareerTokenPurpose::StatusAccess => now()->addDays((int) config('career.tokens.status_access_ttl_days')),
            CareerTokenPurpose::Withdrawal => now()->addHours((int) config('career.tokens.withdrawal_ttl_hours')),
        };

        // Rotate prior active tokens of same purpose
        CareerAccessToken::query()
            ->where('job_application_id', $application->id)
            ->where('purpose', $purpose->value)
            ->whereNull('revoked_at')
            ->whereNull('consumed_at')
            ->update(['revoked_at' => now()]);

        $token = CareerAccessToken::query()->create([
            'job_application_id' => $application->id,
            'purpose' => $purpose,
            'token_hash' => hash('sha256', $plaintext),
            'expires_at' => $ttl,
            'created_ip' => $ip,
        ]);

        return [
            'token' => $token,
            'plaintext' => $plaintext,
        ];
    }

    public function findValid(string $plaintext, CareerTokenPurpose $purpose): CareerAccessToken
    {
        $hash = hash('sha256', $plaintext);

        $token = CareerAccessToken::query()
            ->where('purpose', $purpose->value)
            ->where('token_hash', $hash)
            ->first();

        if (! $token || ! hash_equals($token->token_hash, $hash)) {
            throw new TokenException('Tautan tidak valid atau sudah tidak berlaku.');
        }

        if ($token->revoked_at !== null) {
            throw new TokenException('Tautan sudah dicabut. Silakan minta tautan baru.');
        }

        if ($token->expires_at->isPast()) {
            throw new TokenException('Tautan sudah kedaluwarsa. Silakan minta tautan baru.');
        }

        if ($purpose === CareerTokenPurpose::EmailVerification && $token->consumed_at !== null) {
            throw new TokenException('Tautan verifikasi sudah digunakan.');
        }

        return $token;
    }

    public function consume(CareerAccessToken $token): void
    {
        $token->forceFill([
            'consumed_at' => now(),
            'use_count' => $token->use_count + 1,
        ])->save();
    }

    public function touch(CareerAccessToken $token): void
    {
        $token->forceFill([
            'use_count' => $token->use_count + 1,
        ])->save();
    }

    public function revoke(CareerAccessToken $token): void
    {
        $token->forceFill(['revoked_at' => now()])->save();
    }
}
