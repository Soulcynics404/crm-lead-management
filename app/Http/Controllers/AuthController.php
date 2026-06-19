<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle Firebase Google Sign-In.
     * Frontend sends Firebase ID token, we verify and create/login user.
     */
    public function handleFirebaseLogin(Request $request)
    {
        $request->validate([
            'firebase_uid' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'avatar' => 'nullable|string',
        ]);

        // Find or create user based on Firebase UID
        $user = User::updateOrCreate(
            ['firebase_uid' => $request->firebase_uid],
            [
                'name' => $request->name,
                'email' => $request->email,
                'avatar' => $request->avatar,
                'email_verified_at' => now(),
            ]
        );

        Auth::login($user, true);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'redirect' => route('dashboard'),
        ]);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
