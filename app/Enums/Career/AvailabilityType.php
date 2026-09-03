<?php

namespace App\Enums\Career;

enum AvailabilityType: string
{
    case Immediately = 'IMMEDIATELY';
    case OneMonthNotice = 'ONE_MONTH_NOTICE';
    case TwoMonthNotice = 'TWO_MONTH_NOTICE';
    case Custom = 'CUSTOM';

    public function label(): string
    {
        return match ($this) {
            self::Immediately => 'Segera',
            self::OneMonthNotice => 'Notice 1 bulan',
            self::TwoMonthNotice => 'Notice 2 bulan',
            self::Custom => 'Tanggal khusus',
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
