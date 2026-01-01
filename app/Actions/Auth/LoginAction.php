<?php

namespace App\Actions\Auth;

use App\DataTransferObjects\Auth\LoginDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginAction
{
    public function execute(LoginDTO $dto): array
    {
        $user = User::getUserByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->password)) {
            return [
                'success' => false,
                'message' => 'unauthorized',
            ];
        }

        // Delete existing tokens and create new one
        $user->tokens()->delete();
        $token = $user->createToken('admin')->plainTextToken;

        return [
            'success' => true,
            'token' => $token,
            'user' => $user,
        ];
    }
}
