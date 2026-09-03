<?php

namespace App\Enums\Career;

enum WorkArrangement: string
{
    case Onsite = 'ONSITE';
    case Hybrid = 'HYBRID';
    case Remote = 'REMOTE';

    public function label(): string
    {
        return match ($this) {
            self::Onsite => 'Onsite',
            self::Hybrid => 'Hybrid',
            self::Remote => 'Remote',
        };
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
