<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cms_site_setting_translations')) {
            return;
        }

        Schema::table('cms_site_setting_translations', function (Blueprint $table) {
            if (! Schema::hasColumn('cms_site_setting_translations', 'register_psychologist_copy')) {
                $after = Schema::hasColumn('cms_site_setting_translations', 'register_form_subtitle')
                    ? 'register_form_subtitle'
                    : 'footer_background_path';
                $table->json('register_psychologist_copy')->nullable()->after($after);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cms_site_setting_translations')) {
            return;
        }

        Schema::table('cms_site_setting_translations', function (Blueprint $table) {
            if (Schema::hasColumn('cms_site_setting_translations', 'register_psychologist_copy')) {
                $table->dropColumn('register_psychologist_copy');
            }
        });
    }
};
