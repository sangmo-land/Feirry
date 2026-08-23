<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_fr');
            $table->string('title_en')->nullable();
            $table->string('tagline_fr')->nullable();
            $table->string('tagline_en')->nullable();
            $table->text('excerpt_fr')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('body_fr')->nullable();
            $table->longText('body_en')->nullable();
            $table->string('icon')->default('building-2');
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('features_fr')->nullable();
            $table->json('features_en')->nullable();
            $table->string('starting_price')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
