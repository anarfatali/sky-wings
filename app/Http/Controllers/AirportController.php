<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Airports",
 *     description="API Endpoints for managing airports"
 * )
 */
class AirportController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/airports",
     *     summary="Create a new airport",
     *     tags={"Airports"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "city"},
     *             @OA\Property(property="name", type="string", example="Heathrow Airport"),
     *             @OA\Property(property="city", type="string", example="London")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Airport successfully added",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Airport successfully added")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'string',
            'city' => 'string'
        ]);
        Airport::query()->create($validated);
        return $this->sendResponse("Airport successfully added");
    }

    /**
     * @OA\Get(
     *     path="/api/airports",
     *     summary="Get all airports",
     *     tags={"Airports"},
     *     @OA\Response(
     *         response=200,
     *         description="List of airports",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Heathrow Airport"),
     *                 @OA\Property(property="city", type="string", example="London")
     *             )
     *         )
     *     )
     * )
     */
    public function getAll()
    {
        return $this->sendDataResponse(Airport::query()->get());
    }
}
