<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->integer('amount'); // positive = credit added, negative = consumed
            $table->enum('source', ['purchase', 'refund', 'promo', 'admin_adjustment', 'consumption'])->default('purchase');
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('note')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
