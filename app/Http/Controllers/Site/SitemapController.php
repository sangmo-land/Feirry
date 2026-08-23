<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Response;

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
         */
        $pages = [
            ['home', '1.0', 'weekly'],
            ['about', '0.8', 'monthly'],
            ['services.index', '0.9', 'monthly'],
            ['projects.index', '0.9', 'weekly'],
            ['process', '0.7', 'monthly'],
            ['team', '0.6', 'monthly'],
            ['blog.index', '0.8', 'weekly'],
            ['careers.index', '0.7', 'weekly'],
            ['faq', '0.6', 'monthly'],
            ['contact', '0.8', 'monthly'],
            ['quote', '0.9', 'monthly'],
        ];

        $records = [
            ['services.show', Service::published()->get(), '0.8', 'monthly'],
            ['projects.show', Project::published()->get(), '0.7', 'monthly'],
            ['blog.show', Post::published()->get(), '0.6', 'monthly'],
            ['careers.show', JobOpening::published()->get(), '0.5', 'weekly'],
        ];

        $default = config('site.default_locale');
        $urls = collect();

        foreach (array_keys(config('site.locales')) as $locale) {
            $name = fn (string $route) => $locale === $default ? $route : "{$locale}.{$route}";

            foreach ($pages as [$route, $priority, $freq]) {
                $urls->push(['loc' => route($name($route)), 'priority' => $priority, 'freq' => $freq]);
            }

            foreach ($records as [$route, $models, $priority, $freq]) {
                foreach ($models as $model) {
                    $urls->push([
                        'loc' => route($name($route), $model),
                        'priority' => $priority,
                        'freq' => $freq,
                        'lastmod' => $model->updated_at?->toAtomString(),
                    ]);
                }
            }
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(): Response
    {
        $body = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /storage/applications',
            '',
            'Sitemap: '.route('sitemap'),
            '',
        ]);

        return response($body, 200, ['Content-Type' => 'text/plain']);
    }
}
