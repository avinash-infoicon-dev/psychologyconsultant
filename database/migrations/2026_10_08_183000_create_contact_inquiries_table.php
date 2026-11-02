<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contact_inquiries')) {
            return;
        }

        Schema::create('contact_inquiries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('consultation_type')->nullable();
            $table->text('message')->nullable();
            $table->string('source', 50)->nullable();
            $table->string('page_url', 500)->nullable();
            $table->string('locale', 10)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('mailed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_inquiries');
    }
};
