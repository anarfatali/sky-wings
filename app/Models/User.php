<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'username',
        'email',
        'password',
        'is_admin',
        'profile_photo'
    ];

    protected $hidden = [
        'is_admin',
        'password'
    ];
}
