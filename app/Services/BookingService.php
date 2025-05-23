<?php

namespace App\Services;

use App\Exceptions\BadRequestException;
use App\Mappers\BookingMapper;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Passenger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function create(int $userId, array $validated)
    {
        return DB::transaction(function () use ($userId, $validated) {
            $flight = Flight::query()->findOrFail($validated['flight_id']);
            $passengerCount = count($validated['passengers']);
            if ($validated['isBusiness']) {
                $price = $flight->business_price;
                if ($flight->business_free_seats >= $passengerCount) {
                    $flight->business_free_seats = $flight->business_free_seats - $passengerCount;
                } else throw new BadRequestException("There is no enough seats for this flight");
            } else {
                $price = $flight->econom_price;
                if ($flight->econom_free_seats >= $passengerCount) {
                    $flight->econom_free_seats = $flight->econom_free_seats - $passengerCount;
                } else throw new BadRequestException("There is no enough seats for this flight");
            }
            $totalPrice = $price * $passengerCount;
            $bookedSeats = $flight->booked_seats;
            foreach ($validated['passengers'] as $passenger) {
                $bookedSeats = $bookedSeats . "," . $passenger['seat_number'];
            }
            $flight->booked_seats = $bookedSeats;

            $booking = Booking::query()->create([
                'created_by' => $userId,
                'flight_id' => $validated['flight_id'],
                'isBusiness' => $validated['isBusiness'],
                'total_price' => $totalPrice,
            ]);
            $flight->save();
            foreach ($validated['passengers'] as $passenger) {
                Passenger::query()->create([
                    'name' => $passenger['name'],
                    'surname' => $passenger['surname'],
                    'is_female' => $passenger['is_female'],
                    'date_of_birth' => $passenger['date_of_birth'],
                    'seat_number' => $passenger['seat_number'],
                    'phone_number' => $passenger['phone_number'] ?? null,
                    'booking_id' => $booking->id
                ]);
            }
            return $booking->id;
        });
    }

    public function getMyFlights(int $userId)
    {
        return Booking::query()
            ->where('created_by', $userId)
            ->whereHas('flight', function ($query) {
                $query->where('flight_date', '>', Carbon::now());
            })
            ->with(['flight', 'passengers'])
            ->with('flight')
            ->get()
            ->map(fn($booking) => BookingMapper::toResponse($booking));
    }

    public function getHistory(int $userId)
    {
        return Booking::query()
            ->where('created_by', $userId)
            ->with('flight')
            ->get()
            ->map(fn($booking) => BookingMapper::toResponse($booking));
    }
}
