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
        Schema::create('specialization_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialization_id')->constrained('specializations')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['specialization_id', 'locale']);
        });

        $locales = Locales::codes();
        if ($locales === []) {
            $locales = ['fr_CH', 'en'];
        }

        foreach (DB::table('specializations')->get() as $specialization) {
            foreach ($locales as $locale) {
                DB::table('specialization_translations')->insert([
                    'specialization_id' => $specialization->id,
                    'locale' => $locale,
                    'name' => $specialization->name,
                    'description' => $specialization->description ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('specialization_translations');
    }
};
