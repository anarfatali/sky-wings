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
            'User-Id' => 'required|exists:users,id',
        ]);
        if ($headerValidator->fails()) {
            return $this->sendError('User-Id header is missing or invalid.');
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

    public function show($flightId): JsonResponse
    {
        return $this->sendDataResponse($this->flightService->getById($flightId));
    }

    public function search(Request $request): JsonResponse
    {
        $headerValidator = Validator::make($request->header(), [
            'User-Id' => 'required|exists:users,id',
        ]);

        if ($headerValidator->fails()) {
            return $this->sendError('User-Id header is missing or invalid.');
        }

        $validator = Validator::make($request->all(), [
            'from' => 'nullable|string',
            'to' => 'nullable|string',
            'date' => 'nullable|date_format:Y-m-d',
            'passenger_count' => 'nullable|integer|min:1',
            'isBusiness' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $filter = $validator->validated();
        $isBusiness = $filter['isBusiness'] ?? false;

        $results = $this->flightService->search($filter, $isBusiness);

        return $this->sendDataResponse($results);
    }

    public function store(Request $request): JsonResponse
    {
        $headerValidator = Validator::make($request->header(), [
            'User-Id' => 'required|exists:users,id',
        ]);

        if ($headerValidator->fails()) {
            return $this->sendError('User-Id header is missing or invalid.');
        }
        $userId = (int)$request->header('user-id');

        $validator = Validator::make($request->all(), [
            'from' => 'required|string',
            'to' => 'required|string',
            'flight_date' => 'required|date',
            'arrival_date' => 'required|date|after:flight_date',
            'aircraft' => 'required|string',
            'total_seats' => 'required|integer|min:1',
            'econom_price' => 'nullable|numeric|min:0',
            'business_price' => 'nullable|numeric|min:0',
            'flight_number' => 'required|string',
            'airport_id' => 'required|exists:airports,id',
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
            'User-Id' => 'required|exists:users,id',
        ]);

        if ($headerValidator->fails()) {
            return $this->sendError('User-Id header is missing or invalid.');
        }
        $userId = (int)$request->header('user-id');

        $validator = Validator::make($request->all(), [
            'from' => 'sometimes|required|string',
            'to' => 'sometimes|required|string',
            'flight_date' => 'sometimes|required|date',
            'arrival_date' => 'sometimes|required|date|after:flight_date',
            'aircraft' => 'sometimes|required|string',
            'total_seats' => 'sometimes|required|integer|min:1',
            'econom_price' => 'nullable|numeric|min:0',
            'business_price' => 'nullable|numeric|min:0',
            'flight_number' => 'sometimes|required|string',
            'airport_id' => 'sometimes|required|exists:airports,id',
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
