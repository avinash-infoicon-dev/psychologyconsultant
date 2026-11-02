<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
            $table->foreignId('exchange_id')->nullable()->constrained('exchanges')->nullOnDelete();
            $table->enum('type', [
                'included',
                'consumed',
                'purchased',
                'admin_adjustment',
                'refund',
                'expired',
                'renewal',
            ]);
            $table->integer('quantity');
            $table->string('reason')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['patient_id', 'created_at']);
            $table->index(['subscription_id', 'type']);
            $table->index(['exchange_id', 'type']);
        });

        $this->backfillOpeningBalances();

        $permission = Permission::query()->updateOrCreate(
            ['slug' => 'subscriptions.adjust'],
            [
                'name' => 'Adjust Subscription Exchanges',
                'group' => 'subscriptions',
            ]
        );

        Role::query()
            ->whereIn('slug', ['superadmin', 'admin'])
            ->each(function (Role $role) use ($permission) {
                $role->permissions()->syncWithoutDetaching([$permission->id]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_transactions');

        $permission = Permission::query()->where('slug', 'subscriptions.adjust')->first();
        if ($permission) {
            $permission->roles()->detach();
            $permission->delete();
        }
    }

    protected function backfillOpeningBalances(): void
    {
        if (! Schema::hasTable('subscriptions')) {
            return;
        }

        $now = now();

        DB::table('subscriptions')->orderBy('id')->each(function (object $subscription) use ($now) {
            $exists = DB::table('exchange_transactions')
                ->where('subscription_id', $subscription->id)
                ->exists();

            if ($exists) {
                return;
            }

            $included = (int) ($subscription->included_exchanges ?? 0);
            $purchased = (int) ($subscription->purchased_exchanges ?? 0);
            $consumed = (int) ($subscription->consumed_exchanges ?? 0);
            $patientId = (int) $subscription->patient_id;

            if ($included > 0) {
                DB::table('exchange_transactions')->insert([
                    'patient_id' => $patientId,
                    'subscription_id' => $subscription->id,
                    'type' => 'included',
                    'quantity' => $included,
                    'reason' => 'Opening included allowance',
                    'created_at' => $subscription->created_at ?? $now,
                    'updated_at' => $now,
                ]);
            }

            if ($purchased > 0) {
                DB::table('exchange_transactions')->insert([
                    'patient_id' => $patientId,
                    'subscription_id' => $subscription->id,
                    'type' => 'purchased',
                    'quantity' => $purchased,
                    'reason' => 'Opening additional exchanges',
                    'created_at' => $subscription->created_at ?? $now,
                    'updated_at' => $now,
                ]);
            }

            if ($consumed > 0) {
                DB::table('exchange_transactions')->insert([
                    'patient_id' => $patientId,
                    'subscription_id' => $subscription->id,
                    'type' => 'consumed',
                    'quantity' => -$consumed,
                    'reason' => 'Opening consumed total',
                    'created_at' => $subscription->created_at ?? $now,
                    'updated_at' => $now,
                ]);
            }
        });
    }
};
