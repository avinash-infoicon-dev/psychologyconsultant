<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $this->copyLegacyPsychologists();

        $this->dropPsychologistForeignKey('patients');
        $this->dropPsychologistForeignKey('conversations');
        $this->dropPsychologistForeignKey('clinical_notes');

        Schema::table('patients', function (Blueprint $table) {
            $table->foreign('psychologist_id', 'patients_professional_id_foreign')
                ->references('id')
                ->on('professionals')
                ->nullOnDelete();
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->foreign('psychologist_id', 'conversations_professional_id_foreign')
                ->references('id')
                ->on('professionals')
                ->cascadeOnDelete();
        });

        Schema::table('clinical_notes', function (Blueprint $table) {
            $table->foreign('psychologist_id', 'clinical_notes_professional_id_foreign')
                ->references('id')
                ->on('professionals')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        $this->dropPsychologistForeignKey('patients');
        $this->dropPsychologistForeignKey('conversations');
        $this->dropPsychologistForeignKey('clinical_notes');

        /*
         * Do not restore foreign keys to the disabled psychologists table.
         * The psychologist_id compatibility columns remain available.
         */
    }

    private function copyLegacyPsychologists(): void
    {
        if (! Schema::hasTable('psychologists')) {
            return;
        }

        $typeId = DB::table('professional_types')
            ->where('slug', 'psychologist')
            ->value('id');

        if (! $typeId) {
            $typeId = DB::table('professional_types')->insertGetId([
                'name' => 'Psychologist',
                'slug' => 'psychologist',
                'description' => 'Mental health and psychology professionals.',
                'status' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (DB::table('psychologists')->orderBy('id')->get() as $legacy) {
            $professionalId = DB::table('professionals')
                ->where('user_id', $legacy->user_id)
                ->value('id');

            if (! $professionalId) {
                $user = DB::table('users')->where('id', $legacy->user_id)->first();
                $name = trim((string) ($user->name ?? 'Psychologist'));
                $nameParts = explode(' ', $name, 2);
                $slug = Str::slug($name).'-'.$legacy->user_id;

                $professionalId = DB::table('professionals')->insertGetId([
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $legacy->user_id,
                    'professional_type_id' => $typeId,
                    'first_name' => $nameParts[0] ?: 'Psychologist',
                    'last_name' => $nameParts[1] ?? null,
                    'slug' => $slug,
                    'email' => $user->email ?? null,
                    'phone' => $user->phone ?? null,
                    'license_number' => $legacy->license_number,
                    'specialization' => $legacy->specialization,
                    'experience_years' => $legacy->years_experience,
                    'bio' => $legacy->bio,
                    'languages' => $legacy->languages,
                    'credentials' => $legacy->credentials,
                    'is_verified' => $legacy->is_verified,
                    'is_accepting_patients' => $legacy->is_accepting_patients,
                    'max_active_patients' => $legacy->max_active_patients,
                    'status' => true,
                    'created_at' => $legacy->created_at ?? now(),
                    'updated_at' => $legacy->updated_at ?? now(),
                    'deleted_at' => $legacy->deleted_at,
                ]);
            }

            DB::table('patients')
                ->where('psychologist_id', $legacy->id)
                ->update(['psychologist_id' => $professionalId]);

            DB::table('conversations')
                ->where('psychologist_id', $legacy->id)
                ->update(['psychologist_id' => $professionalId]);

            DB::table('clinical_notes')
                ->where('psychologist_id', $legacy->id)
                ->update(['psychologist_id' => $professionalId]);
        }
    }

    private function dropPsychologistForeignKey(string $tableName): void
    {
        $foreignKey = collect(Schema::getForeignKeys($tableName))
            ->first(fn (array $key) => in_array('psychologist_id', $key['columns'], true));

        if (! $foreignKey) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($foreignKey) {
            $table->dropForeign($foreignKey['name']);
        });
    }
};
