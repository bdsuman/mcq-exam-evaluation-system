<?php

use App\Enums\UserGenderEnum;
use App\Enums\AppLanguageEnum;
use App\Enums\LoginTypeEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 255);
            $table->enum('gender', array_column(UserGenderEnum::cases(), 'value'))->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('email', 255)->unique();
            $table->string('email_verify_token', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->string('language', 2)->default('en');
            $table->string('password_reset_token', 255)->nullable();
            $table->rememberToken();
            $table->timestamps();
            // Helpful indexes
            $table->index('full_name');
            $table->index('email');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
