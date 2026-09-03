<?php

namespace App\Enums\Career;

enum EmailVerificationStatus: string
{
    case Pending = 'PENDING';
    case Verified = 'VERIFIED';
    case Expired = 'EXPIRED';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Verified => 'Verified',
            self::Expired => 'Expired',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
