<?php

namespace App\Models;

use App\Models\enums\Aircraft;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flight extends Model
{
    protected $fillable = [
        'departure_airport_id',
        'arrival_airport_id',
        'flight_date',
        'arrival_date',
        'aircraft',
        'total_seats',
        'econom_free_seats',
        'business_free_seats',
        'econom_price',
        'business_price',
        'flight_number',
        'booked_seats'
    ];

    protected $casts = [
        'flight_date' => 'datetime',
        'arrival_date' => 'datetime',
        'aircraft' => Aircraft::class,
        'booked_seats' => 'array',
    ];

    public function departureAirport():BelongsTo
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    public function arrivalAirport():BelongsTo
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Flight $flight) {
            $flight->total_seats = $flight->econom_free_seats + $flight->business_free_seats;

            if (empty($flight->flight_number)) {
                $flight->flight_number = self::generateFlightNumber();
            }
        });
    }

    private static function generateFlightNumber(): string
    {
        $lastDigit = random_int(0, 9);
        return "J2 810{$lastDigit}";
    }

    public function airport(): BelongsTo
    {
        return $this->belongsTo(Airport::class);
    }
}
