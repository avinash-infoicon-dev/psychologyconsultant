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
                'login_hero_path' => 'string',
                'login_hero_subtitle' => 'string',
                'login_hero_title' => 'string',
                'login_hero_text' => 'text',
                'login_kicker' => 'string',
                'login_visual_title' => 'string',
                'login_visual_text' => 'text',
                'login_point_1' => 'string',
                'login_point_2' => 'string',
                'login_point_3' => 'string',
                'login_point_4' => 'string',
                'login_form_title' => 'string',
                'login_form_subtitle' => 'text',
            ];

            $after = 'footer_background_path';
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
                'login_hero_path',
                'login_hero_subtitle',
                'login_hero_title',
                'login_hero_text',
                'login_kicker',
                'login_visual_title',
                'login_visual_text',
                'login_point_1',
                'login_point_2',
                'login_point_3',
                'login_point_4',
                'login_form_title',
                'login_form_subtitle',
            ] as $column) {
                if (Schema::hasColumn('cms_site_setting_translations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
