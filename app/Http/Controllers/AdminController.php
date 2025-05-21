<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Tag(
 *     name="Admin",
 *     description="Operations related to admin users"
 * )
 */
class AdminController extends BaseController
{
    private AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * @OA\Post(
     *     path="/api/admin",
     *     summary="Create a new admin",
     *     tags={"Admins"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "email", "password", "password_confirmation"},
     *             @OA\Property(property="username", type="string", example="admin123"),
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Admin successfully created"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
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
            $this->adminService->create($validated),
            "Admin successfully created",
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/admin",
     *     summary="Get all admins",
     *     tags={"Admins"},
     *     @OA\Response(
     *         response=200,
     *         description="List of admins"
     *     )
     * )
     */
    public function getAdmins(): JsonResponse
    {
        return $this->sendDataResponse($this->adminService->getAdmins());
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{id}",
     *     summary="Get a specific admin by ID",
     *     tags={"Admins"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin details"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Admin not found"
     *     )
     * )
     */
    public function getAdmin(int $id): JsonResponse
    {
        return $this->sendDataResponse($this->adminService->getAdmin($id));
    }

    /**
     * @OA\Post(
     *     path="/api/admin/signIn",
     *     summary="Admin login",
     *     tags={"Admins"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login", "password"},
     *             @OA\Property(property="login", type="string", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin logged in successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function signIn(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'login' => 'required|string',
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
