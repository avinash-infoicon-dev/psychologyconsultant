<?php

use App\Models\Subscription;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedInteger('included_exchanges')->default(0)->after('exchanges_remaining');
            $table->unsignedInteger('consumed_exchanges')->default(0)->after('included_exchanges');
            $table->unsignedInteger('purchased_exchanges')->default(0)->after('consumed_exchanges');
        });

        Subscription::query()->with(['plan', 'patient'])->chunkById(100, function ($subscriptions) {
            foreach ($subscriptions as $subscription) {
                $included = (int) ($subscription->plan?->exchange_quota
                    ?: ((int) $subscription->exchanges_used + (int) $subscription->exchanges_remaining));
                $consumed = (int) $subscription->exchanges_used;
                $purchased = max(0, (int) ($subscription->patient?->creditBalance() ?? 0));

                $subscription->forceFill([
                    'included_exchanges' => $included,
                    'consumed_exchanges' => $consumed,
                    'purchased_exchanges' => $purchased,
                ])->save();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['included_exchanges', 'consumed_exchanges', 'purchased_exchanges']);
        });
    }
};
