<?php

namespace App\Services;

use App\Models\User;

class AdminService
{

    public function create(array $validated)
    {
        $user = User::query()->create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => password_hash($validated['password'], PASSWORD_DEFAULT),
            'is_admin' => true
        ]);
        return $user->id;
    }

    public function getAdmins()
    {
        return User::query()
            ->where('is_admin', true)
            ->get();
    }

    public function getAdmin(int $id)
    {
        return User::query()
            ->where('id', $id)
            ->where('is_admin', true)
            ->firstOrFail();
    }
}
