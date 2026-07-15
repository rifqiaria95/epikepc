<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class CompanyJourney extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'section_subtitle',
        'section_title',
        'section_title_highlight',
        'section_description',
        'video_url',
        'video_poster',
        'video_poster_tag',
        'video_poster_title',
        'video_established',
        'video_location',
        'video_caption',
        'video_duration',
        'timeline_subtitle',
        'timeline_title',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getPosterUrlAttribute(): ?string
    {
        if (! $this->video_poster) {
            return null;
        }

        return Storage::disk('public')->url($this->video_poster);
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        return self::toYoutubeEmbedUrl($this->video_url);
    }

    public static function toYoutubeEmbedUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (str_contains($url, 'youtube.com/embed/')) {
            return self::appendEmbedParams($url);
        }

        $videoId = null;

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/shorts\/)([A-Za-z0-9_-]{11})/', $url, $matches)) {
            $videoId = $matches[1];
        }

        if (! $videoId) {
            return self::appendEmbedParams($url);
        }

        return self::appendEmbedParams('https://www.youtube.com/embed/' . $videoId);
    }

    private static function appendEmbedParams(string $url): string
    {
        $separator = str_contains($url, '?') ? '&' : '?';

        if (str_contains($url, 'autoplay=')) {
            return $url;
        }

        return $url . $separator . 'autoplay=1&rel=0&modestbranding=1';
    }

    public static function defaults(): array
    {
        return [
            'section_subtitle'        => 'Our Story',
            'section_title'           => 'Company',
            'section_title_highlight' => 'Journey',
            'section_description'     => 'PT Energi Persada Inti Konstruksi (EPIK) delivers integrated EPC and O&M services for oil & gas infrastructure across Indonesia — from pipeline networks and metering stations to HDD crossings and LNG facilities.',
            'video_url'               => null,
            'video_poster_tag'        => 'Company Profile',
            'video_poster_title'      => 'PT Energi Persada Inti Konstruksi',
            'video_established'       => 'Energy Infrastructure Partner',
            'video_location'          => 'Jakarta, Indonesia',
            'video_caption'           => 'Connecting Energy, Building for the Future',
            'video_duration'          => null,
            'timeline_subtitle'       => 'Company History',
            'timeline_title'          => 'Our Milestones',
            'is_active'               => true,
        ];
    }
}
