<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('home_slider_translations')) {
            return;
        }

        if (Schema::hasColumn('home_slider_translations', 'para_text')) {
            DB::statement('ALTER TABLE home_slider_translations MODIFY para_text LONGTEXT NULL');

            return;
        }

        Schema::table('home_slider_translations', function (Blueprint $table) {
            $table->text('para_text')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('home_slider_translations', 'para_text')) {
            return;
        }

        Schema::table('home_slider_translations', function (Blueprint $table) {
            $table->dropColumn('para_text');
        });
    }
};
