<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Helpers\Helpers;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid email or password'], 422);
        }

        $user = Auth::user();
        $user->tokens()->delete();

        $token = $user->createToken($request->userAgent())->plainTextToken;
        $userArray = [
            'id' => Helpers::myCrypt($user->id),
            'phone' => $user->phone,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->role,
            'manager_id' => Helpers::myCrypt($user->manager_id)
        ];

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $userArray
        ], 200);
    }
}
