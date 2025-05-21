<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Post(
 *     path="/api/auth/signIn",
 *     summary="Authenticate a user by username or email",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"login", "password"},
 *             @OA\Property(
 *                 property="login",
 *                 type="string",
 *                 example="user@example.com",
 *                 description="Username or email"
 *             ),
 *             @OA\Property(
 *                 property="password",
 *                 type="string",
 *                 format="password",
 *                 example="password123"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User successfully authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User logged in successfully."),
 *             @OA\Property(property="data", type="integer", example=42)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Email/Username or password is invalid")
 *         )
 *     )
 * )
 */
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
