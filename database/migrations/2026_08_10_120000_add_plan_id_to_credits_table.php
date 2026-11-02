<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            // Which plan/product funded this credit (on-demand vs additional vs promo).
            $table->foreignId('plan_id')
                ->nullable()
                ->after('patient_id')
                ->constrained('plans')
                ->nullOnDelete();
        });

        // Backfill legacy purchase credits from note: plan "on-demand" (...)
        $plansBySlug = DB::table('plans')->pluck('id', 'slug');

        DB::table('credits')
            ->where('source', 'purchase')
            ->whereNull('plan_id')
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($plansBySlug) {
                foreach ($rows as $row) {
                    if (! is_string($row->note) || ! preg_match('/plan "([^"]+)"/', $row->note, $matches)) {
                        continue;
                    }

                    $planId = $plansBySlug[$matches[1]] ?? null;
                    if ($planId) {
                        DB::table('credits')->where('id', $row->id)->update(['plan_id' => $planId]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
        });
    }
};
