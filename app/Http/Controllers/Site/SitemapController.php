<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class SitemapController extends Controller
{
    public function index(): Response
    {
        /*
         * Emitted once per language. Both are listed because both are now real
         * addresses — before the /en prefix existed the English site shared
         * the French URLs and no crawler could reach it.
         *
         * Route names carry the locale: `about` is the French URL, `en.about`
         * the English one.
         *
         * Each entry is built as a group — the same page in every language —
         * rather than a flat list, because every URL in the group has to name
         * every other one as its hreflang alternate. Google treats the pair as
         * translations of one page only when the declaration is reciprocal;
         * listing them as two unrelated URLs is how a translated site ends up
         * competing with itself.
         */
        $pages = [
            ['home', '1.0', 'weekly'],
            ['feirry', '0.8', 'monthly'],
            ['expertises', '0.9', 'monthly'],
            ['projets', '0.9', 'weekly'],
            ['offres', '0.9', 'monthly'],
            ['cdp', '0.8', 'weekly'],
            ['cdp.consultation', '0.6', 'weekly'],
            ['cdp.visit', '0.6', 'weekly'],
            ['conseils', '0.7', 'weekly'],
            ['projet', '0.9', 'monthly'],
            // The legal pages are noindex until their text is approved (LEG-01):
            // a sitemap must not list pages it asks crawlers to skip (SEO-03).
        ];

        $records = [
            ['expertises.show', Service::published()->get(), '0.8', 'monthly'],
            ['projets.show', Project::published()->get(), '0.7', 'monthly'],
            ['conseils.show', Post::published()->get(), '0.6', 'monthly'],
            ['careers.show', JobOpening::published()->get(), '0.5', 'weekly'],
        ];

        $urls = collect();

        foreach ($pages as [$route, $priority, $freq]) {
            $urls = $urls->concat($this->group($route, null, $priority, $freq));
        }

        foreach ($records as [$route, $models, $priority, $freq]) {
            foreach ($models as $model) {
                $urls = $urls->concat(
                    $this->group($route, $model, $priority, $freq, $model->updated_at?->toAtomString()),
                );
            }
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        /*
         * Served from here rather than from a file in public/, so the sitemap
         * line is always the current host and cannot be left behind by a
         * deploy. There must be no public/robots.txt: the web server hands a
         * real file over before Laravel sees the request, and the static one
         * that shipped with the skeleton named no sitemap at all.
         */
        $body = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /storage/applications',
            'Disallow: /*?besoin=',
            'Disallow: /*?offre=',
            // A URL that always redirects; letting a crawler queue and follow
            // it wastes budget on a page that never has content of its own.
            'Disallow: /langue/',
            '',
            'Sitemap: '.route('sitemap'),
            '',
        ]);

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * One page in every language it exists in, each entry naming all the others.
     *
     * `x-default` points at the language that owns the root, which is what a
     * crawler falls back to when it cannot match the reader to either.
     */
    protected function group(
        string $route,
        mixed $model = null,
        string $priority = '0.5',
        string $freq = 'monthly',
        ?string $lastmod = null,
    ): Collection {
        $default = config('site.default_locale');

        $alternates = collect(array_keys(config('site.locales')))
            ->mapWithKeys(function (string $locale) use ($route, $model, $default) {
                $name = $locale === $default ? $route : "{$locale}.{$route}";

                return [$locale => $model ? route($name, $model) : route($name)];
            });

        $alternates = $alternates->put('x-default', $alternates[$default]);

        return $alternates
            ->except('x-default')
            ->map(fn (string $loc) => array_filter([
                'loc' => $loc,
                'lastmod' => $lastmod,
                'freq' => $freq,
                'priority' => $priority,
                'alternates' => $alternates->all(),
            ]))
            ->values();
    }
}
