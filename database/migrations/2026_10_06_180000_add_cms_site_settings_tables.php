<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cms_site_settings')) {
            Schema::create('cms_site_settings', function (Blueprint $table) {
                $table->id();
                $table->string('logo_path')->nullable();
                $table->string('logo_dark_path')->nullable();
                $table->string('footer_logo_path')->nullable();
                $table->string('footer_background_path')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cms_site_setting_translations')) {
            Schema::create('cms_site_setting_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cms_site_setting_id')->constrained('cms_site_settings')->cascadeOnDelete();
                $table->string('locale', 10);
                $table->string('brand')->nullable();
                $table->string('tagline')->nullable();
                $table->string('offcanvas_info_title')->nullable();
                $table->string('availability')->nullable();
                $table->string('footer_links_title')->nullable();
                $table->string('help_title')->nullable();
                $table->string('help_phone_label')->nullable();
                $table->string('help_email_label')->nullable();
                $table->string('newsletter_title')->nullable();
                $table->text('newsletter_text')->nullable();
                $table->string('copyright')->nullable();
                $table->timestamps();

                $table->unique(['cms_site_setting_id', 'locale']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_site_setting_translations');
        Schema::dropIfExists('cms_site_settings');
    }
};
