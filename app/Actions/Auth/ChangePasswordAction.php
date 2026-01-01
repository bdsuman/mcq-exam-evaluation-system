<?php

namespace App\Actions\Auth;

use App\DataTransferObjects\Auth\ChangePasswordDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangePasswordAction
{
    public function execute(User $user, ChangePasswordDTO $dto): array
    {
        // Verify current password
        if (!Hash::check($dto->current_password, $user->password)) {
            return [
                'success' => false,
                'message' => 'current_password_is_incorrect',
            ];
        }

        // Update with new password
        $user->password = Hash::make($dto->password);
        $user->save();

        return [
            'success' => true,
            'user' => $user->fresh(),
        ];
    }
}
