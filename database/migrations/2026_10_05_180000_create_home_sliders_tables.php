<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sliders', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('home_slider_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_slider_id')->constrained('home_sliders')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('subtitle')->nullable();
            $table->string('title');
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_original_name')->nullable();
            $table->timestamps();

            $table->unique(['home_slider_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_slider_translations');
        Schema::dropIfExists('home_sliders');
    }
};
