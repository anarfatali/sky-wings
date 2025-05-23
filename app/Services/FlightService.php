<?php

namespace App\Services;

use App\Mappers\FlightMapper;
use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FlightService
{
    public function getAllByDate(string $date)
    {
        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();

        $flights = Flight::query()
            ->whereBetween('flight_date', [$start, $end])
            ->paginate(5);

        $mapped = $flights->getCollection()->map(function ($flight) {
            return FlightMapper::toResponse($flight);
        });

        return new LengthAwarePaginator(
            $mapped,
            $flights->total(),
            $flights->perPage(),
            $flights->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function getById(int $id)
    {
        return FlightMapper::toResponse(Flight::query()->findOrFail($id));
    }

    public function search(array $filter)
    {
        $flights = Flight::query()
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
            ->where(function ($q) use ($filter) {
                $q->where('business_free_seats', '>=', (int)$filter['passenger_count'])
                    ->orWhere('econom_free_seats', '>=', (int)$filter['passenger_count']);
            })
            ->orderBy('flight_date')
            ->get();

        return $flights->map(fn($flight) => FlightMapper::toResponse($flight));
    }

    public function create(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            $payload = Flight::query()->create($payload);

            return $payload->id;
        });
    }

    public function update(int $flightId, array $payload): void
    {
        //check if user is admin
        $flight = Flight::query()->findOrFail($flightId);

        $flight->update($payload);
    }

    public function delete(int $flightId): void
    {
        //check if user is admin
        $flight = Flight::query()->findOrFail($flightId);

        $flight->delete();
    }
}
