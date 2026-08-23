<?php

namespace App\Support;

/**
 * Translation between the site's two spellings of the same page.
 *
 * Paths are written and stored in their French form, which is the canonical
 * one: it owns the root, and it is what the front end hard-codes. `to()`
 * renders that form in whatever language is asked for.
 *
 * Only the first segment is translated. Record slugs live in a single
 * untranslated `slug` column, so `/en/expertise/gros-oeuvre` is the whole of
 * what English can currently say — translating those means a schema change
 * and an English slug entered for every service, project, post and opening.
 *
 * `resources/js/Lib/routes.js` is the same logic for the front end; both read
 * the one map in `config/site.segments`.
 */
class LocalePath
{
    /** Render a path — in either spelling — in the given locale. */
    public static function to(string $path, string $locale): string
    {
        [$path, $suffix] = static::split($path);

        $segments = static::canonical($path);
        $default = config('site.default_locale');

        if ($segments === []) {
            return ($locale === $default ? '/' : "/{$locale}").$suffix;
        }

        if ($locale !== $default) {
            $segments[0] = config("site.segments.{$segments[0]}", $segments[0]);
            array_unshift($segments, $locale);
        }

        return '/'.implode('/', $segments).$suffix;
    }

    /**
     * Every language this path is reachable in, as absolute URLs.
     *
     * Feeds the hreflang tags. Before English had its own addresses these all
     * pointed at the same URL, which told a crawler nothing.
     */
    public static function alternates(string $path): array
    {
        return collect(array_keys(config('site.locales')))
            ->mapWithKeys(fn (string $locale) => [$locale => url(static::to($path, $locale))])
            ->all();
    }

    /** Strip any locale prefix and put the first segment back into French. */
    protected static function canonical(string $path): array
    {
        $segments = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));

        if ($segments === []) {
            return [];
        }

        if (array_key_exists($segments[0], config('site.locales'))
            && $segments[0] !== config('site.default_locale')) {
            array_shift($segments);
        }

        if ($segments === []) {
            return [];
        }

        $french = array_search($segments[0], config('site.segments'), true);

        if ($french !== false) {
            $segments[0] = $french;
        }

        return $segments;
    }

    /** Query and fragment ride along untouched. */
    protected static function split(string $path): array
    {
        $cut = strcspn($path, '?#');

        return [substr($path, 0, $cut), substr($path, $cut)];
    }
}
