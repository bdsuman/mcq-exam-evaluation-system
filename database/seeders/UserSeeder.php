<?php

namespace Database\Seeders;

use App\Enums\UserGenderEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Reset table and sequences safely across drivers
        Schema::disableForeignKeyConstraints();
        User::truncate(); // Postgres: TRUNCATE ... RESTART IDENTITY CASCADE
        Schema::enableForeignKeyConstraints();

        $now = Carbon::now();

        $fixedUsers = [
            [
                'full_name'         => 'User',
                'email'             => 'user@mail.com',
                'gender'            => UserGenderEnum::MALE->value,
                'date_of_birth'     => '2000-01-01',
                'password'          => Hash::make('password'),
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];
        foreach ($fixedUsers as $data) {
            User::create($data);
        }

        // create 5 mobile users with ids 5..9
        User::factory()->count(5)->create([
            'password' => Hash::make('password'),
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
