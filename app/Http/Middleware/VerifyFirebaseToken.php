<?php

/**
 * HK CRM Lead Management System
 *
 * @author    Harsshh (@Soulcynics404)
 * @github    https://github.com/Soulcynics404/crm-lead-management
 * @quote     "Breaking systems to make them secure."
 * @copyright 2026 Harsshh. All rights reserved.
 *
 * NOTICE: This code is proprietary. Do not copy, modify, or redistribute
 * without proper attribution to the original author.
 */

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\FirebaseService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyFirebaseToken
{
    public function __construct(
        protected FirebaseService $firebase
    ) {}

    /**
     * Handle an incoming request.
     *
     * Verifies the Firebase ID token from the Authorization header
     * and authenticates the user for API requests.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization token required',
            ], 401);
        }

        $payload = $this->firebase->verifyIdToken($token);

        if (!$payload) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token',
            ], 401);
        }

        $userData = $this->firebase->extractUserData($payload);

        if (!$userData['firebase_uid']) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token: missing user ID',
            ], 401);
        }

        // Find or create the user
        $user = User::updateOrCreate(
            ['firebase_uid' => $userData['firebase_uid']],
            [
                'name' => $userData['name'],
                'email' => $userData['email'],
                'avatar' => $userData['avatar'],
                'email_verified_at' => $userData['email_verified'] ? now() : null,
            ]
        );

        Auth::login($user);

        return $next($request);
    }
}
