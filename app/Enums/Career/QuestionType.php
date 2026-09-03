<?php

namespace App\Enums\Career;

enum QuestionType: string
{
    case Text = 'TEXT';
    case Textarea = 'TEXTAREA';
    case SingleChoice = 'SINGLE_CHOICE';
    case MultipleChoice = 'MULTIPLE_CHOICE';
    case Boolean = 'BOOLEAN';
    case Number = 'NUMBER';

    public function label(): string
    {
        return match ($this) {
            self::Text => 'Teks singkat',
            self::Textarea => 'Teks panjang',
            self::SingleChoice => 'Pilihan tunggal',
            self::MultipleChoice => 'Pilihan ganda',
            self::Boolean => 'Ya / Tidak',
            self::Number => 'Angka',
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
