<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\RegistrationRequest;

class RegisterController extends Controller
{
    public function register(RegistrationRequest $request)
    {
        // Get the user data to be saved
        $newUser = $request->validated();

        // Hash the password
        $newUser['password'] = Hash::make($newUser['password']);

        // Set the user role
        $newUser['role'] = config('auth.roles.user');

        // Create the new user
        $user = User::create($newUser);

        // Create a token for the new user
        $tokenName = config('auth.token_names.registration');
        $tokenAbilities = ['app:all'];
        $token = $user->createToken($tokenName, $tokenAbilities)->plainTextToken;

        // Use a custom structure for the success response JSON
        $success = [
            'status' => 'success',
            'message' => 'Registration successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->first_name,
                    'email' => $user->email
                ],
                'token' => $token,
            ],
        ];

        // Return the success response
        return response()->json($success, 200);
    }
}
