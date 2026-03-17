<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class SanctumCookieAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            return $next($request);
        }

        $user = null;
        $tokenRefreshed = false;
        $newAccessToken = null;

        if ($accessToken = $request->cookie('access_token')) {
            $token = PersonalAccessToken::findToken($accessToken);
            
            if ($token) {
                if ($this->isTokenValid($token)) {
                    $user = $token->tokenable;
                    Auth::setUser($user);
                } else {
                    $user = $this->attemptTokenRefresh($request);
                    if ($user) {
                        Auth::setUser($user);
                        $tokenRefreshed = true;
                        $newAccessToken = $user->newAccessToken;
                    }
                }
            }
        }

        if (!$user && ($refreshToken = $request->cookie('refresh_token'))) {
            $user = $this->attemptTokenRefresh($request);
            if ($user) {
                Auth::setUser($user);
                $tokenRefreshed = true;
                $newAccessToken = $user->newAccessToken;
            }
        }

        $response = $next($request);

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

            if (!$token->can('refresh')) {
                return null;
            }

            $user = $token->tokenable;

            if ($token->expires_at && $token->expires_at->isPast()) {
                $token->delete();
                return null;
            }

            $user->tokens()->where('name', 'access_token')->delete();

            $newAccessToken = $user->createToken('access_token', ['*'], now()->addMinutes(2))->plainTextToken;

            $user->newAccessToken = $newAccessToken;

            return $user;

        } catch (\Exception $e) {
            Log::error('Token refresh failed: ' . $e->getMessage());
            return null;
        }
    }

    private function addTokenCookie($response, $token)
    {
        return $response->cookie(
            'access_token',
            $token,
            2,
            '/',
            'localhost',
            false,
            true,
            false,
            'lax'
        );
    }
}