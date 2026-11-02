<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Professional Types
        |--------------------------------------------------------------------------
        */
        Schema::create('professional_types', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('icon')->nullable();
            $table->text('description')->nullable();

            $table->boolean('status')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['status', 'sort_order']);
        });

        /*
        |--------------------------------------------------------------------------
        | Professionals
        |--------------------------------------------------------------------------
        */
        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('professional_type_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('first_name');
            $table->string('last_name')->nullable();

            $table->string('slug')->unique();

            $table->string('title')->nullable();

            $table->string('profile_image')->nullable();
            $table->string('cover_image')->nullable();

            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();

            $table->date('date_of_birth')->nullable();

            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            $table->string('license_number')->unique()->nullable();
            $table->string('specialization')->nullable();

            $table->unsignedSmallInteger('experience_years')->default(0);

            $table->decimal('consultation_fee', 10, 2)->nullable();

            $table->text('bio')->nullable();

            $table->json('languages')->nullable();
            $table->json('credentials')->nullable(); // uploaded certificate refs

            $table->string('organization')->nullable();

            $table->text('address')->nullable();

            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();

            $table->string('postal_code')->nullable();

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->boolean('online_consultation')->default(true);
            $table->boolean('offline_consultation')->default(true);

            $table->decimal('rating', 3, 2)->default(0);
            $table->unsignedInteger('total_reviews')->default(0);

            $table->boolean('is_verified')->default(false);
            $table->boolean('is_accepting_patients')->default(true);
            $table->unsignedInteger('max_active_patients')->default(40);
            $table->boolean('is_featured')->default(false);
            $table->boolean('status')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_verified']);
            $table->index(['professional_type_id', 'status']);
            $table->index('city');
            $table->index('is_featured');
        });

        /*
        |--------------------------------------------------------------------------
        | Specializations
        |--------------------------------------------------------------------------
        */
        Schema::create('specializations', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->text('description')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Professional Specialization
        |--------------------------------------------------------------------------
        */
        Schema::create('professional_specialization', function (Blueprint $table) {

            $table->foreignId('professional_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('specialization_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->primary([
                'professional_id',
                'specialization_id',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Educations
        |--------------------------------------------------------------------------
        */
        Schema::create('educations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('professional_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('degree');

            $table->string('institution');

            $table->string('field_of_study')->nullable();

            $table->year('year')->nullable();

            $table->text('description')->nullable();

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Experiences
        |--------------------------------------------------------------------------
        */
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('professional_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('organization');

            $table->string('designation');

            $table->date('start_date')->nullable();

            $table->date('end_date')->nullable();

            $table->boolean('currently_working')->default(false);

            $table->text('description')->nullable();

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('professional_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->text('description')->nullable();

            $table->decimal('price', 10, 2)->nullable();

            $table->unsignedInteger('duration')->nullable(); // minutes

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->index(['professional_id', 'status']);
        });

        /*
        |--------------------------------------------------------------------------
        | Reviews
        |--------------------------------------------------------------------------
        */
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('professional_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('reviewer_name');
            $table->string('reviewer_email')->nullable();

            $table->unsignedTinyInteger('rating');

            $table->text('review')->nullable();

            $table->boolean('is_approved')->default(false);
            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->index(['professional_id', 'is_approved', 'status']);
        });

        /*
        |--------------------------------------------------------------------------
        | Availabilities
        |--------------------------------------------------------------------------
        */
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('professional_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('day', [
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday',
            ]);

            $table->time('start_time');
            $table->time('end_time');

            $table->boolean('is_available')->default(true);

            $table->timestamps();

            $table->unique(['professional_id', 'day', 'start_time'], 'availabilities_slot_unique');
        });

        /*
        |--------------------------------------------------------------------------
        | Awards
        |--------------------------------------------------------------------------
        */
        Schema::create('awards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('professional_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('organization')->nullable();

            $table->year('year')->nullable();

            $table->text('description')->nullable();

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Appointments
        |--------------------------------------------------------------------------
        */
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('professional_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();

            $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->nullOnDelete();

            $table->date('appointment_date');
            $table->time('appointment_time');

            $table->enum('consultation_type', [
                'online',
                'offline',
            ])->default('online');

            $table->enum('status', [
                'pending',
                'confirmed',
                'completed',
                'cancelled',
            ])->default('pending');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['professional_id', 'appointment_date']);
            $table->index('status');
        });

        /*
        |--------------------------------------------------------------------------
        | Professional Galleries
        |--------------------------------------------------------------------------
        */
        Schema::create('professional_galleries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('professional_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('image');

            $table->string('title')->nullable();

            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->index(['professional_id', 'status', 'sort_order'], 'galleries_professional_status_sort_index');
        });

        /*
        |--------------------------------------------------------------------------
        | Professional Documents
        |--------------------------------------------------------------------------
        */
        Schema::create('professional_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('professional_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->string('document');

            $table->string('document_type')->nullable(); // PDF, JPG, License

            $table->boolean('is_verified')->default(false);

            $table->boolean('status')->default(true);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Professional Social Links
        |--------------------------------------------------------------------------
        */
        Schema::create('professional_social_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('professional_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('platform'); // Facebook, LinkedIn, Instagram

            $table->string('url');

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->unique(['professional_id', 'platform'], 'social_links_professional_platform_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Child tables are dropped before their parents so the foreign keys
     * do not block the rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_social_links');
        Schema::dropIfExists('professional_documents');
        Schema::dropIfExists('professional_galleries');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('awards');
        Schema::dropIfExists('availabilities');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('services');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('educations');
        Schema::dropIfExists('professional_specialization');
        Schema::dropIfExists('specializations');
        Schema::dropIfExists('professionals');
        Schema::dropIfExists('professional_types');
    }
};
