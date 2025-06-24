<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contractor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ContractorAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                => 'required|string|max:255',
            'email'               => 'required|string|email|max:255|unique:users',
            'password'            => 'required|string|min:6|confirmed',
            'phone_number'        => 'required|string',
            'service_categories'  => 'required|array',
            'location'            => 'required|string',
            'years_of_experience' => 'required|integer',
            'description'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create user
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'user_type' => 'contractor',
        ]);

        // Create contractor profile
        Contractor::create([
            'user_id'             => $user->id,
            'phone_number'        => $request->phone_number,
            'service_categories'  => $request->service_categories,
            'location'            => $request->location,
            'years_of_experience' => $request->years_of_experience,
            'description'         => $request->description,
        ]);

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Contractor registered successfully',
            'access_token'   => $token,
            'user'    => $user->load('contractor')
        ], 201);
    }
}

