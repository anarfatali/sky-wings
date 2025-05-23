<?php

namespace App\Models\DTOs;

class PassengerResponse
{
    public function __construct(
        public int    $id,
        public string $name,
        public string $surname,
        public bool   $isFemale,
        public string $dateOfBirth,
        public string $seatNumber
    )
    {
    }
}

