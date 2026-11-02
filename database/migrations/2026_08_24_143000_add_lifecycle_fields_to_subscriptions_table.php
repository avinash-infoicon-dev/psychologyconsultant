<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->boolean('auto_renew')->default(true)->after('purchased_exchanges');
            $table->boolean('cancel_at_period_end')->default(false)->after('auto_renew');
            $table->foreignId('pending_plan_id')->nullable()->after('plan_id')
                ->constrained('plans')->nullOnDelete();
            $table->timestamp('last_payment_failed_at')->nullable()->after('cancelled_at');
            $table->string('last_payment_error')->nullable()->after('last_payment_failed_at');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pending_plan_id');
            $table->dropColumn([
                'auto_renew',
                'cancel_at_period_end',
                'last_payment_failed_at',
                'last_payment_error',
            ]);
        });
    }
};
