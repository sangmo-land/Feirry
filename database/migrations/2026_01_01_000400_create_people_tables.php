<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('role_fr');
            $table->string('role_en')->nullable();
            $table->text('bio_fr')->nullable();
            $table->text('bio_en')->nullable();
            $table->string('photo')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('linkedin')->nullable();
            $table->json('expertise')->nullable();
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_leadership')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('author_name');
            $table->string('author_role_fr')->nullable();
            $table->string('author_role_en')->nullable();
            $table->string('company')->nullable();
            $table->string('photo')->nullable();
            $table->text('content_fr');
            $table->text('content_en')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->string('type')->default('client');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('issuer')->nullable();
            $table->string('logo')->nullable();
            $table->text('description_fr')->nullable();
            $table->text('description_en')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certifications');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('team_members');
    }
};
