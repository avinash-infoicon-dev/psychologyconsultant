<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * §6.1–§6.5 pricing grid support:
 * plan_type, response SLA, channels, recommended flag,
 * and per_exchange billing for on-demand / additional units.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('plan_type', 32)->default('subscription')->after('slug');
            $table->unsignedSmallInteger('response_time_hours')->nullable()->after('exchange_quota');
            $table->string('response_time_label')->nullable()->after('response_time_hours');
            $table->json('channels')->nullable()->after('response_time_label');
            $table->boolean('is_recommended')->default(false)->after('is_active');
        });

        // Widen billing_interval beyond monthly/quarterly/yearly for §6.3 on-demand rows.
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE plans MODIFY billing_interval VARCHAR(32) NOT NULL DEFAULT 'monthly'");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE plans ALTER COLUMN billing_interval TYPE VARCHAR(32)');
        }
        // sqlite stores enums as strings already — no alter needed.
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'plan_type',
                'response_time_hours',
                'response_time_label',
                'channels',
                'is_recommended',
            ]);
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // Old enum (kept for rollback). Rows with per_exchange must be fixed first.
            DB::statement("ALTER TABLE plans MODIFY billing_interval ENUM('monthly', 'quarterly', 'yearly') NOT NULL DEFAULT 'monthly'");
        }
    }
};
