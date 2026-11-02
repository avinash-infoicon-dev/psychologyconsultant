<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('price'); // smallest currency unit (e.g. cents/rappen)
            $table->string('currency', 3)->default('CHF');
            $table->enum('billing_interval', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->unsignedInteger('exchange_quota'); // messages exchanges per cycle
            $table->json('features')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
