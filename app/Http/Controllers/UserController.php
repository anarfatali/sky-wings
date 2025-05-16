<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseController
{

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        return $this->sendDataResponse(
            $this->userService->create($validated),
            "User successfully created",
            201
        );
    }

    public function show($id): JsonResponse
    {
        return $this->sendDataResponse($this->userService->getById($id));
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $userId = (int)$request->header('user-id');
        $validated = $request->validate([
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6'
        ]);
        $this->userService->updatePassword($userId, $validated);
        return $this->sendResponse("Password successfully updated");
    }

    public function updateEmail(Request $request): JsonResponse
    {
        $userId = (int)$request->header('user-id');
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email|max:255'
        ]);
        $this->userService->updateEmail($userId, $validated["email"]);
        return $this->sendResponse("Email successfully updated");
    }

    public function uploadProfilePhoto(Request $request, $id): JsonResponse
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        return $this->userService->uploadProfilePhoto($id, $request);
    }

    public function deleteProfilePhoto($id)
    {
        $this->userService->deleteProfilPhoto($id);
    }
}
