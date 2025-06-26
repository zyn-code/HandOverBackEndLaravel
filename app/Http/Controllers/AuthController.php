<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use PragmaRX\Google2FAQRCode\Google2FA;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct()
    {
        // Limit TOTP verification attempts: max 5 per minute
        $this->middleware('throttle:5,1')->only('verifyTotp');
    }

    /**
     * Verify TOTP code and issue JWT
     */
    public function verifyTotp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! $user->google2fa_secret) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication not enabled or user not found.'
            ], 403);
        }

        $google2fa = new Google2FA();
        if (! $google2fa->verifyKey($user->google2fa_secret, $request->code, 1)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid TOTP code.'
            ], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth('api')->factory()->getTTL() * 60,
                'user_type'    => $user->user_type,
                'user'         => $user,
            ],
        ]);
    }

    /**
     * Register new user and generate 2FA secret
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $google2fa  = new Google2FA();
        $secret     = $google2fa->generateSecretKey();

        $user = User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'google2fa_secret' => $secret,
        ]);

        $qrCode = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'success' => true,
            'message' => 'User registered. Scan the QR code to enable 2FA.',
            'data'    => [
                'qr_code' => $qrCode,
                // 'secret' omitted for security
            ],
        ], 201);
    }

    /**
     * Authenticate user credentials and optionally require 2FA
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        $user = auth()->user();

        if ($user->google2fa_secret) {
            // Prompt for TOTP without issuing token
            return response()->json([
                'success' => true,
                'data'    => ['2fa_required' => true],
                'message' => 'Two-factor authentication required.'
            ], 200);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth('api')->factory()->getTTL() * 60,
                'user_type'    => $user->user_type,
                'user'         => $user,
            ],
        ]);
    }

    /**
     * Invalidate JWT and logout
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            auth('api')->logout();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed, please try again.'
            ], 500);
        }
    }
}
