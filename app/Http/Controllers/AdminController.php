<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends BaseController
{
    private AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        return $this->sendDataResponse(
            $this->adminService->create($validated),
            "Admin successfully created",
            201
        );
    }

    public function getAdmins(): JsonResponse
    {
        return $this->sendDataResponse($this->adminService->getAdmins());
    }

    public function getAdmin(int $id): JsonResponse{
        return $this->sendDataResponse($this->adminService->getAdmin($id));
    }

    public function signIn(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'login' => 'required|string', // login can be either email or username
            'password' => 'required|string|min:6',
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginField, $credentials['login'])
            ->where('is_admin', true)
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return $this->sendError("Email/Username or password is invalid", 401);
        }

        return $this->sendDataResponse($user->id, "Admin logged in successfully.");
    }
}
