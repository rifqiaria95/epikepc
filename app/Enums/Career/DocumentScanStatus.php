<?php

namespace App\Enums\Career;

enum DocumentScanStatus: string
{
    case Pending = 'PENDING';
    case Clean = 'CLEAN';
    case Rejected = 'REJECTED';
    case Failed = 'FAILED';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu pemindaian',
            self::Clean => 'Bersih',
            self::Rejected => 'Ditolak scanner',
            self::Failed => 'Pemindaian gagal',
        };
    }

    public function isTrustedForDownload(): bool
    {
        // Only CLEAN is trusted. PENDING must not be treated as trusted.
        return $this === self::Clean;
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
