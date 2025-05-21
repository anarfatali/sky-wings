<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends BaseController
{
    private BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }
    /**
     * @OA\Post(
     *     path="/api/bookings",
     *     summary="Create a new booking",
     *     tags={"Bookings"},
     *     @OA\Parameter(
     *         name="user-id",
     *         in="header",
     *         required=true,
     *         description="Authenticated user's ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"flight_id", "isBusiness", "passengers"},
     *             @OA\Property(property="flight_id", type="integer", example=5),
     *             @OA\Property(property="isBusiness", type="boolean", example=false),
     *             @OA\Property(
     *                 property="passengers",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="name", type="string", example="John"),
     *                     @OA\Property(property="surname", type="string", example="Doe"),
     *                     @OA\Property(property="is_female", type="boolean", example=false),
     *                     @OA\Property(property="date_of_birth", type="string", format="date", example="2000-01-01"),
     *                     @OA\Property(property="seat_number", type="string", example="12A"),
     *                     @OA\Property(property="phone_number", type="string", example="+123456789")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Booking created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Booking created."),
     *             @OA\Property(property="data", type="integer", example=101)
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->header(), [
            'user-id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return $this->sendError("user-id is missing or invalid.");
        }
        $userId = (int)$request->header('user-id');

        $validated = $request->validate([
            'flight_id' => 'required|exists:flights,id',
            'isBusiness' => 'required|boolean',
            'passengers' => 'required|array|min:1',
            'passengers.*.name' => 'required|string|max:255',
            'passengers.*.surname' => 'required|string|max:255',
            'passengers.*.is_female' => 'required|boolean',
            'passengers.*.date_of_birth' => 'required|date_format:Y-m-d',
            'passengers.*.seat_number' => 'required|string|max:10',
            'passengers.*.phone_number' => 'nullable|string|max:20',
        ]);
        $createdId = $this->bookingService->create($userId, $validated);
        return $this->sendDataResponse("Booking created.", $createdId, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/bookings/my-flights",
     *     summary="Get upcoming flights for authenticated user",
     *     tags={"Bookings"},
     *     @OA\Parameter(
     *         name="user-id",
     *         in="header",
     *         required=true,
     *         description="Authenticated user's ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of upcoming flights",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    public function showMyFlights(Request $request): JsonResponse
    {
        $validator = Validator::make($request->header(), [
            'user-id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return $this->sendError("user-id is missing or invalid.");
        }
        $userId = (int)$request->header('user-id');
        return $this->sendDataResponse($this->bookingService->getMyFlights($userId));
    }

    /**
     * @OA\Get(
     *     path="/api/bookings/history",
     *     summary="Get booking history for authenticated user",
     *     tags={"Bookings"},
     *     @OA\Parameter(
     *         name="user-id",
     *         in="header",
     *         required=true,
     *         description="Authenticated user's ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking history",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    public function showHistory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->header(), [
            'user-id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return $this->sendError("user-id is missing or invalid.");
        }
        $userId = (int)$request->header('user-id');
        return $this->sendDataResponse($this->bookingService->getHistory($userId));
    }
}
