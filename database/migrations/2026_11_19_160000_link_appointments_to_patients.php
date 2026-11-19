<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('patient_id')
                ->nullable()
                ->after('professional_id')
                ->constrained('patients')
                ->nullOnDelete();
            $table->foreignId('relationship_id')
                ->nullable()
                ->after('patient_id')
                ->constrained('patient_psychologist_relationships')
                ->nullOnDelete();
            $table->unsignedSmallInteger('duration_minutes')->default(50)->after('appointment_time');
            $table->foreignId('requested_by')
                ->nullable()
                ->after('duration_minutes')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('cancelled_by')
                ->nullable()
                ->after('requested_by')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('cancel_reason')->nullable()->after('notes');
            $table->index(['patient_id', 'appointment_date']);
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['patient_id', 'appointment_date']);
            $table->dropConstrainedForeignId('patient_id');
            $table->dropConstrainedForeignId('relationship_id');
            $table->dropConstrainedForeignId('requested_by');
            $table->dropConstrainedForeignId('cancelled_by');
            $table->dropColumn(['duration_minutes', 'cancel_reason']);
        });
    }
};
