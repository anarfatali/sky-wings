<?php

namespace App\Models\DTOs;

class PassengerResponse
{
    public function __construct(
        public int    $id,
        public string $name,
        public string $surname,
        public bool   $is_female,
        public string $date_of_birth,
        public string $seat_number
    )
    {
    }
}

