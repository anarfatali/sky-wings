<?php

namespace App\Http\Controllers;

use App\Services\FlightService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FlightController extends BaseController
{
    private FlightService $flightService;

    public function __construct(FlightService $flightService)
    {
        $this->flightService = $flightService;
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $flights = $this->flightService->getAllByDate($validated['date']);

        return $this->sendDataResponse($flights);
    }


    public function show($flightId): JsonResponse
    {
        return $this->sendDataResponse($this->flightService->getById($flightId));
    }

    public function search(Request $request): JsonResponse
    {
        $filter = $request->validated();

        return $this->sendDataResponse($this->flightService->search($filter));
    }

    public function store(Request $request): JsonResponse
    {
        $userId = (int)$request->header('User-Id');
        $payload = $request->validated();

        $flight = $this->flightService->create($userId, $payload);

        return $this->sendDataResponse($flight, 'Flight created', 201);
    }

    public function update(Request $request, int $flightId): JsonResponse
    {
        $userId = (int)$request->header('User-Id');
        $payload = $request->validated();

        $this->flightService->update($userId, $flightId, $payload);

        return $this->sendDataResponse('Flight updated successfully!', 204);
    }

    public function destroy(int $flightId): JsonResponse
    {
        $this->flightService->delete($flightId);

        return $this->sendResponse('Flight deleted successfully!', 204);
    }
}
