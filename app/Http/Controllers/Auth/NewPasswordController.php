<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Debug: Log the request data
        \Log::info('Password reset attempt', [
            'email' => $request->email,
            'token_length' => strlen($request->token),
            'token_preview' => substr($request->token, 0, 20)
        ]);

        // Find the token record in database
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();
        
        if (!$tokenRecord) {
            \Log::info('No token found in database for email: ' . $request->email);
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Token reset password tidak ditemukan.']);
        }

        // Check if token is expired (60 minutes)
        $tokenAge = now()->diffInMinutes($tokenRecord->created_at);
        if ($tokenAge > 60) {
            \Log::info('Token expired for email: ' . $request->email);
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Token reset password sudah kadaluarsa.']);
        }

        // Verify the token hash
        if (!Hash::check($request->token, $tokenRecord->token)) {
            \Log::info('Invalid token for email: ' . $request->email);
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Token reset password tidak valid.']);
        }

        // Find the user
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            \Log::info('User not found for email: ' . $request->email);
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'User tidak ditemukan.']);
        }

        // Update the password
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Fire the password reset event
        event(new PasswordReset($user));

        \Log::info('Password reset successful for email: ' . $request->email);

        return redirect()->route('login')->with('status', 'Password berhasil direset! Silakan login dengan password baru Anda.');
    }
}
