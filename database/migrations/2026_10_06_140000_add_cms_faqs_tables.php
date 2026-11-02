<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cms_faqs')) {
            Schema::create('cms_faqs', function (Blueprint $table) {
                $table->id();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cms_faq_translations')) {
            Schema::create('cms_faq_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cms_faq_id')->constrained('cms_faqs')->cascadeOnDelete();
                $table->string('locale', 10);
                $table->string('question');
                $table->text('answer');
                $table->timestamps();

                $table->unique(['cms_faq_id', 'locale']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_faq_translations');
        Schema::dropIfExists('cms_faqs');
    }
};
