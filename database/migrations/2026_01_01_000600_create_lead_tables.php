<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->unique();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('project_type')->nullable();
            $table->string('budget_range')->nullable();
            $table->string('timeline')->nullable();
            $table->string('location')->nullable();
            $table->unsignedInteger('surface_m2')->nullable();
            $table->text('message')->nullable();
            $table->string('attachment')->nullable();
            $table->string('status')->default('new');
            $table->string('source')->default('website');
            $table->text('internal_notes')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('ip_address')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });

        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_fr');
            $table->string('title_en')->nullable();
            $table->string('department')->nullable();
            $table->string('location')->default('Douala, Cameroun');
            $table->string('contract_type')->default('CDI');
            $table->string('experience_level')->nullable();
            $table->longText('description_fr')->nullable();
            $table->longText('description_en')->nullable();
            $table->json('requirements_fr')->nullable();
            $table->json('requirements_en')->nullable();
            $table->json('benefits_fr')->nullable();
            $table->json('benefits_en')->nullable();
            $table->string('salary_range')->nullable();
            $table->date('closes_at')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_opening_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('cv_path')->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('linkedin')->nullable();
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->string('status')->default('received');
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('locale')->default('fr');
            $table->boolean('is_active')->default(true);
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_openings');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('quote_requests');
    }
};
