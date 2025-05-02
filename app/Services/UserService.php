<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserService
{

    function getById(int $id)
    {
        $user = User::query()->findOrFail($id);
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'profile_photo' => $user->profile_photo
        ];

    }

    public function create(array $validated)
    {
        $user = User::query()->create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => password_hash($validated['password'], PASSWORD_DEFAULT),
        ]);
        return $user->id;
    }

    public function updatePassword($id, array $validated)
    {
        $user = User::query()->findOrFail($id);
        if (!password_verify($validated['old_password'], $user->password)) {
            throw new BadRequestHttpException('Wrong password');
        }
        $user->password = password_hash($validated['new_password'], PASSWORD_DEFAULT);
        $user->save();
    }

    public function uploadProfilePhoto($id, $request): JsonResponse
    {
        $user = User::query()->findOrFail($id);

        $file = $request->file('profile_photo');
        $path = $file->store('profile_photos', 'public');

        if ($user->profile_photo) {
            $this->deleteProfilPhoto($id);
        }

        $user->profile_photo = basename($path);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile photo uploaded successfully.',
            'data' => [
                'profile_photo_url' => asset('storage/' . $path)
            ]
        ]);
    }

    public function deleteProfilPhoto($id)
    {
        $user = User::findOrFail($id);

        if ($user->profile_photo) {
            $filePath = 'profile_photos/' . $user->profile_photo;

            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            $user->profile_photo = null;
            $user->save();
        }
    }
}
