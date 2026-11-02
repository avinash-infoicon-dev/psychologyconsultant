<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->unsignedInteger('patient_message_limit')->nullable()->after('status');
            $table->unsignedInteger('psychologist_message_limit')->nullable()->after('patient_message_limit');
            $table->unsignedInteger('patient_messages_used')->default(0)->after('psychologist_message_limit');
            $table->unsignedInteger('psychologist_messages_used')->default(0)->after('patient_messages_used');
        });
    }

    public function down(): void
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->dropColumn([
                'patient_message_limit',
                'psychologist_message_limit',
                'patient_messages_used',
                'psychologist_messages_used',
            ]);
        });
    }
};
