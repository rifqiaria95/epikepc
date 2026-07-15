<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{
    public function __invoke(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Cek hash valid
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        // Cek apakah sudah verifikasi
        if ($user->hasVerifiedEmail()) {
            return redirect('/login')->with('status', 'Your email has already been verified.');
        }

        // Verifikasi email
        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect('/login')->with('status', 'Email verified successfully. Please log in.');
    }
}
