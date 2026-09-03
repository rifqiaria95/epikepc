<?php

namespace App\Enums\Career;

enum ApplicationStatus: string
{
    case PendingVerification = 'PENDING_VERIFICATION';
    case Submitted = 'SUBMITTED';
    case Screening = 'SCREENING';
    case Shortlisted = 'SHORTLISTED';
    case Interview = 'INTERVIEW';
    case Offered = 'OFFERED';
    case Hired = 'HIRED';
    case Rejected = 'REJECTED';
    case Withdrawn = 'WITHDRAWN';
    case Expired = 'EXPIRED';

    public function label(): string
    {
        return match ($this) {
            self::PendingVerification => 'Menunggu Verifikasi',
            self::Submitted => 'Submitted',
            self::Screening => 'Screening',
            self::Shortlisted => 'Shortlisted',
            self::Interview => 'Interview',
            self::Offered => 'Offered',
            self::Hired => 'Hired',
            self::Rejected => 'Rejected',
            self::Withdrawn => 'Withdrawn',
            self::Expired => 'Expired',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Hired,
            self::Rejected,
            self::Withdrawn,
            self::Expired,
        ], true);
    }

    public function publicLabel(): string
    {
        return config('career.public_statuses.'.$this->value, $this->label());
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
