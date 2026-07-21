<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        // Log login attempt
        LoginAttempt::create([
            'email' => $request->email,
            'user_id' => $user?->id,
            'success' => $user && Hash::check($request->password, $user->password),
            'attempted_at' => now(),
        ]);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact HR.',
            ], 403);
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Create token using Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user->load('department'),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        // Revoke the current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Logout from all devices
     */
    public function logoutAll(Request $request)
    {
        // Revoke all tokens
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices successfully'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()->load('department'),
        ]);
    }

    /**
     * Refresh token (create new token, delete old one)
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        
        // Delete current token
        $user->currentAccessToken()->delete();
        
        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get all active tokens for the user
     */
    public function tokens(Request $request)
    {
        $tokens = $request->user()->tokens()
            ->where('name', 'auth_token')
            ->get()
            ->map(function($token) {
                return [
                    'id' => $token->id,
                    'last_used_at' => $token->last_used_at,
                    'created_at' => $token->created_at,
                    'expires_at' => $token->expires_at,
                    'abilities' => $token->abilities,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $tokens,
        ]);
    }
}