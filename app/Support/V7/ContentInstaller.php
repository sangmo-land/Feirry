<?php

namespace App\Support\V7;

use App\Models\Campaign;
use App\Models\Offer;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Installs the content of the validated V7 mock-up.
 *
 * Nothing is deleted. The demonstration records the first build shipped with
 * (invented services, projects, team, testimonials, articles, job offers…) are
 * unpublished, so they disappear from the public site while staying in the
 * admin, where FEIRRY can restore anything worth keeping. V7 records are
 * matched on their slug, so running this twice changes nothing.
 */
class ContentInstaller
{
    public static function install(): void
    {
        $data = require database_path('data/v7_content.php');

        DB::transaction(function () use ($data) {
            static::services($data['services']);
            static::projects($data['projects']);
            static::offers($data['offers'], $data);
            static::campaign();
            static::hideDemonstrationContent();
            static::settings();
        });
    }

    protected static function services(array $services): void
    {
        $slugs = array_column($services, 'slug');

        Service::whereNotIn('slug', $slugs)->update(['is_published' => false, 'is_featured' => false]);

        foreach ($services as $position => $service) {
            Service::updateOrCreate(
                ['slug' => $service['slug']],
                $service + ['position' => $position + 1, 'is_published' => true, 'is_featured' => true],
            );
        }
    }

    protected static function projects(array $projects): void
    {
        $slugs = array_column($projects, 'slug');

        Project::whereNotIn('slug', $slugs)->update([
            'is_published' => false,
            'is_featured' => false,
            'visibility' => 'archived',
        ]);

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['slug' => $project['slug']],
                $project + [
                    'is_published' => true,
                    'visibility' => 'published',
                    'visual_is_provisional' => true,
                    // Unverified on the old records; never shown on V7 sheets.
                    'client_name' => null,
                    'budget_xaf' => null,
                    'budget_public' => false,
                    'surface_m2' => null,
                    'year' => null,
                    'status' => 'ongoing',
                    'progress' => 0,
                ],
            );
        }
    }

    protected static function offers(array $offers, array $data): void
    {
        foreach ($offers as $offer) {
            Offer::updateOrCreate(['slug' => $offer['slug']], $offer + [
                'price_factors_fr' => $data['price_factors_fr'],
                'price_factors_en' => $data['price_factors_en'],
                'is_published' => true,
            ]);
        }
    }

    /**
     * The 2026 pilot edition, as set in the Coup de Pouce specification
     * (17 Nov 2026): prepared, not open. FEIRRY opens it from the admin once
     * the go/no-go is given; every value below stays editable there.
     */
    protected static function campaign(): void
    {
        if (Campaign::query()->exists()) {
            return;
        }

        Campaign::create([
            'slug' => 'edition-2026-fin-annee',
            'name' => 'Coup de Pouce FEIRRY — édition pilote 2026',
            'edition' => '2026-fin-annee',
            'status' => 'draft',
            'announced_at' => '2026-11-20',
            'starts_at' => '2026-11-23',
            'ends_at' => '2026-12-19',
            'public_consultations' => 10,
            'public_visits' => 5,
            'internal_consultations' => 20,
            'internal_visits' => 20,
            'zone_fr' => 'Douala',
            'zone_en' => 'Douala',
            'visit_zone_keywords' => 'douala',
            'banner_fr' => 'Édition fin d’année 2026',
            'banner_en' => '2026 end-of-year edition',
            'terms_fr' => "Orientation technique préliminaire gratuite, sans engagement d’achat ni devis exigé.\nLes demandes sont étudiées par l’équipe FEIRRY : places limitées, sélection et confirmation par FEIRRY. L’envoi d’une demande ne réserve aucun créneau.\nConsultations au bureau le lundi (ou en visioconférence) ; visites de chantier le mercredi et le jeudi, à Douala.\nUn rapport préliminaire est remis après l’intervention. Il ne remplace pas une étude complète ni une mission réglementaire.\nRefuser la prospection commerciale ou la publication de photos ne change rien à l’accès au programme.",
            'terms_en' => "Free preliminary technical guidance, with no purchase commitment and no quote required.\nRequests are reviewed by the FEIRRY team: places are limited, subject to selection and confirmation by FEIRRY. Sending a request does not book a slot.\nConsultations at the office on Mondays (or by video call); site visits on Wednesdays and Thursdays, in Douala.\nA preliminary report is provided after the intervention. It does not replace a full study or a regulatory mission.\nDeclining marketing or photo publication does not affect access to the programme.",
            'consultation_enabled' => true,
            'visit_enabled' => true,
        ]);
    }

    protected static function hideDemonstrationContent(): void
    {
        foreach (['testimonials', 'partners', 'certifications', 'milestones', 'team_members', 'faqs', 'job_openings', 'posts', 'process_steps'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            if (Schema::hasColumn($table, 'is_published')) {
                DB::table($table)->update(['is_published' => false]);
            } elseif (Schema::hasColumn($table, 'status')) {
                DB::table($table)->update(['status' => 'draft']);
            }
        }
    }

    /** Overwrite the invented identity values the first build mirrored into settings. */
    protected static function settings(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        foreach (['brand.legal_name', 'brand.tagline', 'contact.address', 'contact.city', 'contact.phone', 'contact.email', 'contact.whatsapp', 'contact.hours_fr', 'contact.hours_en', 'contact.maps_url', 'contact.latitude', 'contact.longitude'] as $key) {
            Setting::put($key, config("site.{$key}"), str_starts_with($key, 'brand') ? 'brand' : 'contact');
        }

        // Social links: invented handles are cleared until FEIRRY sets the real ones.
        foreach (array_keys(config('site.social')) as $network) {
            Setting::put("social.{$network}", '', 'social');
        }
    }
}
