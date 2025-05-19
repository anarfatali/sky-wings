<?php

namespace App\Services;

use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FlightService
{
    public function getAllByDate(string $date, int $userId)
    {
        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();

        return Flight::query()->whereBetween('flight_date', [$start, $end])->paginate(5);
    }

    public function getById(int $id)
    {
        return Flight::query()->findOrFail($id);
    }

    public function search(array $filter)
    {
        return Flight::query()
            ->whereHas('departureAirport', function ($q) use ($filter) {
                $q->where('city', $filter['from']);
            })
            ->whereHas('arrivalAirport', function ($q) use ($filter) {
                $q->where('city', $filter['to']);
            })
            ->whereBetween('flight_date', [
                Carbon::parse($filter['date'])->startOfDay(),
                Carbon::parse($filter['date'])->endOfDay(),
            ])
            ->where('business_free_seats', '>=', (int)$filter['passenger_count'])
            ->orWhere('econom_free_seats', '>=', (int)$filter['passenger_count'])
            ->orderBy('flight_date')
            ->get();
    }

    public function create(int $userId, array $payload)
    {
        return DB::transaction(function () use ($userId, $payload) {
            $payload['created_by'] = $userId;
            //check if user is admin

            $payload = Flight::query()->create($payload);

            return $payload->id;
        });
    }

    public function update(int $userId, int $flightId, array $payload): void
    {
        //check if user is admin
        $flight = Flight::query()->findOrFail($flightId);

        $payload['updated_by'] = $userId;

        $flight->update($payload);
    }

    public function delete(int $flightId): void
    {
        //check if user is admin
        $flight = Flight::query()->findOrFail($flightId);

        $flight->delete();
    }
}
