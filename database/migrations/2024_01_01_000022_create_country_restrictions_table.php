<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_restrictions', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->unique(); // ISO 3166-1 alpha-2
            $table->string('country_name')->nullable();
            $table->enum('status', ['allowed', 'restricted', 'blocked'])->default('allowed');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_restrictions');
    }
};
