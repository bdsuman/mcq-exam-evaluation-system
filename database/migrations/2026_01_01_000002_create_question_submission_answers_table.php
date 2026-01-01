<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_submission_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->json('selected_option_ids');
            $table->json('correct_option_ids');
            $table->decimal('mark', 8, 2)->default(0);
            $table->decimal('obtained_marks', 8, 2)->default(0);
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_submission_answers');
    }
};
