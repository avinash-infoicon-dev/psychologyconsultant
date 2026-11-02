<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_category_id')->constrained('cms_categories')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['cms_category_id', 'locale']);
        });

        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('cms_category_id')->nullable()->constrained('cms_categories')->nullOnDelete();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_page_id')->constrained('cms_pages')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['cms_page_id', 'locale']);
        });

        Schema::create('cms_menus', function (Blueprint $table) {
            $table->id();
            $table->string('location', 50)->default('header');
            $table->foreignId('parent_id')->nullable()->constrained('cms_menus')->nullOnDelete();
            /** page | category | url | route */
            $table->string('type', 20);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->string('target', 20)->default('_self');
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['location', 'is_active', 'sort_order']);
            $table->index(['type', 'reference_id']);
        });

        Schema::create('cms_menu_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_menu_id')->constrained('cms_menus')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('title');
            $table->timestamps();

            $table->unique(['cms_menu_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_menu_translations');
        Schema::dropIfExists('cms_menus');
        Schema::dropIfExists('cms_page_translations');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('cms_category_translations');
        Schema::dropIfExists('cms_categories');
    }
};
