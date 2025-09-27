<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuDetail;
use App\Models\MenuGroup;
use App\Models\Analytics;

class DashboardController extends Controller
{
    public function index()
    {
        // Get analytics data
        $analyticsData = Analytics::getDashboardData();
        
        return view('dashboard', compact('analyticsData'));
    }

    public function extDashboard()
    {
        return view('ext-dashboard');
    }

    /**
     * Get analytics data for API
     */
    public function getAnalytics()
    {
        $analyticsData = Analytics::getDashboardData();
        
        return response()->json([
            'success' => true,
            'data' => $analyticsData
        ]);
    }

    /**
     * Track analytics event
     */
    public function trackAnalytics(Request $request)
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
}
