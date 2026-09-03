<?php

namespace App\Enums\Career;

enum EmploymentType: string
{
    case FullTime = 'FULL_TIME';
    case Contract = 'CONTRACT';
    case Internship = 'INTERNSHIP';
    case Freelance = 'FREELANCE';

    public function label(): string
    {
        return match ($this) {
            self::FullTime => 'Full Time',
            self::Contract => 'Kontrak',
            self::Internship => 'Magang',
            self::Freelance => 'Freelance',
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
