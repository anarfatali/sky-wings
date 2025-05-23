<?php

namespace App\Mappers;

use App\Models\Booking;
use App\Models\DTOs\BookingResponse;

class BookingMapper
{
    public static function toResponse(Booking $booking): BookingResponse
    {
        return new BookingResponse(
            id: $booking->id,
            created_by: $booking->created_by,
            isBusiness: $booking->isBusiness,
            total_price: $booking->total_price,
            created_at: $booking->created_at->toISOString(),
            updated_at: $booking->updated_at->toISOString(),
            flight: FlightMapper::toResponse($booking->flight)
        );
    }
}

