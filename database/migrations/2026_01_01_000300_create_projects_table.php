<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('title_fr');
            $table->string('title_en')->nullable();
            $table->string('client_name')->nullable();
            $table->string('location')->default('Douala, Cameroun');
            $table->string('city')->default('Douala');
            $table->text('excerpt_fr')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('body_fr')->nullable();
            $table->longText('body_en')->nullable();
            $table->text('challenge_fr')->nullable();
            $table->text('challenge_en')->nullable();
            $table->text('solution_fr')->nullable();
            $table->text('solution_en')->nullable();
            $table->text('outcome_fr')->nullable();
            $table->text('outcome_en')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->unsignedInteger('surface_m2')->nullable();
            $table->unsignedInteger('duration_months')->nullable();
            $table->unsignedBigInteger('budget_xaf')->nullable();
            $table->boolean('budget_public')->default(false);
            $table->unsignedSmallInteger('year')->nullable();
            $table->date('completed_at')->nullable();
            $table->string('status')->default('completed');
            $table->unsignedTinyInteger('progress')->default(100);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('services_used')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'is_featured']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
