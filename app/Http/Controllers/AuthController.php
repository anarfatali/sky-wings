<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function signIn(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'login' => 'required|string', // login can be either email or username
            'password' => 'required|string|min:6',
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginField, $credentials['login'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return $this->sendError("Email/Username or password is invalid", 401);
        }

        return $this->sendDataResponse($user->id, "User logged in successfully.");
    }
}
