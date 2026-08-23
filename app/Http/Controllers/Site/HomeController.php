<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\Milestone;
use App\Models\Partner;
use App\Models\Post;
use App\Models\ProcessStep;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        Seo::set([
            'title' => null,
            'description' => __('site.home.hero_lead', ['year' => config('site.brand.founded')]),
            'schema' => Seo::organizationSchema(),
        ]);

        return Inertia::render('Home', [
            'services' => Service::published()->limit(6)->get()->map->toCard(),
            'featuredProjects' => Project::published()
                ->with('category')
                ->where('is_featured', true)
                ->orderBy('position')
                ->limit(6)
                ->get()
                ->map
                ->toCard(),
            'processSteps' => ProcessStep::published()->get()->map->toArrayLocalised(),
            'testimonials' => Testimonial::published()
                ->with('project')
                ->where('is_featured', true)
                ->limit(6)
                ->get()
                ->map
                ->toArrayLocalised(),
            'posts' => Post::published()
                ->with(['category', 'author'])
                ->latest('published_at')
                ->limit(3)
                ->get()
                ->map
                ->toCard(),
            'partners' => Partner::published()->get(['id', 'name', 'logo', 'website']),
            'certifications' => Certification::published()->limit(4)->get()->map->toArrayLocalised(),
            'milestones' => Milestone::published()->get()->map->toArrayLocalised(),
        ]);
    }
}
