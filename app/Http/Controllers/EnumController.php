<?php

namespace App\Http\Controllers;

use App\Models\enums\Aircraft;
use App\Models\enums\City;

class EnumController extends BaseController
{
    public function aircrafts()
    {
        $values = array_map(fn($case) => $case->value, Aircraft::cases());
        return $this->sendDataResponse($values, 'Aircraft list');
    }

    public function cities()
    {
        $values = array_map(fn($case) => $case->value, City::cases());
        return $this->sendDataResponse($values, 'City list');
    }
}
