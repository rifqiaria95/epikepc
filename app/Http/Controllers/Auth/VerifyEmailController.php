<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        // Ambil user berdasarkan ID dari URL
        $user = User::findOrFail($request->route('id'));
        
        // Verifikasi hash
        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Verification link is invalid or has expired.');
        }

        // Cek apakah sudah diverifikasi
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('message', 'Email was already verified. Please sign in.');
        }

        // Verifikasi email
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            
            // Update status user menjadi aktif setelah verifikasi email
            $user->update(['active' => 1]);
            
            // Login user setelah verifikasi berhasil
            Auth::login($user);
            
            return redirect()->route('dashboard')->with('success', 'Email verified successfully! Welcome, ' . $user->name . '!');
        }

        return redirect()->route('login')->with('error', 'Failed to verify email. Please try again.');
    }
}
