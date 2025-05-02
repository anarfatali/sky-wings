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

    public function store(Request $request)
    {
        $validator = Validator::make($request->header(), [
            'User-Id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return $this->sendError("User-Id is missing or invalid.");
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

    public function showMyFlights(Request $request): JsonResponse
    {
        $validator = Validator::make($request->header(), [
            'User-Id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return $this->sendError("User-Id is missing or invalid.");
        }
        $userId = (int)$request->header('user-id');
        return $this->sendDataResponse($this->bookingService->getMyFlights($userId));
    }

    public function showHistory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->header(), [
            'User-Id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return $this->sendError("User-Id is missing or invalid.");
        }
        $userId = (int)$request->header('user-id');
        return $this->sendDataResponse($this->bookingService->getHistory($userId));
    }
}
