<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\Faq;
use App\Models\Milestone;
use App\Models\Partner;
use App\Models\ProcessStep;
use App\Models\Project;
use App\Models\TeamMember;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function about(): Response
    {
        Seo::set([
            'title' => __('site.about.title'),
            'description' => __('site.about.hero_lead'),
            // This is the page that carries the office address, the opening
            // hours and the map. None of it exists in the response until React
            // has run, so it is also written out server-side.
            'noscript' => 'partials.location-noscript',
        ]);

        /*
         * The richer graph, in place of the organisation node every other page
         * gets: it names the location section, ties the pin and the opening
         * hours to it, and addresses it at the fragment the section renders
         * with. Set after the page, because it quotes the page's own title.
         */
        Seo::schema(Seo::locationSchema());

        return Inertia::render('About', [
            'milestones' => Milestone::published()->get()->map->toArrayLocalised(),
            'certifications' => Certification::published()->get()->map->toArrayLocalised(),
            'leadership' => TeamMember::published()->where('is_leadership', true)->get()->map->toArrayLocalised(),
            'partners' => Partner::published()->get(['id', 'name', 'logo', 'website']),
            'projectCount' => Project::published()->count(),
        ]);
    }

    public function process(): Response
    {
        Seo::set([
            'title' => __('site.process.title'),
            'description' => __('site.process.hero_lead'),
        ]);

        return Inertia::render('Process', [
            'steps' => ProcessStep::published()->get()->map->toArrayLocalised(),
            'faqs' => Faq::published()->where('group', 'process')->get()->map->toArrayLocalised(),
        ]);
    }

    public function team(): Response
    {
        Seo::set([
            'title' => __('site.team.title'),
            'description' => __('site.team.hero_lead'),
        ]);

        $members = TeamMember::published()->get();

        return Inertia::render('Team', [
            'leadership' => $members->where('is_leadership', true)->values()->map->toArrayLocalised(),
            'members' => $members->where('is_leadership', false)->values()->map->toArrayLocalised(),
        ]);
    }

    public function faq(): Response
    {
        Seo::set([
            'title' => __('site.faq.title'),
            'description' => __('site.faq.hero_lead'),
        ]);

        $faqs = Faq::published()->get();

        return Inertia::render('Faq', [
            'groups' => $faqs
                ->groupBy('group')
                ->map(fn ($items, $group) => [
                    'key' => $group,
                    'items' => $items->map->toArrayLocalised()->values(),
                ])
                ->values(),
        ]);
    }
}
