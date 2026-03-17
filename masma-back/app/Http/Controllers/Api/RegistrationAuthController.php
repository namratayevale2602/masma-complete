<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegisteredUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class RegistrationAuthController extends Controller
{
    /**
     * Login method
     */
    public function login(Request $request): JsonResponse
    {
        try {
            Log::debug('=== REGISTRATION LOGIN ATTEMPT ===');
            
            $validator = Validator::make($request->all(), [
                'office_email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            $user = RegisteredUser::where('office_email', $request->office_email)->first();

            if (!$user) {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], Response::HTTP_UNAUTHORIZED);
            }

            if (!$user->payment_verified) {
                return response()->json([
                    'error' => 'Payment not verified. Please contact administrator.'
                ], Response::HTTP_UNAUTHORIZED);
            }

            if (!$user->credentials_sent) {
                return response()->json([
                    'error' => 'Credentials not sent. Please contact administrator.'
                ], Response::HTTP_UNAUTHORIZED);
            }

            if (!Hash::check($request->password, $user->generated_password)) {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Authenticate user
            Auth::guard('web')->login($user);
            
            // Revoke existing tokens
            $user->tokens()->delete();
            
            // Create access token (2 minutes)
            $accessToken = $user->createToken('access_token', ['*'], now()->addMinutes(2))->plainTextToken;
            
            // Create refresh token (7 days)
            $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addDays(7))->plainTextToken;

            $userData = [
                'id' => $user->id,
                'applicant_name' => $user->applicant_name,
                'office_email' => $user->office_email,
                'organization' => $user->organization,
                'mobile' => $user->mobile,
            ];

            $response = response()->json([
                'message' => 'User successfully logged in',
                'user' => $userData,
            ]);

            return $response
                ->cookie(
                    'access_token',
                    $accessToken,
                    2,
                    '/',
                    'localhost',
                    false,
                    true,
                    false,
                    'lax'
                )
                ->cookie(
                    'refresh_token',
                    $refreshToken,
                    10080,
                    '/',
                    'localhost',
                    false,
                    true,
                    false,
                    'lax'
                );

        } catch (\Exception $e) {
            Log::error('Registration login error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Logout method
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            if ($request->user()) {
                $request->user()->tokens()->delete();
            }

            $response = response()->json([
                'message' => 'User successfully logged out'
            ]);

            return $response
                ->withoutCookie('access_token')
                ->withoutCookie('refresh_token');

        } catch (\Exception $e) {
            Log::error('Registration logout error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Logout failed',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'applicant_name' => $user->applicant_name,
                    'office_email' => $user->office_email,
                    'organization' => $user->organization,
                    'mobile' => $user->mobile,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Registration get user error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch user data',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Check authentication status
     */
    public function checkAuth(Request $request): JsonResponse
    {
        try {
            Log::debug('Registration checkAuth called');
            
            $accessToken = $request->cookie('access_token');
            $refreshToken = $request->cookie('refresh_token');
            
            Log::debug('Cookies present:', [
                'access_token' => $accessToken ? 'YES' : 'NO',
                'refresh_token' => $refreshToken ? 'YES' : 'NO',
            ]);

            $user = null;
            $newAccessToken = null;

            // Check access token first
            if ($accessToken) {
                $token = PersonalAccessToken::findToken($accessToken);
                
                if ($token) {
                    if ($token->expires_at && $token->expires_at->isPast()) {
                        Log::debug('Access token expired, attempting refresh');
                        $refreshResult = $this->attemptTokenRefresh($request);
                        if ($refreshResult) {
                            list($user, $newAccessToken) = $refreshResult;
                        }
                    } else {
                        Log::debug('Access token valid');
                        $user = $token->tokenable;
                    }
                }
            }
            
            // If no user, try direct refresh
            if (!$user && $refreshToken) {
                Log::debug('Trying direct refresh token');
                $refreshResult = $this->attemptTokenRefresh($request);
                if ($refreshResult) {
                    list($user, $newAccessToken) = $refreshResult;
                }
            }

            if ($user) {
                Auth::setUser($user);
                
                if (!$user->isActive()) {
                    return response()->json([
                        'authenticated' => false,
                        'message' => 'Account is not active'
                    ]);
                }
                
                if ($newAccessToken) {
                    Log::debug('New access token generated during checkAuth');
                    
                    $response = response()->json([
                        'authenticated' => true,
                        'user' => [
                            'id' => $user->id,
                            'applicant_name' => $user->applicant_name,
                            'office_email' => $user->office_email,
                            'organization' => $user->organization,
                            'mobile' => $user->mobile,
                        ],
                        'token_refreshed' => true
                    ]);
                    
                    return $response->cookie(
                        'access_token',
                        $newAccessToken,
                        2,
                        '/',
                        'localhost',
                        false,
                        true,
                        false,
                        'lax'
                    );
                }
                
                return response()->json([
                    'authenticated' => true,
                    'user' => [
                        'id' => $user->id,
                        'applicant_name' => $user->applicant_name,
                        'office_email' => $user->office_email,
                        'organization' => $user->organization,
                        'mobile' => $user->mobile,
                    ]
                ]);
            }

            return response()->json([
                'authenticated' => false,
                'message' => 'No valid authentication found'
            ]);

        } catch (\Exception $e) {
            Log::error('Registration checkAuth error: ' . $e->getMessage());
            return response()->json([
                'authenticated' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Refresh token endpoint
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            Log::debug('Registration refresh endpoint called');
            
            $refreshToken = $request->cookie('refresh_token');
            
            if (!$refreshToken) {
                return response()->json([
                    'error' => 'No refresh token found'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $token = PersonalAccessToken::findToken($refreshToken);
            
            if (!$token) {
                return response()->json([
                    'error' => 'Invalid refresh token'
                ], Response::HTTP_UNAUTHORIZED);
            }

            if (!$token->can('refresh')) {
                return response()->json([
                    'error' => 'Token cannot be used for refresh'
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($token->expires_at && $token->expires_at->isPast()) {
                $token->delete();
                return response()->json([
                    'error' => 'Refresh token expired'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user = $token->tokenable;

            if (!$user->isActive()) {
                return response()->json([
                    'error' => 'User account is not active'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user->tokens()->where('name', 'access_token')->delete();

            $newAccessToken = $user->createToken('access_token', ['*'], now()->addMinutes(2))->plainTextToken;

            $response = response()->json([
                'message' => 'Token refreshed successfully'
            ]);

            return $response->cookie(
                'access_token',
                $newAccessToken,
                2,
                '/',
                'localhost',
                false,
                true,
                false,
                'lax'
            );

        } catch (\Exception $e) {
            Log::error('Registration refresh error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Token refresh failed',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'User not authenticated'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!Hash::check($request->current_password, $user->generated_password)) {
            return response()->json([
                'error' => 'Current password is incorrect'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->update([
            'generated_password' => Hash::make($request->new_password)
        ]);

        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password changed successfully. Please login again.'
        ]);
    }

    /**
     * Get CSRF token
     */
    public function getCsrfToken(Request $request): JsonResponse
    {
        return response()->json([
            'csrf_token' => csrf_token(),
            'message' => 'CSRF token generated successfully'
        ]);
    }

    /**
     * Private helper methods
     */
    private function attemptTokenRefresh(Request $request): ?array
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

            if (!$user->isActive()) {
                return null;
            }

            $user->tokens()->where('name', 'access_token')->delete();

            $newAccessToken = $user->createToken('access_token', ['*'], now()->addMinutes(2))->plainTextToken;

            return [$user, $newAccessToken];

        } catch (\Exception $e) {
            Log::error('Token refresh failed: ' . $e->getMessage());
            return null;
        }
    }
}