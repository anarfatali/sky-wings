<?php

namespace App\Models\DTOs;

use App\Models\enums\City;

class FlightResponse
{
    public function __construct(
        public int    $id,
        public int $departure_airport_id,
        public City $departure_city,
        public string $departure_airport_name,
        public int $arrival_airport_id,
        public City $arrival_city,
        public string $arrival_airport_name,
        public string $flight_date,
        public string $arrival_date,
        public string $aircraft,
        public int    $total_seats,
        public int    $econom_free_seats,
        public int    $business_free_seats,
        public ?string $booked_seats,
        public string $econom_price,
        public string $business_price,
        public string $flight_number,
        public string $created_at,
        public string $updated_at,
    )
    {
    }
}

