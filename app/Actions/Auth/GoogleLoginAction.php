<?php

namespace App\Actions\Auth;

use App\DataTransferObjects\Auth\GoogleLoginDTO;
use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleLoginAction
{
    public function execute(GoogleLoginDTO $dto): array
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($dto->token);
        } catch (Throwable $e) {
            report($e);

            return [
                'success' => false,
                'message' => 'invalid_google_token',
            ];
        }

        $email = Str::lower($googleUser->getEmail() ?? '');

        if (! $email) {
            return [
                'success' => false,
                'message' => 'email_not_provided',
            ];
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'full_name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: 'Google User'),
                'email' => $email,
                'language' => $dto->language ?? 'en',
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'role' => UserRoleEnum::STUDENT->value,
            ]);
        } else {
            $updates = [];

            if (! $user->email_verified_at) {
                $updates['email_verified_at'] = now();
            }

            if ($googleUser->getAvatar() && $user->avatar !== $googleUser->getAvatar()) {
                $updates['avatar'] = $googleUser->getAvatar();
            }

            if ($dto->language && $user->language !== $dto->language) {
                $updates['language'] = $dto->language;
            }

            if (! $user->role) {
                $updates['role'] = UserRoleEnum::STUDENT->value;
            }

            if ($updates) {
                $user->fill($updates)->save();
            }
        }

        $user->tokens()->delete();
        $token = $user->createToken('admin')->plainTextToken;

        return [
            'success' => true,
            'token' => $token,
            'user' => $user,
        ];
    }
}
