<?php

namespace Database\Seeders;

use App\Enums\UserGenderEnum;
use App\Enums\UserRoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $adminEmail = env('ADMIN_EMAIL', 'admin@mail.com');
        $adminName = env('ADMIN_NAME', 'Admin');
        $adminPassword = env('ADMIN_PASSWORD', 'password');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'full_name' => $adminName,
                'gender' => UserGenderEnum::MALE->value,
                'date_of_birth' => '2000-01-01',
                'password' => Hash::make($adminPassword),
                'email_verified_at' => $now,
                'role' => UserRoleEnum::ADMIN->value,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
