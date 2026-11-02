<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cms_blog_categories')) {
            Schema::create('cms_blog_categories', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cms_blog_category_translations')) {
            Schema::create('cms_blog_category_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cms_blog_category_id')->constrained('cms_blog_categories')->cascadeOnDelete();
                $table->string('locale', 10);
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->unique(['cms_blog_category_id', 'locale'], 'cms_blog_cat_tr_unique');
            });
        }

        if (! Schema::hasTable('cms_blog_posts')) {
            Schema::create('cms_blog_posts', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->foreignId('cms_blog_category_id')->nullable()->constrained('cms_blog_categories')->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamp('published_at')->nullable();
                $table->string('cover_path')->nullable();
                $table->string('author_photo_path')->nullable();
                $table->string('video_url')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cms_blog_post_translations')) {
            Schema::create('cms_blog_post_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cms_blog_post_id')->constrained('cms_blog_posts')->cascadeOnDelete();
                $table->string('locale', 10);
                $table->string('title');
                $table->text('excerpt')->nullable();
                $table->longText('content')->nullable();
                $table->string('author_name')->nullable();
                $table->string('author_role')->nullable();
                $table->text('author_bio')->nullable();
                $table->text('quote')->nullable();
                $table->string('quote_author')->nullable();
                $table->string('tags')->nullable();
                $table->string('read_time')->nullable();
                $table->timestamps();

                $table->unique(['cms_blog_post_id', 'locale'], 'cms_blog_post_tr_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_blog_post_translations');
        Schema::dropIfExists('cms_blog_posts');
        Schema::dropIfExists('cms_blog_category_translations');
        Schema::dropIfExists('cms_blog_categories');
    }
};
