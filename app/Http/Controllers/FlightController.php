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

    /**
     * @OA\Get(
     *     path="/api/flights",
     *     summary="Get flights by date",
     *     tags={"Flights"},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Flight")
     *         )
     *     )
     * )
     * @OA\Schema(
     *      schema="Flight",
     *      type="object",
     *      title="Flight",
     *      required={"id", "flight_date", "arrival_date"},
     *      @OA\Property(property="id", type="integer"),
     *      @OA\Property(property="departure_airport_id", type="integer"),
     *      @OA\Property(property="arrival_airport_id", type="integer"),
     *      @OA\Property(property="flight_date", type="string", format="date-time"),
     *      @OA\Property(property="arrival_date", type="string", format="date-time"),
     *      @OA\Property(property="aircraft", type="string"),
     *      @OA\Property(property="econom_free_seats", type="integer"),
     *      @OA\Property(property="business_free_seats", type="integer"),
     *      @OA\Property(property="econom_price", type="number", format="float"),
     *      @OA\Property(property="business_price", type="number", format="float")
     *  )
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $date = $validator->validated()['date'];

        $flights = $this->flightService->getAllByDate($date);

        return $this->sendDataResponse($flights);
    }

    /**
     * @OA\Get(
     *     path="/api/flights/{id}",
     *     summary="Get flight details by ID",
     *     tags={"Flights"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Flight ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Flight details",
     *         @OA\JsonContent(ref="#/components/schemas/Flight")
     *     ),
     *     @OA\Response(response=404, description="Flight not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show($flightId)
    {
        return $this->sendDataResponse($this->flightService->getById((int)$flightId));
    }

    /**
     * @OA\Post(
     *     path="/api/flights/search",
     *     summary="Search for flights",
     *     tags={"Flights"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"from","to"},
     *             @OA\Property(property="from", type="string", example="NYC"),
     *             @OA\Property(property="to", type="string", example="LAX"),
     *             @OA\Property(property="date", type="string", format="date", example="2025-05-20"),
     *             @OA\Property(property="passenger_count", type="integer", minimum=1, maximum=8, example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Flight"))
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
            'date' => 'date_format:Y-m-d',
            'passenger_count' => 'integer|min:1|max:8',
        ]);

        $results = $this->flightService->search($validated);

        return $this->sendDataResponse($results);
    }

    /**
     * @OA\Post(
     *     path="/api/flights",
     *     summary="Create a new flight",
     *     tags={"Flights"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={
     *                 "departure_airport_id",
     *                 "arrival_airport_id",
     *                 "flight_date",
     *                 "arrival_date",
     *                 "aircraft",
     *                 "econom_free_seats",
     *                 "business_free_seats",
     *                 "econom_price",
     *                 "business_price"
     *             },
     *             @OA\Property(property="departure_airport_id", type="integer", example=1),
     *             @OA\Property(property="arrival_airport_id", type="integer", example=2),
     *             @OA\Property(property="flight_date", type="string", format="date-time", example="2025-05-20T10:00:00Z"),
     *             @OA\Property(property="arrival_date", type="string", format="date-time", example="2025-05-20T12:00:00Z"),
     *             @OA\Property(property="aircraft", type="string", example="Boeing 737"),
     *             @OA\Property(property="econom_free_seats", type="integer", example=100),
     *             @OA\Property(property="business_free_seats", type="integer", example=20),
     *             @OA\Property(property="econom_price", type="number", format="float", example=150.50),
     *             @OA\Property(property="business_price", type="number", format="float", example=300.75)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Flight created",
     *         @OA\JsonContent(ref="#/components/schemas/Flight")
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(Request $request): JsonResponse
    {
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

        $flight = $this->flightService->create($data);

        return $this->sendDataResponse($flight, 'Flight created', 201);
    }

    /**
     * @OA\Put(
     *     path="/api/flights/{id}",
     *     summary="Update flight by ID",
     *     tags={"Flights"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Flight ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="departure_airport_id", type="integer", example=1),
     *             @OA\Property(property="arrival_airport_id", type="integer", example=2),
     *             @OA\Property(property="flight_date", type="string", format="date-time", example="2025-05-20T10:00:00Z"),
     *             @OA\Property(property="arrival_date", type="string", format="date-time", example="2025-05-20T12:00:00Z"),
     *             @OA\Property(property="aircraft", type="string", example="Boeing 737"),
     *             @OA\Property(property="econom_free_seats", type="integer", example=100),
     *             @OA\Property(property="business_free_seats", type="integer", example=20),
     *             @OA\Property(property="econom_price", type="number", format="float", example=150.50),
     *             @OA\Property(property="business_price", type="number", format="float", example=300.75)
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Flight updated successfully"
     * ))
     */
    public function update(Request $request, int $flightId): JsonResponse
    {
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

        $this->flightService->update($flightId, $data);

        return $this->sendDataResponse('Flight updated successfully!', 204);
    }

    /**
     * @OA\Delete(
     *     path="/api/flights/{id}",
     *     summary="Delete flight by ID",
     *     tags={"Flights"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Flight ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Flight deleted"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Flight not found")
     * )
     */
    public function destroy(int $flightId): JsonResponse
    {
        $this->flightService->delete($flightId);

        return $this->sendResponse('Flight deleted successfully!', 204);
    }
}
