<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('home_slider_translations', 'para_text')) {
            return;
        }

        DB::statement('ALTER TABLE home_slider_translations MODIFY para_text LONGTEXT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasColumn('home_slider_translations', 'para_text')) {
            return;
        }

        DB::statement("UPDATE home_slider_translations SET para_text = '' WHERE para_text IS NULL");
        DB::statement('ALTER TABLE home_slider_translations MODIFY para_text LONGTEXT NOT NULL');
    }
};
