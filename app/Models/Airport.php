<?php

namespace App\Models;

use App\Models\enums\City;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    protected $table = 'airports';

    protected $fillable = [
        'name',
        'city',
    ];

    protected $casts = [
        'city' => City::class
    ];
}
