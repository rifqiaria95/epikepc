<?php

namespace App\Enums\Career;

enum DocumentType: string
{
    case Cv = 'CV';
    case CoverLetter = 'COVER_LETTER';
    case Portfolio = 'PORTFOLIO';
    case Certificate = 'CERTIFICATE';

    public function label(): string
    {
        return match ($this) {
            self::Cv => 'CV / Resume',
            self::CoverLetter => 'Cover Letter',
            self::Portfolio => 'Portofolio',
            self::Certificate => 'Sertifikat',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
