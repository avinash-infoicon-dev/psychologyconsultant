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
            $columns = [
                'register_hero_path' => 'string',
                'register_hero_subtitle' => 'string',
                'register_hero_title' => 'string',
                'register_hero_text' => 'text',
                'register_kicker' => 'string',
                'register_heading' => 'string',
                'register_intro' => 'text',
                'register_form_title' => 'string',
                'register_form_subtitle' => 'text',
            ];

            $after = Schema::hasColumn('cms_site_setting_translations', 'login_form_subtitle')
                ? 'login_form_subtitle'
                : 'footer_background_path';

            foreach ($columns as $name => $type) {
                if (Schema::hasColumn('cms_site_setting_translations', $name)) {
                    continue;
                }
                if ($type === 'text') {
                    $table->text($name)->nullable()->after($after);
                } else {
                    $table->string($name)->nullable()->after($after);
                }
                $after = $name;
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cms_site_setting_translations')) {
            return;
        }

        Schema::table('cms_site_setting_translations', function (Blueprint $table) {
            foreach ([
                'register_hero_path',
                'register_hero_subtitle',
                'register_hero_title',
                'register_hero_text',
                'register_kicker',
                'register_heading',
                'register_intro',
                'register_form_title',
                'register_form_subtitle',
            ] as $column) {
                if (Schema::hasColumn('cms_site_setting_translations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
