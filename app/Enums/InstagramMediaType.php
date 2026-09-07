<?php

namespace App\Enums;

enum InstagramMediaType: string
{
    case Image = 'IMAGE';
    case Video = 'VIDEO';
    case CarouselAlbum = 'CAROUSEL_ALBUM';
    case Reels = 'REELS';

    public function isVideo(): bool
    {
        return $this === self::Video || $this === self::Reels;
    }

    public function isCarousel(): bool
    {
        return $this === self::CarouselAlbum;
    }

    public function indicator(): ?string
    {
        return match ($this) {
            self::CarouselAlbum => 'carousel',
            self::Video, self::Reels => 'video',
            default => null,
        };
    }
}
