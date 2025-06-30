<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $token = JWTAuth::fromUser($user);

        return response()->json(['message' => 'User registered successfully',
                    'access_token'   => $token,
                    'user' => $user]);
    }

        public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
            'user_type'    => $user->user_type, 
            'user'         => $user
        ]);
    }
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
    public function changePassword(Request $request): JsonResponse
    {
        // 1. Validation des champs
        $request->validate([
            'old_password'          => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
            // 'password_confirmation' est implicite grâce à « confirmed »
        ]);

        // 2. Récupération de l'utilisateur authentifié
        $user = auth('api')->user();

        // 3. Vérification de l'ancien mot de passe
        if (! Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The old password does not match.'
            ], 400);
        }

        // 4. Mise à jour du mot de passe
        $user->password = Hash::make($request->password);
        $user->save();

        // 5. Réponse réussie
        return response()->json([
            'success' => true,
            'message' => '“Password updated successfully.'
        ]);
    }
}
