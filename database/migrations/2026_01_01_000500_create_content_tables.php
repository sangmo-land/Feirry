<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('title_fr');
            $table->string('title_en')->nullable();
            $table->text('excerpt_fr')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('body_fr')->nullable();
            $table->longText('body_en')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedSmallInteger('reading_minutes')->default(4);
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general');
            $table->string('question_fr');
            $table->string('question_en')->nullable();
            $table->text('answer_fr');
            $table->text('answer_en')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('title_fr');
            $table->string('title_en')->nullable();
            $table->text('description_fr')->nullable();
            $table->text('description_en')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('process_steps', function (Blueprint $table) {
            $table->id();
            $table->string('title_fr');
            $table->string('title_en')->nullable();
            $table->text('description_fr')->nullable();
            $table->text('description_en')->nullable();
            $table->string('icon')->default('compass');
            $table->string('duration_fr')->nullable();
            $table->string('duration_en')->nullable();
            $table->json('deliverables_fr')->nullable();
            $table->json('deliverables_en')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group')->default('general');
            $table->longText('value')->nullable();
            $table->string('type')->default('string');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('process_steps');
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('posts');
    }
};
