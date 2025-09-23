<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Education;

class EducationController extends Controller
{
    public function index()
    {
        // Optimasi: Cache API response untuk education
        $education = \Cache::remember('api_education_data', 1800, function() {
            return Education::select(['id', 'title', 'subtitle', 'institution', 'year', 'description', 'created_by', 'created_at'])
                ->with(['createdBy:id,name'])
                ->orderBy('year', 'desc')
                ->get();
        });

        return response()->json($education);
    }
}
