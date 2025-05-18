<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AirportController extends BaseController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'string',
            'city' => 'string'
        ]);
        Airport::query()->create($validated);
        return $this->sendResponse("Airport successfully added");
    }

    public function getAll()
    {
        return $this->sendDataResponse(Airport::query()->get());
    }
}
