<?php

namespace App\Mappers;

use App\Models\DTOs\FlightResponse;
use App\Models\Flight;

class FlightMapper
{
    public static function  toResponse(Flight $flight): FlightResponse
    {
        return new FlightResponse(
            id: $flight->id,
            departure_airport_id: $flight->departure_airport_id,
            departure_city: $flight->departureAirport?->city ?? null,
            departure_airport_name: $flight->departureAirport?->name ?? null,
            arrival_airport_id: $flight->arrival_airport_id,
            arrival_city: $flight->arrivalAirport?->city ?? null,
            arrival_airport_name: $flight->arrivalAirport?->name ?? null,
            flight_date: $flight->flight_date->toISOString(),
            arrival_date: $flight->arrival_date->toISOString(),
            aircraft: $flight->aircraft->value,
            total_seats: $flight->total_seats,
            econom_free_seats: $flight->econom_free_seats,
            business_free_seats: $flight->business_free_seats,
            booked_seats: $flight->booked_seats,
            econom_price: $flight->econom_price,
            business_price: $flight->business_price,
            flight_number: $flight->flight_number,
            created_at: $flight->created_at->toISOString(),
            updated_at: $flight->updated_at->toISOString(),
        );
    }
}

