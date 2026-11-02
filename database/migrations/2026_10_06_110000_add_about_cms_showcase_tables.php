<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('cms_pages', 'template')) {
            Schema::table('cms_pages', function (Blueprint $table) {
                $table->string('template', 50)->default('default')->after('slug');
            });
        }

        if (! Schema::hasColumn('cms_page_translations', 'layout')) {
            Schema::table('cms_page_translations', function (Blueprint $table) {
                $table->json('layout')->nullable()->after('meta_description');
            });
        }

        if (! Schema::hasTable('cms_testimonials')) {
            Schema::create('cms_testimonials', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->string('photo_path')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cms_testimonial_translations')) {
            Schema::create('cms_testimonial_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cms_testimonial_id')->constrained('cms_testimonials')->cascadeOnDelete();
                $table->string('locale', 10);
                $table->string('author_name');
                $table->string('author_role')->nullable();
                $table->text('quote');
                $table->timestamps();

                $table->unique(['cms_testimonial_id', 'locale']);
            });
        }

        if (! Schema::hasTable('cms_team_members')) {
            Schema::create('cms_team_members', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->string('photo_path')->nullable();
                $table->string('profile_url')->nullable();
                $table->string('facebook_url')->nullable();
                $table->string('twitter_url')->nullable();
                $table->string('linkedin_url')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cms_team_member_translations')) {
            Schema::create('cms_team_member_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cms_team_member_id')->constrained('cms_team_members')->cascadeOnDelete();
                $table->string('locale', 10);
                $table->string('name');
                $table->string('role')->nullable();
                $table->timestamps();

                $table->unique(['cms_team_member_id', 'locale']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_team_member_translations');
        Schema::dropIfExists('cms_team_members');
        Schema::dropIfExists('cms_testimonial_translations');
        Schema::dropIfExists('cms_testimonials');

        Schema::table('cms_page_translations', function (Blueprint $table) {
            $table->dropColumn('layout');
        });

        Schema::table('cms_pages', function (Blueprint $table) {
            $table->dropColumn('template');
        });
    }
};
