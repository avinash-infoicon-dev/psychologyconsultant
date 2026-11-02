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
            if (! Schema::hasColumn('cms_site_setting_translations', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('copyright');
            }
            if (! Schema::hasColumn('cms_site_setting_translations', 'logo_dark_path')) {
                $table->string('logo_dark_path')->nullable()->after('logo_path');
            }
            if (! Schema::hasColumn('cms_site_setting_translations', 'footer_logo_path')) {
                $table->string('footer_logo_path')->nullable()->after('logo_dark_path');
            }
            if (! Schema::hasColumn('cms_site_setting_translations', 'footer_background_path')) {
                $table->string('footer_background_path')->nullable()->after('footer_logo_path');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cms_site_setting_translations')) {
            return;
        }

        Schema::table('cms_site_setting_translations', function (Blueprint $table) {
            foreach (['logo_path', 'logo_dark_path', 'footer_logo_path', 'footer_background_path'] as $column) {
                if (Schema::hasColumn('cms_site_setting_translations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
