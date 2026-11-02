<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Legacy table disabled. Professional profiles now live in
         * `professionals` (created by 2026_07_29_100513_professionaltable).
         *
         * Schema::create('psychologists', function (Blueprint $table) {
         *     $table->id();
         *     $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
         *     $table->string('license_number')->unique()->nullable();
         *     $table->string('specialization')->nullable();
         *     $table->text('bio')->nullable();
         *     $table->unsignedSmallInteger('years_experience')->default(0);
         *     $table->boolean('is_verified')->default(false);
         *     $table->boolean('is_accepting_patients')->default(true);
         *     $table->unsignedInteger('max_active_patients')->default(40);
         *     $table->json('languages')->nullable();
         *     $table->json('credentials')->nullable();
         *     $table->timestamps();
         *     $table->softDeletes();
         * });
         */
    }

    public function down(): void
    {
        // Legacy table is no longer created.
        // Schema::dropIfExists('psychologists');
    }
};
