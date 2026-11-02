<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add psychologist fields that were missing from professionals.
     * Safe to run after the create migration was updated to include the same columns.
     */
    public function up(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            if (! Schema::hasColumn('professionals', 'license_number')) {
                $table->string('license_number')->nullable()->unique();
            }

            if (! Schema::hasColumn('professionals', 'specialization')) {
                $table->string('specialization')->nullable();
            }

            if (! Schema::hasColumn('professionals', 'credentials')) {
                $table->json('credentials')->nullable();
            }

            if (! Schema::hasColumn('professionals', 'is_accepting_patients')) {
                $table->boolean('is_accepting_patients')->default(true);
            }

            if (! Schema::hasColumn('professionals', 'max_active_patients')) {
                $table->unsignedInteger('max_active_patients')->default(40);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $columns = collect([
                'license_number',
                'specialization',
                'credentials',
                'is_accepting_patients',
                'max_active_patients',
            ])->filter(fn (string $column) => Schema::hasColumn('professionals', $column))->all();

            if ($columns === []) {
                return;
            }

            if (in_array('license_number', $columns, true)) {
                $table->dropUnique(['license_number']);
            }

            $table->dropColumn($columns);
        });
    }
};
