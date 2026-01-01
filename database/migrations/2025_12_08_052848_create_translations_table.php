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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->morphs('translatable'); // translatable_id + translatable_type
            $table->string('language', 5); // en, de, bn, etc.
            $table->string('key');          // title, description, etc.
            $table->text('value');          // actual content
            $table->timestamps();

            $table->unique(['translatable_type','translatable_id','language','key'], 'translations_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
