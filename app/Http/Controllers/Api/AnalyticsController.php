<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Analytics;

class AnalyticsController extends Controller
{
    /**
     * Track analytics event from frontend
     */
    public function track(Request $request)
    {
        $request->validate([
            'type' => 'required|in:session,pageview,lead,conversion',
            'source' => 'nullable|string',
            'page' => 'nullable|string',
            'metadata' => 'nullable|array'
        ]);

        $analytics = Analytics::track(
            $request->type,
            $request->source ?? 'frontend',
            $request->page,
            $request->metadata ?? []
        );

        return response()->json([
            'success' => true,
            'message' => 'Analytics tracked successfully',
            'data' => $analytics
        ]);
    }

    /**
     * Get analytics data for frontend
     */
    public function getData()
    {
        $analyticsData = Analytics::getDashboardData();
        
        return response()->json([
            'success' => true,
            'data' => $analyticsData
        ]);
    }
}
