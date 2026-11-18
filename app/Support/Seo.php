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
            // A Blade partial rendered inside the layout's <noscript>. The site
            // is client-rendered, so a crawler that does not execute JavaScript
            // receives a body with nothing in it. A page whose content has to
            // survive that names a partial here and the content is present in
            // the HTML as delivered, before anything renders.
            'noscript' => $attributes['noscript'] ?? null,
            // The above-the-fold image, preloaded so the client-rendered LCP
            // does not wait for the bundle to discover it.
            'preload' => $attributes['preload'] ?? null,
            'preload_sizes' => $attributes['preload_sizes'] ?? null,
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

    /**
     * Attach structured data after the fact.
     *
     * locationSchema() reads the title and description back out of the page it
     * is describing, so it cannot be built in the same call that sets them.
     * A controller sets the page first and hands the graph over second.
     */
    public static function schema(array $schema): void
    {
        static::get();

        static::$data['schema'] = $schema;

        View::share('seo', static::$data);
    }

    /*
    |--------------------------------------------------------------------------
    | Identifiers
    |--------------------------------------------------------------------------
    | Every node the site emits is addressed by a stable @id, so that the graph
    | on one page and the graph on another describe the same company rather
    | than two companies that happen to share a name. The organisation's id is
    | absolute and never changes; the page-level ones hang off the canonical.
    */

    public static function organizationId(): string
    {
        return rtrim(config('app.url'), '/').'/#organization';
    }

    public static function websiteId(): string
    {
        return rtrim(config('app.url'), '/').'/#website';
    }

    /** The fragment the company page's location section is addressed by. */
    public static function locationAnchor(): string
    {
        return (string) config('site.seo.location_anchor', 'localisation');
    }

    /**
     * The company as a local business.
     *
     * Injected on every page. GeneralContractor is a subtype of LocalBusiness,
     * which is itself a subtype of Place — so this one node carries the trade,
     * the contact details and the physical location together, which is what a
     * map result is assembled out of.
     *
     * Read through SiteSettings rather than straight off config, because the
     * address and the phone number are editable from the dashboard, and
     * structured data that disagrees with the address printed on the page is
     * worse than no structured data at all.
     */
    public static function organizationSchema(): array
    {
        $fallback = config('site.contact');
        $get = fn (string $key) => SiteSettings::get("contact.{$key}", $fallback[$key] ?? null);

        $latitude = $get('latitude');
        $longitude = $get('longitude');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'GeneralContractor',
            '@id' => static::organizationId(),
            'name' => SiteSettings::brandName(),
            'legalName' => SiteSettings::get('brand.legal_name', config('site.brand.legal_name')),
            // Same sentence as the home page description: nothing here that the page does not say.
            'description' => __('v7.seo.home.description'),
            'url' => rtrim(config('app.url'), '/').'/',
            'telephone' => $get('phone'),
            'email' => $get('email'),
            'foundingDate' => (string) config('site.brand.founded'),
            // Built off APP_URL rather than the request, so the same node is
            // emitted whatever hostname served it. A graph whose @id is stable
            // but whose image moves is two descriptions of one company.
            'image' => static::asset(config('site.seo.default_image')),
            'logo' => static::asset(config('site.seo.logo', config('site.seo.default_image'))),
            'address' => static::postalAddress(),
            // Only the places FEIRRY has confirmed (CDC correctif SEO-04).
            'areaServed' => ['Douala', 'Yaoundé', 'Cameroun'],
            'sameAs' => array_values(array_filter(SiteSettings::socialLinks())),
        ];

        // A pin with only one of the two numbers is not a pin. Google discards
        // a GeoCoordinates node missing either, so the node is left out whole
        // rather than emitted half filled.
        if (filled($latitude) && filled($longitude)) {
            $schema['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $latitude,
                'longitude' => (float) $longitude,
            ];
        }

        if ($map = static::mapUrl()) {
            $schema['hasMap'] = $map;
        }

        if ($hours = static::openingHours()) {
            $schema['openingHoursSpecification'] = $hours;
        }

        $points = array_values(array_filter([
            static::contactPoint('customer service', $get('phone'), $get('email')),
            static::contactPoint('sales', null, $fallback['email_devis'] ?? null),
            static::contactPoint('human resources', null, $fallback['email_rh'] ?? null),
        ]));

        if ($points !== []) {
            $schema['contactPoint'] = $points;
        }

        return $schema;
    }

    /**
     * The company page's graph, with the location section as an addressable
     * part of it.
     *
     * A fragment is not a separate URL: nothing here makes
     * `/entreprise#localisation` its own entry in the index. What it does is
     * name the section, give it an id at that fragment and declare what it is
     * about, so the address, the hours and the pin are attached to a specific
     * place on a specific page rather than floating loose in the markup — and
     * so Google has a section to point at when it decides to link straight to
     * one.
     */
    public static function locationSchema(?string $canonical = null): array
    {
        $canonical = rtrim($canonical ?? url()->current(), '/');
        $anchor = static::locationAnchor();
        $brand = SiteSettings::brandName();
        $current = static::get();

        // The organisation is repeated inside the graph rather than linked to,
        // because a page carries one JSON-LD block: @context belongs to the
        // graph now, and the node keeps the same @id it has everywhere else.
        $organization = static::organizationSchema();
        unset($organization['@context']);

        $place = [
            '@type' => 'Place',
            '@id' => "{$canonical}#{$anchor}",
            'name' => $brand.' — '.__('site.about.location_title'),
            'url' => "{$canonical}#{$anchor}",
            'address' => static::postalAddress(),
        ];

        foreach (['geo', 'hasMap'] as $key) {
            if (isset($organization[$key])) {
                $place[$key] = $organization[$key];
            }
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    '@id' => static::websiteId(),
                    'url' => rtrim(config('app.url'), '/').'/',
                    'name' => $brand,
                    'publisher' => ['@id' => static::organizationId()],
                    'inLanguage' => app()->getLocale(),
                ],
                $organization,
                [
                    '@type' => 'WebPage',
                    '@id' => "{$canonical}#webpage",
                    'url' => $canonical,
                    'name' => $current['full_title'] ?? $brand,
                    'description' => $current['description'] ?? '',
                    'isPartOf' => ['@id' => static::websiteId()],
                    'about' => ['@id' => static::organizationId()],
                    'inLanguage' => app()->getLocale(),
                    'hasPart' => [
                        [
                            '@type' => 'WebPageElement',
                            '@id' => "{$canonical}#{$anchor}-section",
                            'name' => __('site.about.location_title'),
                            'url' => "{$canonical}#{$anchor}",
                            // The selector points a parser at the markup this
                            // node describes. It is the same id the section
                            // renders with, out of the same config key.
                            'cssSelector' => "#{$anchor}",
                            'about' => ['@id' => "{$canonical}#{$anchor}"],
                        ],
                    ],
                ],
                $place,
            ],
        ];
    }

    /** An absolute URL for a bundled asset, anchored to APP_URL. */
    protected static function asset(string $path): string
    {
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return rtrim(config('app.url'), '/').'/'.ltrim($path, '/');
    }

    /** The postal address, in the one shape schema.org reads. */
    public static function postalAddress(): array
    {
        $fallback = config('site.contact');
        $get = fn (string $key) => SiteSettings::get("contact.{$key}", $fallback[$key] ?? null);

        return array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => trim($get('address').', '.($fallback['district'] ?? ''), " ,\t\n"),
            'addressLocality' => $get('city'),
            'addressRegion' => $fallback['region'] ?? null,
            // A Cameroonian BP is a post-office box, not a postal code. Filed
            // under postalCode it describes a delivery area that does not
            // exist, and Google drops the address rather than half-reading it.
            'postOfficeBoxNumber' => $fallback['postal_box'] ?? null,
            'addressCountry' => 'CM',
        ], fn ($value) => filled($value));
    }

    /**
     * A link to the pin. The dashboard holds the short link the client shared;
     * with none saved, one is built out of the coordinates, so `hasMap` is
     * still a real address rather than a missing property.
     */
    public static function mapUrl(): ?string
    {
        $fallback = config('site.contact');
        $saved = SiteSettings::get('contact.maps_url', $fallback['maps_url'] ?? null);

        if (filled($saved)) {
            return $saved;
        }

        $latitude = SiteSettings::get('contact.latitude', $fallback['latitude'] ?? null);
        $longitude = SiteSettings::get('contact.longitude', $fallback['longitude'] ?? null);

        if (blank($latitude) || blank($longitude)) {
            return null;
        }

        return "https://www.google.com/maps/search/?api=1&query={$latitude},{$longitude}";
    }

    /** config's opening hours, restated as OpeningHoursSpecification nodes. */
    protected static function openingHours(): array
    {
        return collect(config('site.contact.opening_hours', []))
            ->filter(fn ($slot) => filled($slot['days'] ?? null) && filled($slot['opens'] ?? null))
            ->map(fn (array $slot) => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array_values($slot['days']),
                'opens' => $slot['opens'],
                'closes' => $slot['closes'],
            ])
            ->values()
            ->all();
    }

    /** One ContactPoint, or null when the address behind it is not set. */
    protected static function contactPoint(string $type, ?string $phone, ?string $email): ?array
    {
        if (blank($phone) && blank($email)) {
            return null;
        }

        return array_filter([
            '@type' => 'ContactPoint',
            'contactType' => $type,
            'telephone' => $phone,
            'email' => $email,
            'areaServed' => 'CM',
            'availableLanguage' => array_keys(config('site.locales')),
        ], fn ($value) => filled($value));
    }
}
