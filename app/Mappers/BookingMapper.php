<?php

namespace App\Mappers;

use App\Models\Booking;
use App\Models\DTOs\BookingResponse;
use App\Models\DTOs\PassengerResponse;

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
            flight: FlightMapper::toResponse($booking->flight),
            passengers: $booking->passengers->map(fn($p) => new PassengerResponse(
                id: $p->id,
                name: $p->name,
                surname: $p->surname,
                isFemale: $p->is_female,
                dateOfBirth: $p->date_of_birth,
                seatNumber: $p->seat_number,
            ))->toArray()
        );
    }
}

