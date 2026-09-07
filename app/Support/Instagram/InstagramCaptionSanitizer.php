<?php

namespace App\Support\Instagram;

final class InstagramCaptionSanitizer
{
    public static function text(?string $caption, int $limit = 280): string
    {
        $clean = html_entity_decode(strip_tags((string) $caption), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $clean = preg_replace('/\s+/u', ' ', $clean) ?? '';
        $clean = trim($clean);

        if ($clean === '') {
            return '';
        }

        return mb_strlen($clean) > $limit
            ? rtrim(mb_substr($clean, 0, $limit - 1)).'…'
            : $clean;
    }

    public static function alt(?string $caption, string $fallback = 'Instagram post from EPIK EPC'): string
    {
        $text = self::text($caption, 120);

        return $text !== '' ? $text : $fallback;
    }
}
