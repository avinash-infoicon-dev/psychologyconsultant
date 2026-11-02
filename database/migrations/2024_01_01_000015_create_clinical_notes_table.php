<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            // Legacy FK:
            // $table->foreignId('psychologist_id')->constrained('psychologists')->cascadeOnDelete();
            // Constraint is added after the professionals table exists.
            $table->foreignId('psychologist_id');
            $table->foreignId('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->text('content'); // encrypted at rest via cast, see ClinicalNote model
            $table->boolean('is_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('editable_until')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'psychologist_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_notes');
    }
};
