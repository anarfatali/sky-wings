<?php

namespace App\Services;

use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FlightService
{
    public function getAllByDate(string $date): Builder
    {
        $start = Carbon::parse($date)->startOfDay();

        return Flight::query()->where('flight_date', $start); //Pagination
    }

    public function getById(int $id): Model|Collection|Builder|array|null
    {
        return Flight::query()->findOrFail($id);
    }

    public function search(array $filter, bool $isBusiness): Builder
    {
        $flight = Flight::query();

        if (!empty($filter['from'])) {
            $flight->where('from', $filter['from']);
        }
        if (!empty($filter['to'])) {
            $flight->where('to', $filter['to']);
        }

        if (!empty($filter['date'])) {
            $flight->where('flight_date', $filter['date']);
        }

        if (!empty($filter['passenger_count'])) {
            $n = (int) $filter['passenger_count'];

            if ($isBusiness) {
                $flight->where('business_free_seats', '>=', $n);
            } else {
                $flight->where('econom_free_seats', '>=', $n);
            }
        }

        return $flight->orderBy('flight_date');
    }

    public function create(int $userId, array $payload): Builder|Model
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
