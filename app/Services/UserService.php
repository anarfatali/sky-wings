<?php

namespace App\Services;

use App\Models\User;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserService
{

    function getById(int $id)
    {
        return null;

    }

    public function create(array $validated)
    {
        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => password_hash($validated['password'], PASSWORD_DEFAULT),
        ]);
        return $user->id;
    }

    public function updatePassword($id, array $validated)
    {
        $user = User::query()->findOrFail($id);
        if (!password_verify($validated['password'], $user->password)) {
            throw new BadRequestHttpException('Wrong password');
        }
        //ardi var
    }

}
