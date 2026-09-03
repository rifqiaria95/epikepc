<?php

namespace App\Enums\Career;

enum CareerTokenPurpose: string
{
    case EmailVerification = 'EMAIL_VERIFICATION';
    case StatusAccess = 'STATUS_ACCESS';
    case Withdrawal = 'WITHDRAWAL';

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
