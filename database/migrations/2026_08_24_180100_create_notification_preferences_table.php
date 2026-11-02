<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->boolean('new_message_in_app')->default(true);
            $table->boolean('new_message_email')->default(true);
            $table->boolean('subscription_in_app')->default(true);
            $table->boolean('subscription_email')->default(true);
            $table->boolean('exchange_in_app')->default(true);
            $table->boolean('exchange_email')->default(true);
            $table->boolean('account_in_app')->default(true);
            $table->boolean('account_email')->default(true);
            $table->boolean('marketing_in_app')->default(false);
            $table->boolean('marketing_email')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
