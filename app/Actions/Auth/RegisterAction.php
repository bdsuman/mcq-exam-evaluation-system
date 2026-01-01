<?php

namespace App\Actions\Auth;

use App\DataTransferObjects\Auth\RegisterDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterAction
{
    public function execute(RegisterDTO $dto): array
    {
        $email = Str::lower(trim($dto->email));

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            return [
                'success' => false,
                'message' => 'email_already_exists',
            ];
        }

        // Create new user
        $user = new User();
        $user->full_name = $dto->full_name;
        $user->email = $email;
        $user->password = Hash::make($dto->password);
        $user->language = $dto->language;
        $user->save();

        // Issue a Sanctum token
        $token = $user->createToken('web')->plainTextToken;

        return [
            'success' => true,
            'token' => $token,
            'user' => $user,
        ];
    }
}
