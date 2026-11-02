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
        Schema::table('conversations', function (Blueprint $table) {
            $table->foreignId('relationship_id')->nullable()->after('id')->constrained('patient_psychologist_relationships')->nullOnDelete();
        });

        // Link existing conversations safely
        // Loop over all conversations. If there is exactly one relationship for the patient/psychologist pair, link it.
        $conversations = \App\Models\Conversation::all();
        foreach ($conversations as $conversation) {
            $relationships = \App\Models\PatientPsychologistRelationship::query()
                ->where('patient_id', $conversation->patient_id)
                ->where('psychologist_id', $conversation->psychologist_id)
                ->get();
            
            if ($relationships->count() === 1) {
                $conversation->update(['relationship_id' => $relationships->first()->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['relationship_id']);
            $table->dropColumn('relationship_id');
        });
    }
};
