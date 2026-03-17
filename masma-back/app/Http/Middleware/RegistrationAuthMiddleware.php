<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class RegistrationAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is already authenticated
        if ($request->user()) {
            return $next($request);
        }

        $user = null;
        $tokenRefreshed = false;
        $newAccessToken = null;

        // Method 1: Check Access Token Cookie - MATCHING SanctumCookieAuth
        if ($accessToken = $request->cookie('access_token')) {
            $token = PersonalAccessToken::findToken($accessToken);
            
            if ($token) {
                if ($this->isTokenValid($token)) {
                    $user = $token->tokenable;
                    Auth::setUser($user);
                } else {
                    // Access token is expired, try to refresh it
                    $user = $this->attemptTokenRefresh($request);
                    if ($user) {
                        Auth::setUser($user);
                        $tokenRefreshed = true;
                        $newAccessToken = $user->newAccessToken;
                    }
                }
            }
        }

        // Method 2: If no access token, try refresh token directly
        if (!$user && ($refreshToken = $request->cookie('refresh_token'))) {
            $user = $this->attemptTokenRefresh($request);
            if ($user) {
                Auth::setUser($user);
                $tokenRefreshed = true;
                $newAccessToken = $user->newAccessToken;
            }
        }

        // Process the request
        $response = $next($request);

        // If token was refreshed, set the new access token cookie
        if ($tokenRefreshed && $newAccessToken) {
            $response = $this->addTokenCookie($response, $newAccessToken);
        }

        return $response;
    }

    private function isTokenValid($token): bool
    {
        if (!$token) {
            return false;
        }
        
        // Check if token has expired
        if ($token->expires_at && $token->expires_at->isPast()) {
            return false;
        }
        
        return true;
    }

    private function attemptTokenRefresh(Request $request)
    {
        try {
            $refreshToken = $request->cookie('refresh_token');
            
            if (!$refreshToken) {
                return null;
            }

            $token = PersonalAccessToken::findToken($refreshToken);
            
            if (!$token) {
                return null;
            }

            // Verify this is a refresh token
            if (!$token->can('refresh')) {
                return null;
            }

            $user = $token->tokenable;

            // Check if refresh token is expired
            if ($token->expires_at && $token->expires_at->isPast()) {
                $token->delete();
                return null;
            }

            // Check if user is active
            if (!$user->isActive()) {
                return null;
            }

            // Revoke old access tokens
            $user->tokens()->where('name', 'access_token')->delete();

            // Create new access token (2 minutes)
            $newAccessToken = $user->createToken('access_token', ['*'], now()->addMinutes(2))->plainTextToken;

            // Store the new token on the user object temporarily
            $user->newAccessToken = $newAccessToken;

            return $user;

        } catch (\Exception $e) {
            Log::error('Registration middleware token refresh failed: ' . $e->getMessage());
            return null;
        }
    }

    private function addTokenCookie($response, $token)
    {
        $isLocal = app()->environment('local');

        \Log::debug('Registration middleware setting access_token cookie', [
            'domain' => $isLocal ? 'localhost' : null,
            'secure' => $isLocal ? false : true,
            'httpOnly' => true,
            'sameSite' => $isLocal ? 'lax' : 'none'
        ]);
        
        return $response->cookie(
            'access_token',
            $token,
            2,
            '/',
            $isLocal ? 'localhost' : null,
            $isLocal ? false : true,
            true,
            false,
            $isLocal ? 'lax' : 'none'
        );
    }
}