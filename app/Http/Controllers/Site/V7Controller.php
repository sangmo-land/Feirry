<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Offer;
use App\Models\Post;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Support\Media;
use App\Support\Seo;
use App\Support\V7\LeadFiles;
use App\Support\V7\LeadNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The eight sections of the V7 site (CDC §02). Every page reads its content
 * from the admin-managed tables; nothing on these pages is hard-coded data.
 */
class V7Controller extends Controller
{
    public function home(): Response
    {
        $this->seo('home', '/storage/v7/japoma5-1280.webp');
        Seo::schema(Seo::organizationSchema());

        return Inertia::render('V7/Home', [
            'expertises' => $this->expertiseList(),
            'projects' => $this->projectList(),
            'offers' => $this->offerList(),
            'posts' => $this->postList(3),
            'campaign' => $this->campaignData(),
        ]);
    }

    public function feirry(): Response
    {
        $this->seo('feirry');

        return Inertia::render('V7/Feirry');
    }

    public function expertises(): Response
    {
        $this->seo('expertises', Media::url(Service::published()->value('cover_image')), '(max-width: 767px) 100vw, 33vw');

        return Inertia::render('V7/Expertises', ['expertises' => $this->expertiseList(detail: true)]);
    }

    public function expertise(Service $service): Response
    {
        abort_unless($service->is_published, 404);

        Seo::set([
            'title' => $service->meta_title ?: $service->tr('title'),
            'description' => $service->meta_description ?: $service->tr('excerpt').' '.$service->tr('body'),
            'image' => Media::url($service->cover_image),
            'preload' => Media::url($service->cover_image),
            'preload_sizes' => '(max-width: 980px) 100vw, 45vw',
        ]);

        $all = Service::published()->get();

        return Inertia::render('V7/Expertise', [
            'expertise' => $service->toDetail(),
            'others' => $all->reject(fn ($s) => $s->is($service))->map->toCard()->values(),
        ]);
    }

    public function projets(): Response
    {
        $first = Project::published()->orderBy('position')->first();
        $this->seo('projets', Media::url($first?->cover_image), '(max-width: 980px) 100vw, 60vw');

        return Inertia::render('V7/Projets', ['projects' => $this->projectList()]);
    }

    public function projet(Project $project): Response
    {
        abort_unless($project->is_published && $project->visibility === 'published', 404);

        Seo::set([
            'title' => $project->meta_title ?: $project->tr('title'),
            'description' => $project->meta_description ?: trim($project->tr('title').' — '.$project->location.'. '.$project->tr('programme')),
            'image' => Media::url($project->cover_image),
            'type' => 'article',
            'preload' => Media::url($project->cover_image),
        ]);

        $projects = Project::published()->orderBy('position')->get();
        $index = $projects->search(fn ($p) => $p->is($project));
        $next = $projects->get(($index + 1) % max(1, $projects->count()));

        return Inertia::render('V7/Projet', [
            'project' => $project->toDetail(),
            'next' => $next && ! $next->is($project) ? $next->toCard() : null,
        ]);
    }

    public function offres(): Response
    {
        $this->seo('offres');

        return Inertia::render('V7/Offres', ['offers' => $this->offerList()]);
    }

    public function conseils(): Response
    {
        $this->seo('conseils');

        return Inertia::render('V7/Conseils', ['posts' => $this->postList()]);
    }

    public function monProjet(Request $request): Response
    {
        $this->seo('projet');

        return Inertia::render('V7/MonProjet', [
            'offers' => $this->offerList(),
            'preset' => [
                'need' => in_array($request->query('besoin'), StoreProjectRequest::NEEDS, true) ? $request->query('besoin') : null,
                'offer' => Offer::published()->where('slug', $request->query('offre'))->value('slug'),
            ],
        ]);
    }

    public function storeProjet(StoreProjectRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['attachments', 'website', 'privacy']);

        // Anti-duplicate: the same request re-sent within ten minutes returns
        // the reference already issued instead of creating a second lead.
        $fingerprint = hash('sha256', mb_strtolower(implode('|', [$data['need'], $data['location'], $data['phone'] ?? '', $data['email'] ?? '', $data['name']])));
        $existing = QuoteRequest::where('fingerprint', $fingerprint)->where('created_at', '>=', now()->subMinutes(10))->first();

        if ($existing) {
            return back()->with(['reference' => $existing->reference, 'success' => 'project']);
        }

        $quote = QuoteRequest::create($data + [
            'project_type' => $data['need'],
            'source' => 'website',
            'locale' => app()->getLocale(),
            'privacy_accepted_at' => now(),
            'fingerprint' => $fingerprint,
        ]);

        $quote->update(['attachments' => LeadFiles::store($request->file('attachments', []), "leads/projets/{$quote->reference}")]);

        LeadNotifier::send('projet', $quote->reference, url("/admin/quote-requests/{$quote->id}/edit"));

        return back()->with(['reference' => $quote->reference, 'success' => 'project']);
    }

    /* ----------------------------------------------------------------- */

    protected function seo(string $page, ?string $preload = null, ?string $sizes = null): void
    {
        Seo::set([
            'title' => $page === 'home' ? null : __("v7.seo.{$page}.title"),
            'description' => __("v7.seo.{$page}.description"),
            'preload' => $preload,
            'preload_sizes' => $sizes,
        ]);
    }

    protected function expertiseList(bool $detail = false): array
    {
        return Service::published()->get()
            ->map(fn (Service $s) => $detail ? $s->toDetail() : $s->toCard() + ['body' => $s->tr('body')])
            ->values()->all();
    }

    protected function projectList(): array
    {
        return Project::published()->orderBy('position')->get()->map->toCard()->values()->all();
    }

    protected function offerList(): array
    {
        return Offer::published()->get()->map->toCard()->values()->all();
    }

    protected function postList(?int $limit = null): array
    {
        return Post::published()
            ->with(['category', 'author'])
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->when($limit, fn ($q) => $q->limit($limit))
            ->get()->map->toCard()->values()->all();
    }

    protected function campaignData(): array
    {
        return CoupDePouceController::publicCampaign();
    }
}
