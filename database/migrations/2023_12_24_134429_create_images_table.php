<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();
            $table->string('query')->nullable();
            $table->text('image');
            $table->text('original');

            $table->unsignedInteger('original_width');
            $table->unsignedInteger('original_height');
            $table->unsignedInteger('resized_width');
            $table->unsignedInteger('resized_height');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
