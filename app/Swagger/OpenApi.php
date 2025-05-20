<?php

/**
 * @OA\Info(
 *     title="Sky Wings API",
 *     version="1.0.0",
 *     description="API documentation for Sky Wings system"
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="Local API Server"
 * )
 */

/**
 * @OA\Schema(
 *     schema="Flight",
 *     type="object",
 *     title="Flight",
 *     required={"id", "flight_date", "arrival_date"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="departure_airport_id", type="integer"),
 *     @OA\Property(property="arrival_airport_id", type="integer"),
 *     @OA\Property(property="flight_date", type="string", format="date-time"),
 *     @OA\Property(property="arrival_date", type="string", format="date-time"),
 *     @OA\Property(property="aircraft", type="string"),
 *     @OA\Property(property="econom_free_seats", type="integer"),
 *     @OA\Property(property="business_free_seats", type="integer"),
 *     @OA\Property(property="econom_price", type="number", format="float"),
 *     @OA\Property(property="business_price", type="number", format="float")
 * )
 */

