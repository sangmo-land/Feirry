<?php

namespace App\Http\Middleware;

use App\Models\PageHero;
use App\Support\Seo;
use App\Support\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $locale = app()->getLocale();
        // The careers pages are the last ones still drawn by the first version of the site.
        $legacy = $request->routeIs('careers.*', 'en.careers.*');

        return array_merge(parent::share($request), [
            'locale' => $locale,
            'locales' => config('site.locales'),
            // The front end writes every internal href in its French spelling
            // and translates it on the way out, so this is the same map the
            // route file builds the URLs from — one source, never two.
            'segments' => config('site.segments'),
            'defaultLocale' => config('site.default_locale'),
            // The fragment the company page's location section is addressed
            // by. Shared rather than written twice, so the id rendered into
            // the markup and the id inside the structured data describing it
            // cannot drift apart.
            'locationAnchor' => Seo::locationAnchor(),
            // Only the legacy careers pages still read these strings; the V7 pages
            // carry their own interface dictionary. The groups left out belong to
            // retired pages and still hold the template's demonstration copy
            // (a founding year, headcounts, branches): sending them would publish
            // it in the page source, where search engines read it.
            'translations' => fn () => $legacy
                ? Arr::except(trans('site'), ['home', 'about', 'values', 'process', 'faq', 'quote'])
                : [],
            'site' => fn () => SiteSettings::forFrontend($locale),
            // Footer reminder of the Coup de Pouce edition (CDC Coup de Pouce §02).
            'cdpEdition' => function () use ($locale) {
                $c = \App\Models\Campaign::displayed();
                $p = $c?->toPublic($locale);

                return $p && in_array($p['state'], ['open', 'waitlist', 'upcoming'], true) ? ($p['edition'] ?? null) : null;
            },
            'seo' => fn () => Seo::get(),
            /*
             * The opening block of a fixed page, resolved from the route that
             * matched rather than passed by each controller: the eleven page
             * controllers would otherwise each repeat the same lookup, and a
             * page added later would silently lose its editable hero.
             *
             * Record-backed pages — a project, a post, a job — map to no key
             * and get null, so they keep drawing their hero from their own row.
             */
            // The V7 pages write their own opening block and never read this one,
            // so it is not sent to them: an unread row would still be published.
            'hero' => fn () => $legacy
                ? PageHero::forPage(PageHero::keyForRoute($request->route()?->getName()), $locale)
                : null,
            'flash' => fn () => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'reference' => $request->session()->get('reference'),
            ],
            'auth' => fn () => [
                'user' => $request->user()
                    ? ['name' => $request->user()->name, 'email' => $request->user()->email]
                    : null,
            ],
        ]);
    }
}
