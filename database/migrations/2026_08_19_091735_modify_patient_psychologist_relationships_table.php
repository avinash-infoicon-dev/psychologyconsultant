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
        Schema::table('patient_psychologist_relationships', function (Blueprint $table) {
            $table->index('patient_id');
            $table->dropUnique('patient_psychologist_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_psychologist_relationships', function (Blueprint $table) {
            $table->unique(['patient_id', 'psychologist_id'], 'patient_psychologist_unique');
        });
    }
};
