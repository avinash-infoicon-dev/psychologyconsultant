<?php

use App\Support\Locales;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');
            $table->string('motto')->nullable();
            $table->text('description')->nullable();
            $table->string('response_time_label')->nullable();
            $table->json('features')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'locale']);
        });

        $locales = Locales::codes();
        if ($locales === []) {
            $locales = ['fr_CH', 'en'];
        }

        $plans = DB::table('plans')->get();

        foreach ($plans as $plan) {
            $features = $plan->features;
            $featuresJson = null;
            if (is_array($features)) {
                $featuresJson = json_encode($features);
            } elseif (is_string($features) && $features !== '') {
                $featuresJson = $features;
            }

            foreach ($locales as $locale) {
                DB::table('plan_translations')->insert([
                    'plan_id' => $plan->id,
                    'locale' => $locale,
                    'name' => $plan->name,
                    'motto' => $plan->motto ?? null,
                    'description' => $plan->description ?? null,
                    'response_time_label' => $plan->response_time_label ?? null,
                    'features' => $featuresJson,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_translations');
    }
};
