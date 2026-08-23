<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Support\Media;
use App\Support\Seo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function show(Project $project): Response
    {
        abort_unless($project->is_published, 404);

        Seo::set([
            'title' => $project->meta_title ?: $project->tr('title'),
            'description' => $project->meta_description ?: $project->tr('excerpt'),
            // Absolute-ised by the layout. Passed through Media so the
            // stored disk path becomes a real URL: the raw column is
            // 'blog/covers/x.jpg' and og:image would resolve it against the
            // document root, one directory short of the file.
            'image' => Media::url($project->cover_image),
            'type' => 'article',
        ]);

        $project->load(['category', 'testimonials' => fn ($q) => $q->published()]);

        $next = Project::published()
            ->where('id', '>', $project->id)
            ->orderBy('id')
            ->with('category')
            ->first()
            ?? Project::published()->with('category')->whereKeyNot($project->id)->orderBy('id')->first();

        return Inertia::render('Projects/Show', [
            'project' => $project->toDetail(),
            'testimonial' => $project->testimonials->first()?->toArrayLocalised(),
            'similar' => Project::published()
                ->with('category')
                ->where('project_category_id', $project->project_category_id)
                ->whereKeyNot($project->id)
                ->limit(3)
                ->get()
                ->map
                ->toCard(),
            'nextProject' => $next?->toCard(),
        ]);
    }

    public function index(Request $request): Response
    {
        Seo::set([
            'title' => __('site.projects.title'),
            'description' => __('site.projects.hero_lead'),
        ]);

        $category = $request->string('categorie')->toString();
        $status = $request->string('statut')->toString();

        $projects = Project::published()
            ->with('category')
            ->when($category, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $category)))
            ->when(in_array($status, Project::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->orderByDesc('is_featured')
            ->orderByDesc('year')
            ->orderBy('position')
            ->get()
            ->map
            ->toCard();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'categories' => ProjectCategory::withCount(['projects' => fn ($q) => $q->published()])
                ->orderBy('position')
                ->get()
                ->map
                ->toArrayLocalised(),
            'filters' => ['categorie' => $category ?: null, 'statut' => $status ?: null],
        ]);
    }
}
