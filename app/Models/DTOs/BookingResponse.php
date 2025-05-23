<?php

namespace App\Models\DTOs;

class BookingResponse
{
    public function __construct(
        public int            $id,
        public int            $created_by,
        public bool           $isBusiness,
        public string         $total_price,
        public string         $created_at,
        public string         $updated_at,
        public FlightResponse $flight,
    )
    {
    }
}

