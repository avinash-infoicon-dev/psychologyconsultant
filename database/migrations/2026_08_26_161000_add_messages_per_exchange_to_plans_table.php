<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('patient_messages_per_exchange')->nullable()->after('max_audio_seconds');
            $table->unsignedInteger('psychologist_messages_per_exchange')->nullable()->after('patient_messages_per_exchange');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'patient_messages_per_exchange',
                'psychologist_messages_per_exchange',
            ]);
        });
    }
};
