<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


/**
 * @OA\Info(
 *     title="Sky Wings API",
 *     version="1.0.0",
 *     description="API documentation for Sky Wings application"
 * )
 */
class UserController extends BaseController
{

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create a new user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username","email","password","password_confirmation"},
     *             @OA\Property(property="username", type="string", example="john_doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="User successfully created")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get user by ID",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User data retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        return $this->sendDataResponse($this->userService->getById($id));
    }


    /**
     * @OA\patch(
     *     path="/api/users/password",
     *     summary="Update user password",
     *     tags={"User"},
     *     @OA\Header(
     *         header="user-id",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"old_password", "new_password", "confirm_password"},
     *             @OA\Property(property="old_password", type="string", example="oldpass123"),
     *             @OA\Property(property="new_password", type="string", example="newpass123"),
     *             @OA\Property(property="confirm_password", type="string", example="newpass123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password successfully updated"
     *     ),
     *     @OA\Response(response=400, description="Validation error")
     * )
     */

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

    /**
     * @OA\Patch(
     *     path="/api/users/email",
     *     summary="Update user's email",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *      @OA\Parameter(
     *           name="user-id",
     *           in="header",
     *           required=true,
     *           description="Authenticated user's ID",
     *           @OA\Schema(type="integer", example=1)
     *       ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="newemail@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email successfully updated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function updateEmail(Request $request): JsonResponse
    {
        $userId = (int)$request->header('user-id');
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email|max:255'
        ]);
        $this->userService->updateEmail($userId, $validated["email"]);
        return $this->sendResponse("Email successfully updated");
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/upload-photo",
     *     summary="Upload profile photo",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"profile_photo"},
     *                 @OA\Property(
     *                     property="profile_photo",
     *                     type="string",
     *                     format="binary",
     *                     description="Profile photo file"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile photo uploaded successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function uploadProfilePhoto(Request $request, $id): JsonResponse
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        return $this->userService->uploadProfilePhoto($id, $request);
    }

    /**
     * @OA\Patch(
     *     path="/api/users/{id}/delete-photo",
     *     summary="Delete profile photo",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Profile photo deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Profile photo not found"
     *     )
     * )
     */
    public function deleteProfilePhoto($id)
    {
        $this->userService->deleteProfilPhoto($id);
    }
}
