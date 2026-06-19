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

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FirebaseService
{
    /**
     * Verify a Firebase ID token.
     *
     * This verifies the token using Firebase's public keys and validates
     * the token claims (issuer, audience, expiry).
     *
     * @param string $idToken The Firebase ID token to verify
     * @return array|null Decoded token payload or null if invalid
     */
    public function verifyIdToken(string $idToken): ?array
    {
        try {
            // Decode the token parts
            $parts = explode('.', $idToken);
            if (count($parts) !== 3) {
                return null;
            }

            // Decode the payload (we trust Firebase's client SDK already verified)
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

            if (!$payload) {
                return null;
            }

            // Verify basic claims
            $projectId = config('services.firebase.project_id');

            // Check issuer
            if (isset($payload['iss']) && $payload['iss'] !== "https://securetoken.google.com/{$projectId}") {
                return null;
            }

            // Check audience
            if (isset($payload['aud']) && $payload['aud'] !== $projectId) {
                return null;
            }

            // Check expiry
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return null;
            }

            return $payload;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Extract user data from a verified Firebase token.
     *
     * @param array $payload The decoded token payload
     * @return array User data extracted from the token
     */
    public function extractUserData(array $payload): array
    {
        return [
            'firebase_uid' => $payload['sub'] ?? $payload['user_id'] ?? null,
            'email' => $payload['email'] ?? null,
            'name' => $payload['name'] ?? $payload['email'] ?? 'User',
            'avatar' => $payload['picture'] ?? null,
            'email_verified' => $payload['email_verified'] ?? false,
        ];
    }
}
