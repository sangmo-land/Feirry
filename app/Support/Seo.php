<?php

namespace App\Support;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

/**
 * Server-rendered SEO metadata.
 *
 * Inertia's <Head> only reaches crawlers when an SSR process is running, so
 * the canonical tags are written straight into the root Blade layout instead.
 * Controllers call Seo::set(...) and the data lands in both places.
 */
class Seo
{
    protected static array $data = [];

    public static function set(array $attributes): void
    {
        $brand = SiteSettings::brandName();

        $title = $attributes['title'] ?? $brand;

        static::$data = array_merge([
            'title' => $title,
            // Brand first, then the page: the tab is read left to right and a
            // narrow one truncates from the right, so the name is the half
            // that has to survive. The home page carries no second half — it
            // is the brand — and is left as the name on its own.
            'full_title' => $title === $brand ? $brand : "{$brand} - {$title}",
            'description' => Str::limit(strip_tags((string) ($attributes['description'] ?? '')), 175),
            'image' => $attributes['image'] ?? config('site.seo.default_image'),
            'canonical' => $attributes['canonical'] ?? url()->current(),
            'type' => $attributes['type'] ?? 'website',
            'published_at' => $attributes['published_at'] ?? null,
            'noindex' => $attributes['noindex'] ?? false,
            'schema' => $attributes['schema'] ?? null,
        ], []);

        View::share('seo', static::$data);
    }

    public static function get(): array
    {
        if (static::$data === []) {
            static::set([]);
        }

        return static::$data;
    }

    /** Organization schema injected on every page. */
    public static function organizationSchema(): array
    {
        $contact = config('site.contact');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'GeneralContractor',
            'name' => SiteSettings::brandName(),
            'legalName' => config('site.brand.legal_name'),
            'url' => config('app.url'),
            'telephone' => $contact['phone'],
            'email' => $contact['email'],
            'foundingDate' => (string) config('site.brand.founded'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $contact['address'].', '.$contact['district'],
                'addressLocality' => $contact['city'],
                'addressRegion' => $contact['region'],
                'addressCountry' => 'CM',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $contact['latitude'],
                'longitude' => $contact['longitude'],
            ],
            'areaServed' => ['Douala', 'Yaoundé', 'Kribi', 'Limbe', 'Cameroun'],
            'sameAs' => array_values(array_filter(config('site.social'))),
        ];
    }
}
