<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Enums\City;
use App\Models\Enums\Aircraft;

class Flight extends Model
{
    protected $fillable = [
        'from',
        'to',
        'flight_date',
        'arrival_date',
        'aircraft',
        'econom_free_seats',
        'business_free_seats',
        'econom_price',
        'business_price',
        'flight_number',
        'free_seats',
        'booked_seats',
        'airport_id',
    ];

    protected $casts = [
        'flight_date' => 'datetime',
        'arrival_date' => 'datetime',
        'from' => City::class,
        'to' => City::class,
        'aircraft' => Aircraft::class,
        'booked_seats' => 'array',
    ];

    public function airport()
    {
        return $this->belongsTo(Airport::class);
    }
}
