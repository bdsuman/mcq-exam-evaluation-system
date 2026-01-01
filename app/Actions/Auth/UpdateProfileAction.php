<?php

namespace App\Actions\Auth;

use App\DataTransferObjects\Auth\UpdateProfileDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateProfileAction
{
    public function execute(User $user, UpdateProfileDTO $dto): User
    {
        $user->full_name = $dto->full_name;

        if ($dto->language) {
            $user->language = $dto->language;
        }

        if ($dto->gender) {
            $user->gender = $dto->gender;
        }

        if ($dto->password) {
            $user->password = Hash::make($dto->password);
        }

        if ($dto->avatar) {
            $user->avatar = $dto->avatar;
        }

        $user->save();

        return $user->fresh();
    }
}
