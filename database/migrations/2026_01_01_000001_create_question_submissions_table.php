<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_marks', 8, 2)->default(0);
            $table->decimal('obtained_marks', 8, 2)->default(0);
            $table->unsignedInteger('questions_answered')->default(0);
            $table->unsignedInteger('correct_answers')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_submissions');
    }
};
