<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The opening block of every fixed page.
 *
 * Project, service, post and job pages already draw their hero from their own
 * record. This covers the pages that had no record behind them — the home page
 * and the section landings — whose headline and photograph were previously
 * frozen in the translation files and in a constant in the front end, and so
 * could not be changed without a deploy.
 *
 * Rows are seeded once per page and edited in place; the admin cannot add or
 * remove them, because each one answers to a route that exists in code.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title_fr')->nullable();
            $table->string('title_en')->nullable();
            // The home page sets its second line in brand orange; every other
            // page runs a single line and leaves this empty.
            $table->string('title_accent_fr')->nullable();
            $table->string('title_accent_en')->nullable();
            $table->text('lead_fr')->nullable();
            $table->text('lead_en')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_heroes');
    }
};
