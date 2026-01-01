<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('task_archives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_task_id');
            $table->foreignIdFor(App\Models\User::class)->constrained()->onDelete('cascade');
            $table->string('avatar')->nullable();
            $table->boolean('is_completed')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_archives');
    }
};
