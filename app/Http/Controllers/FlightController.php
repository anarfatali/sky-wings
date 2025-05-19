<?php

namespace App\Http\Controllers;

use App\Services\FlightService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FlightController extends BaseController
{
    private FlightService $flightService;

    public function __construct(FlightService $flightService)
    {
        $this->flightService = $flightService;
    }

    public function index(Request $request): JsonResponse
    {
        $headerValidator = Validator::make($request->header(), [
            'user-id' => 'required|exists:users,id',
        ]);
        if ($headerValidator->fails()) {
            return $this->sendError('user-id header is missing or invalid.');
        }
        $userId = (int)$request->header('user-id');

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $date = $validator->validated()['date'];

        $flights = $this->flightService->getAllByDate($date, $userId);

        return $this->sendDataResponse($flights);
    }

    public function show($flightId)
    {
        return $this->sendDataResponse($this->flightService->getById((int)$flightId));
    }

    public function search(Request $request): JsonResponse
    {
        $headerValidator = Validator::make($request->header(), [
            'user-id' => 'required|exists:users,id',
        ]);

        if ($headerValidator->fails()) {
            return $this->sendError('user-id header is missing or invalid.');
        }

        $validated = $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
            'date' => 'date_format:Y-m-d',
            'passenger_count' => 'integer|min:1|max:8',
        ]);

        $results = $this->flightService->search($validated);

        return $this->sendDataResponse($results);
    }

    public function store(Request $request): JsonResponse
    {
        $headerValidator = Validator::make($request->header(), [
            'user-id' => 'required|exists:users,id',
        ]);

        if ($headerValidator->fails()) {
            return $this->sendError('user-id header is missing or invalid.');
        }
        $userId = (int)$request->header('user-id');

        $validator = Validator::make($request->all(), [
            'departure_airport_id' => 'required|exists:airports,id',
            'arrival_airport_id' => 'required|exists:airports,id',
            'flight_date' => 'required|date',
            'arrival_date' => 'required|date|after:flight_date',
            'aircraft' => 'required|string',
            'econom_free_seats' => 'required|integer|min:0',
            'business_free_seats' => 'required|integer|min:0',
            'econom_price' => 'required|numeric|min:0',
            'business_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $data = $validator->validated();

        $flight = $this->flightService->create($userId, $data);

        return $this->sendDataResponse($flight, 'Flight created', 201);
    }

    public function update(Request $request, int $flightId): JsonResponse
    {
        $headerValidator = Validator::make($request->header(), [
            'user-id' => 'required|exists:users,id',
        ]);

        if ($headerValidator->fails()) {
            return $this->sendError('user-id header is missing or invalid.');
        }
        $userId = (int)$request->header('user-id');

        $validator = Validator::make($request->all(), [
            'departure_airport_id' => 'sometimes|required|exists:airports,id',
            'arrival_airport_id' => 'sometimes|required|exists:airports,id',
            'flight_date' => 'sometimes|required|date',
            'arrival_date' => 'sometimes|required|date|after:flight_date',
            'aircraft' => 'sometimes|required|string',
            'econom_free_seats' => 'sometimes|required|integer|min:0',
            'business_free_seats' => 'sometimes|required|integer|min:0',
            'econom_price' => 'sometimes|numeric|min:0',
            'business_price' => 'sometimes|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $data = $validator->validated();

        $this->flightService->update($userId, $flightId, $data);

        return $this->sendDataResponse('Flight updated successfully!', 204);
    }

    public function destroy(int $flightId): JsonResponse
    {
        $this->flightService->delete($flightId);

        return $this->sendResponse('Flight deleted successfully!', 204);
    }
}
