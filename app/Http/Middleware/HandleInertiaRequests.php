<?php

namespace App\Http\Middleware;

use App\Models\PageHero;
use App\Support\Seo;
use App\Support\SiteSettings;
use Illuminate\Http\Request;
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

        return array_merge(parent::share($request), [
            'locale' => $locale,
            'locales' => config('site.locales'),
            // The front end writes every internal href in its French spelling
            // and translates it on the way out, so this is the same map the
            // route file builds the URLs from — one source, never two.
            'segments' => config('site.segments'),
            'defaultLocale' => config('site.default_locale'),
            'translations' => fn () => trans('site'),
            'site' => fn () => SiteSettings::forFrontend($locale),
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
            'hero' => fn () => PageHero::forPage(
                PageHero::keyForRoute($request->route()?->getName()),
                $locale,
            ),
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
