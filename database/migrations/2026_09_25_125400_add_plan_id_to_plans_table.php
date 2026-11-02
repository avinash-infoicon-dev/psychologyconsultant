<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('plans', 'plan_id')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->string('plan_id')->nullable()->after('stripe_price_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('plans', 'plan_id')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->dropColumn('plan_id');
            });
        }
    }
};
