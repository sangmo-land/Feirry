<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| Cahier des charges V7, §07 to §09: the content model the new site needs.
|
| Projects gain the fields that let a sheet say exactly what FEIRRY did
| (roles), how far the work is (stage), which filter family it belongs to and
| whether it may be shown at all (visibility, photo rights). Offers and the
| Coup de Pouce campaign become administrable records, and every lead keeps
| its documents on the private disk.
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // architecture | construction | design | suivi
            $table->json('families')->nullable()->after('project_category_id');
            // design | build | follow | expert
            $table->json('roles')->nullable()->after('families');
            // design | works | done | tbc
            $table->string('stage')->default('tbc')->after('roles');
            $table->string('stage_note_fr')->nullable()->after('stage');
            $table->string('stage_note_en')->nullable()->after('stage_note_fr');
            $table->string('programme_fr')->nullable()->after('stage_note_en');
            $table->string('programme_en')->nullable()->after('programme_fr');
            $table->string('mission_fr')->nullable()->after('programme_en');
            $table->string('mission_en')->nullable()->after('mission_fr');
            $table->string('period')->nullable()->after('mission_en');
            $table->json('deliverables_fr')->nullable()->after('period');
            $table->json('deliverables_en')->nullable()->after('deliverables_fr');
            $table->string('cover_position')->default('center 50%')->after('cover_image');
            $table->boolean('visual_is_provisional')->default(true)->after('cover_position');
            $table->boolean('photo_rights_confirmed')->default(false)->after('visual_is_provisional');
            $table->string('photo_credits')->nullable()->after('photo_rights_confirmed');
            // draft | published | archived
            $table->string('visibility')->default('published')->after('is_published');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('family_fr')->nullable()->after('title_en');
            $table->string('family_en')->nullable()->after('family_fr');
            $table->string('cover_position')->default('center 50%')->after('cover_image');
            $table->json('situations_fr')->nullable()->after('features_en');
            $table->json('situations_en')->nullable()->after('situations_fr');
            $table->json('deliverables_fr')->nullable()->after('situations_en');
            $table->json('deliverables_en')->nullable()->after('deliverables_fr');
            $table->text('limits_fr')->nullable()->after('deliverables_en');
            $table->text('limits_en')->nullable()->after('limits_fr');
            // The need pre-selected in the quote form when a visitor comes from here.
            $table->string('quote_need')->default('conception')->after('limits_en');
        });

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_fr');
            $table->string('title_en')->nullable();
            $table->string('subtitle_fr')->nullable();
            $table->string('subtitle_en')->nullable();
            $table->string('audience_fr')->nullable();
            $table->string('audience_en')->nullable();
            $table->json('included_fr')->nullable();
            $table->json('included_en')->nullable();
            $table->json('excluded_fr')->nullable();
            $table->json('excluded_en')->nullable();
            $table->json('steps_fr')->nullable();
            $table->json('steps_en')->nullable();
            $table->json('price_factors_fr')->nullable();
            $table->json('price_factors_en')->nullable();
            $table->string('price_label_fr')->default('Sur devis');
            $table->string('price_label_en')->default('On request');
            $table->string('cta_fr')->nullable();
            $table->string('cta_en')->nullable();
            $table->string('quote_need')->default('conception');
            $table->boolean('is_recommended')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // draft | open | closed — only "open" inside its dates accepts requests.
            $table->string('status')->default('draft');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->boolean('show_capacity')->default(false);
            $table->string('territory_fr')->nullable();
            $table->string('territory_en')->nullable();
            $table->text('terms_fr')->nullable();
            $table->text('terms_en')->nullable();
            $table->boolean('consultation_enabled')->default(true);
            $table->boolean('visit_enabled')->default(true);
            $table->boolean('documents_required')->default(false);
            $table->timestamps();
        });

        Schema::create('assistance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->unique();
            // consultation | visit
            $table->string('path');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('project_type')->nullable();
            $table->text('description')->nullable();
            // office | video (consultation)
            $table->string('meeting_mode')->nullable();
            // visit
            $table->string('site_location')->nullable();
            $table->text('objective')->nullable();
            $table->text('access_constraints')->nullable();
            $table->string('availability')->nullable();
            $table->json('attachments')->nullable();
            $table->string('locale', 5)->default('fr');
            $table->timestamp('privacy_accepted_at')->nullable();
            $table->string('status')->default('new');
            $table->text('internal_notes')->nullable();
            $table->string('fingerprint', 64)->nullable()->index();
            $table->timestamps();
            $table->index('status');
        });

        Schema::table('quote_requests', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('need')->nullable()->after('service_id');
            $table->string('offer')->nullable()->after('need');
            $table->string('programme')->nullable()->after('location');
            $table->string('phase')->nullable()->after('programme');
            $table->string('land_status')->nullable()->after('phase');
            $table->string('size')->nullable()->after('land_status');
            $table->string('contact_preference')->nullable()->after('size');
            $table->json('attachments')->nullable()->after('attachment');
            $table->string('locale', 5)->default('fr')->after('source');
            $table->timestamp('privacy_accepted_at')->nullable()->after('locale');
            $table->string('fingerprint', 64)->nullable()->index()->after('privacy_accepted_at');
        });
    }

    public function down(): void
    {
        Schema::table('quote_requests', function (Blueprint $table) {
            $table->dropColumn(['need', 'offer', 'programme', 'phase', 'land_status', 'size', 'contact_preference', 'attachments', 'locale', 'privacy_accepted_at', 'fingerprint']);
        });
        Schema::dropIfExists('assistance_requests');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('offers');
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['family_fr', 'family_en', 'cover_position', 'situations_fr', 'situations_en', 'deliverables_fr', 'deliverables_en', 'limits_fr', 'limits_en', 'quote_need']);
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['families', 'roles', 'stage', 'stage_note_fr', 'stage_note_en', 'programme_fr', 'programme_en', 'mission_fr', 'mission_en', 'period', 'deliverables_fr', 'deliverables_en', 'cover_position', 'visual_is_provisional', 'photo_rights_confirmed', 'photo_credits', 'visibility']);
        });
    }
};
