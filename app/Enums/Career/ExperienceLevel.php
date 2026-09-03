<?php

namespace App\Enums\Career;

enum ExperienceLevel: string
{
    case FreshGraduate = 'FRESH_GRADUATE';
    case Junior = 'JUNIOR';
    case Mid = 'MID';
    case Senior = 'SENIOR';
    case Lead = 'LEAD';
    case Manager = 'MANAGER';

    public function label(): string
    {
        return match ($this) {
            self::FreshGraduate => 'Fresh Graduate',
            self::Junior => 'Junior',
            self::Mid => 'Mid Level',
            self::Senior => 'Senior',
            self::Lead => 'Lead',
            self::Manager => 'Manager',
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
