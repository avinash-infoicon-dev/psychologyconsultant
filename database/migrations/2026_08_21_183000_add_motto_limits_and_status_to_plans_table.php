<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('motto')->nullable()->after('description');
            $table->unsignedInteger('max_text_chars')->nullable()->after('exchange_quota');
            $table->unsignedInteger('max_audio_seconds')->nullable()->after('max_text_chars');
            $table->string('status', 16)->default('draft')->after('is_active');
        });

        // Keep history: previously inactive rows become draft; active stay active.
        DB::table('plans')->where('is_active', true)->update(['status' => 'active']);
        DB::table('plans')->where('is_active', false)->update(['status' => 'draft']);
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['motto', 'max_text_chars', 'max_audio_seconds', 'status']);
        });
    }
};
