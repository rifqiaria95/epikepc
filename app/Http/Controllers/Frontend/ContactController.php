<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('frontend.contact.index', [
            'contact' => config('frontend_contact'),
            'services' => Service::query()
                ->select(['title'])
                ->withoutTrashed()
                ->orderBy('title')
                ->pluck('title')
                ->all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        return redirect()->route('frontend.contact.index')
            ->with('success', 'Your message has been sent successfully. We will contact you shortly.');
    }
}
