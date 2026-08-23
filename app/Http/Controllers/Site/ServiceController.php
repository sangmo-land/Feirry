<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Support\Media;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        Seo::set([
            'title' => __('site.services.title'),
            'description' => __('site.services.hero_lead'),
        ]);

        return Inertia::render('Services/Index', [
            'services' => Service::published()->get()->map->toDetail(),
            'faqs' => Faq::published()->where('group', 'services')->get()->map->toArrayLocalised(),
        ]);
    }

    public function show(Service $service): Response
    {
        abort_unless($service->is_published, 404);

        Seo::set([
            'title' => $service->meta_title ?: $service->tr('title'),
            'description' => $service->meta_description ?: $service->tr('excerpt'),
            // Absolute-ised by the layout. Passed through Media so the
            // stored disk path becomes a real URL: the raw column is
            // 'blog/covers/x.jpg' and og:image would resolve it against the
            // document root, one directory short of the file.
            'image' => Media::url($service->cover_image),
            'type' => 'article',
        ]);

        $related = Project::published()
            ->with('category')
            ->whereJsonContains('services_used', $service->slug)
            ->limit(3)
            ->get()
            ->map
            ->toCard();

        if ($related->isEmpty()) {
            $related = Project::published()->with('category')->inRandomOrder()->limit(3)->get()->map->toCard();
        }

        return Inertia::render('Services/Show', [
            'service' => $service->toDetail(),
            'relatedProjects' => $related,
            'otherServices' => Service::published()
                ->whereKeyNot($service->id)
                ->limit(5)
                ->get()
                ->map
                ->toCard(),
        ]);
    }
}
