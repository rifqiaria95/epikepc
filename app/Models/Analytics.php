<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'source',
        'page',
        'user_agent',
        'ip_address',
        'referrer',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get analytics data for dashboard
     */
    public static function getDashboardData()
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Sessions (unique visitors per day)
        $sessionsToday = self::where('type', 'session')
            ->whereDate('created_at', $today)
            ->distinct('ip_address')
            ->count();

        $sessionsYesterday = self::where('type', 'session')
            ->whereDate('created_at', $yesterday)
            ->distinct('ip_address')
            ->count();

        // Page Views
        $pageViewsToday = self::where('type', 'pageview')
            ->whereDate('created_at', $today)
            ->count();

        $pageViewsYesterday = self::where('type', 'pageview')
            ->whereDate('created_at', $yesterday)
            ->count();

        // Leads (form submissions, registrations, etc.)
        $leadsToday = self::where('type', 'lead')
            ->whereDate('created_at', $today)
            ->count();

        $leadsYesterday = self::where('type', 'lead')
            ->whereDate('created_at', $yesterday)
            ->count();

        // Conversions (purchases, signups, etc.)
        $conversionsToday = self::where('type', 'conversion')
            ->whereDate('created_at', $today)
            ->count();

        $conversionsYesterday = self::where('type', 'conversion')
            ->whereDate('created_at', $yesterday)
            ->count();

        // Calculate conversion rate
        $conversionRate = $sessionsToday > 0 ? round(($conversionsToday / $sessionsToday) * 100, 1) : 0;

        return [
            'sessions' => [
                'today' => $sessionsToday,
                'yesterday' => $sessionsYesterday,
                'change' => $sessionsYesterday > 0 ? round((($sessionsToday - $sessionsYesterday) / $sessionsYesterday) * 100, 1) : 0
            ],
            'page_views' => [
                'today' => $pageViewsToday,
                'yesterday' => $pageViewsYesterday,
                'change' => $pageViewsYesterday > 0 ? round((($pageViewsToday - $pageViewsYesterday) / $pageViewsYesterday) * 100, 1) : 0
            ],
            'leads' => [
                'today' => $leadsToday,
                'yesterday' => $leadsYesterday,
                'change' => $leadsYesterday > 0 ? round((($leadsToday - $leadsYesterday) / $leadsYesterday) * 100, 1) : 0
            ],
            'conversions' => [
                'today' => $conversionsToday,
                'yesterday' => $conversionsYesterday,
                'change' => $conversionsYesterday > 0 ? round((($conversionsToday - $conversionsYesterday) / $conversionsYesterday) * 100, 1) : 0
            ],
            'conversion_rate' => $conversionRate
        ];
    }

    /**
     * Track analytics event
     */
    public static function track($type, $source = 'frontend', $page = null, $metadata = [])
    {
        return self::create([
            'type' => $type,
            'source' => $source,
            'page' => $page,
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'referrer' => request()->header('referer'),
            'metadata' => $metadata,
        ]);
    }
}
