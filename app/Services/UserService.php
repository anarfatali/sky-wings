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
        return null;

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
        if (!password_verify($validated['password'], $user->password)) {
            throw new BadRequestHttpException('Wrong password');
        }
        //ardi var
    }

    public function uploadProfilePhoto($id, $request): JsonResponse
    {
        $user = User::query()->findOrFail($id);

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->store('profile_photos', 'public');

            if ($user->profile_photo) {
                Storage::disk('public/profile_photos')->delete($user->profile_photo);
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
        throw new BadRequestHttpException('Profile photo upload failed.');
    }

    public function deleteProfilPhoto($id)
    {
        $user = User::query()->findOrFail($id);
        if ($user->profil_photo) {
            Storage::disk('public/profile_photos')->delete($user->profile_photo);
        }
    }
}
