<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuDetail;
use App\Models\MenuGroup;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function extDashboard()
    {
        return view('ext-dashboard');
    }

}
