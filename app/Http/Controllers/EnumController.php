<?php

namespace App\Http\Controllers;

use App\Models\enums\Aircraft;
use App\Models\enums\City;
use Illuminate\Http\JsonResponse;

class EnumController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/enum/aircrafts",
     *     summary="Get list of aircraft types",
     *     tags={"Enum"},
     *     @OA\Response(
     *         response=200,
     *         description="List of aircrafts",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string", example="Airbus A320")
     *         )
     *     )
     * )
     */
    public function aircrafts(): JsonResponse
    {
        $values = array_map(fn($case) => $case->value, Aircraft::cases());
        return $this->sendDataResponse($values, 'Aircraft list');
    }

    /**
     * @OA\Get(
     *     path="/api/enum/cities",
     *     summary="Get list of cities",
     *     tags={"Enum"},
     *     @OA\Response(
     *         response=200,
     *         description="List of cities",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string", example="Baku")
     *         )
     *     )
     * )
     */
    public function cities(): JsonResponse
    {
        $values = array_map(fn($case) => $case->value, City::cases());
        return $this->sendDataResponse($values, 'City list');
    }
}
