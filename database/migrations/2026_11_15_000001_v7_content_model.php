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

        /*
        | Coup de Pouce FEIRRY (CDC Coup de Pouce V2, §08, §12, §16).
        | Every value that may change lives here, never in the components.
        */
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            // Edition label, also the storage folder name (e.g. 2026-fin-annee).
            $table->string('edition');
            // draft (préparation) | open | paused (en pause / complet) | closed (édition terminée)
            $table->string('status')->default('draft');
            // Public launch date and intervention window; never hard-coded.
            $table->date('announced_at')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            // Places announced to the public, per week.
            $table->unsignedSmallInteger('public_consultations')->nullable();
            $table->unsignedSmallInteger('public_visits')->nullable();
            // Internal capacity: never published.
            $table->unsignedSmallInteger('internal_consultations')->nullable();
            $table->unsignedSmallInteger('internal_visits')->nullable();
            // While paused, keep the forms open as an explicit waiting list.
            $table->boolean('waitlist')->default(false);
            $table->string('zone_fr')->nullable();
            $table->string('zone_en')->nullable();
            // Pilot zone used to flag out-of-zone visit requests (comma-separated).
            $table->string('visit_zone_keywords')->nullable();
            $table->string('banner_fr')->nullable();
            $table->string('banner_en')->nullable();
            $table->text('terms_fr')->nullable();
            $table->text('terms_en')->nullable();
            $table->text('confirmation_fr')->nullable();
            $table->text('confirmation_en')->nullable();
            $table->string('support_whatsapp')->nullable();
            $table->boolean('consultation_enabled')->default(true);
            $table->boolean('visit_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('assistance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            // Public reference, non-sequential (CDP-26-K7Q9T3); never an access key.
            $table->string('reference')->unique();
            // Client-generated key: a resent submission returns the same request.
            $table->string('idempotency_key', 64)->nullable()->unique();
            // consultation (fiche A) | visit (fiche B)
            $table->string('path');
            // web | office (registered by an agent)
            $table->string('source')->default('web');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('project_type')->nullable();
            // A: idee | plans | chantier | termine — B: fondations | elevation | toiture | finitions | autre
            $table->string('progress')->nullable();
            $table->string('land_area')->nullable();
            $table->string('footprint_area')->nullable();
            $table->string('levels')->nullable();
            $table->text('description')->nullable();
            // office | video (consultation)
            $table->string('meeting_mode')->nullable();
            // Visit: neighbourhood + landmark only, GPS optional; never public.
            $table->string('site_location')->nullable();
            $table->string('gps')->nullable();
            $table->text('access_constraints')->nullable();
            $table->string('availability')->nullable();
            $table->boolean('danger_reported')->default(false);
            $table->boolean('out_of_zone')->default(false);
            $table->json('attachments')->nullable();
            $table->string('locale', 5)->default('fr');
            // Separate consents (CDC §15, §23): contact for the request vs. prospection.
            $table->timestamp('privacy_accepted_at')->nullable();
            $table->string('notice_version')->nullable();
            $table->boolean('marketing_consent')->default(false);
            $table->timestamp('marketing_consent_at')->nullable();
            // received | incomplete | studying | waiting | scheduled | done | report_sent | closed | out_of_scope
            $table->string('status')->default('received');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('decision_reason')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->string('scheduled_mode')->nullable();
            $table->string('scheduled_team')->nullable();
            $table->date('report_due_at')->nullable();
            $table->timestamp('report_sent_at')->nullable();
            $table->json('reports')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('fingerprint', 64)->nullable()->index();
            $table->timestamps();
            $table->index(['status', 'path']);
        });

        // Who did what on a request; never a copy of the personal data itself.
        Schema::create('assistance_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assistance_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('detail')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('users', function (Blueprint $table) {
            // admin | administration | referent | communication (CDC §10)
            $table->string('role')->default('admin')->after('email');
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
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('role'));
        Schema::dropIfExists('assistance_audit_logs');
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
