<?php

namespace App\Services;

use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class FlightService
{
    public function getAllByDate(string $date): Builder
    {
        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();

        return Flight::query()
            ->whereBetween('flight_date', [$start, $end])
            ->orderBy('flight_date');
    }

    public function getById(int $id): Model|Collection|Builder|array|null
    {
        return Flight::query()->findOrFail($id);
    }

    public function search(array $filter): Builder
    {
        $query = Flight::query();

        if (!empty($filter['from'])) {
            $query->where('from', $filter['from']);
        }
        if (!empty($filter['to'])) {
            $query->where('to', $filter['to']);
        }

        if (!empty($filter['date'])) {
            $query->where('flight_date', $filter['date']);
        }

        return $query->orderBy('flight_date');
    }


    public function create(int $userId, array $payload): Builder|Model
    {
        $payload['created_by'] = $userId;

        $payload = Flight::query()->create($payload);

        return $payload->id;
    }


    public function update(int $userId, int $flightId, array $payload): void
    {
        $flight = Flight::query()->findOrFail($flightId);

        $payload['updated_by'] = $userId;

        $flight->update($payload);
    }

    public function delete(int $flightId): void
    {
        $flight = Flight::query()->findOrFail($flightId);

        $flight->delete();
    }
}
