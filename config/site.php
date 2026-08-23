<?php

/*
|--------------------------------------------------------------------------
| Site identity
|--------------------------------------------------------------------------
| Static fallbacks for the public site. Anything here can be overridden at
| runtime from the Filament "Paramètres du site" page, which writes to the
| `settings` table; these values are what ships with the repository.
*/

return [

    'brand' => [
        'name' => 'FEIRRY',
        'legal_name' => 'FEIRRY SARL — Bâtiment et Travaux Publics',
        // Carried untranslated, in both locales: it is part of the identity.
        'tagline' => 'Shaping a brighter tomorrow',
        'founded' => 2009,
    ],

    'contact' => [
        'address' => 'Rue Joss, Immeuble Kondo, 3ᵉ étage',
        'district' => 'Bonanjo',
        'city' => 'Douala',
        'region' => 'Littoral',
        'country' => 'Cameroun',
        'postal_box' => 'BP 12457 Douala',
        'phone' => '+237 6 99 45 12 80',
        'phone_secondary' => '+237 2 33 42 18 06',
        'whatsapp' => '237694451280',
        'email' => 'contact@feirry.org',
        'email_rh' => 'recrutement@feirry.org',
        'email_devis' => 'devis@feirry.org',
        'hours_fr' => 'Lun – Ven : 07h30 – 17h30 · Sam : 08h00 – 13h00',
        'hours_en' => 'Mon – Fri: 7:30am – 5:30pm · Sat: 8:00am – 1:00pm',

        /*
        | The same opening hours a second time, in the shape schema.org asks
        | for. The strings above are what a visitor reads; this is what Google
        | parses into the opening-hours line of a local result. They are kept
        | adjacent so a change to one is an obvious prompt to change the other.
        */
        'opening_hours' => [
            [
                'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens' => '07:30',
                'closes' => '17:30',
            ],
            [
                'days' => ['Saturday'],
                'opens' => '08:00',
                'closes' => '13:00',
            ],
        ],
        // The pin the client shared, resolved from its maps.app.goo.gl short
        // link. The previous pair pointed at the middle of Bonanjo, some 2.7 km
        // west of the office. All three are overridable from the dashboard.
        'maps_url' => 'https://maps.app.goo.gl/nzyHafywg61D5aUA6',
        'latitude' => 4.047588,
        'longitude' => 9.728659,
    ],

    'legal' => [
        'rccm' => 'RC/DLA/2009/B/4187',
        'niu' => 'M090912345678L',
        'capital' => '150 000 000 FCFA',
        'insurer' => 'Chanas Assurances — Police RC Décennale n° 0982-114',
    ],

    'social' => [
        'facebook' => 'https://facebook.com/feirry',
        'linkedin' => 'https://linkedin.com/company/feirry',
        'instagram' => 'https://instagram.com/feirry',
        'tiktok' => 'https://tiktok.com/@feirry',
        'youtube' => 'https://youtube.com/@feirry',
    ],

    'stats' => [
        ['value' => 16, 'suffix' => '', 'label_fr' => "Années d'expérience", 'label_en' => 'Years of experience'],
        ['value' => 240, 'suffix' => '+', 'label_fr' => 'Chantiers livrés', 'label_en' => 'Projects delivered'],
        ['value' => 385, 'suffix' => '', 'label_fr' => 'Collaborateurs', 'label_en' => 'People on staff'],
        ['value' => 98, 'suffix' => '%', 'label_fr' => 'Livraisons dans les délais', 'label_en' => 'On-time delivery'],
    ],

    'locales' => [
        'fr' => ['label' => 'Français', 'short' => 'FR'],
        'en' => ['label' => 'English', 'short' => 'EN'],
    ],

    /*
    | The language that owns the root, and the one every other is measured
    | against. Deliberately not `app.locale`: Application::setLocale() writes
    | that key, so once a request has resolved into English it reports 'en'
    | and anything asking it which language is the default gets the wrong
    | answer — the hreflang tags pointed French at /fr/en/company.
    */
    'default_locale' => 'fr',

    /*
    |--------------------------------------------------------------------------
    | Localised path segments
    |--------------------------------------------------------------------------
    | The single source of truth for the site's URLs, read by the route file,
    | by the sitemap and — shared through Inertia — by the front end, so a
    | path can never be spelled one way in PHP and another way in React.
    |
    | French is the canonical form: it keeps the root, every French URL the
    | site has ever published is unchanged, and English is served beneath the
    | /en prefix. The prefix is what makes `contact` and `faq` — spelled the
    | same in both languages — able to exist twice.
    */
    'segments' => [
        'entreprise' => 'company',
        'methode' => 'method',
        'equipe' => 'team',
        'faq' => 'faq',
        'metiers' => 'expertise',
        'realisations' => 'projects',
        'actualites' => 'news',
        'carrieres' => 'careers',
        'candidature' => 'apply',
        'contact' => 'contact',
        'devis' => 'quote',
        'mentions-legales' => 'legal-notice',
        'confidentialite' => 'privacy',
    ],

    'seo' => [
        'default_image' => '/images/og/feirry-og.jpg',
        'twitter' => '@feirry',

        /*
        | The logo as structured data reports it. Deliberately its own key
        | rather than reusing the social card: Google wants a plain rendering
        | of the mark, and will not read an SVG — which is what favicon.svg
        | is — so this has to stay a raster file. It falls back to the social
        | card, which at least carries the name, until a square PNG exists.
        */
        'logo' => '/images/og/feirry-og.jpg',

        /*
        | Search-console ownership proofs. Google and Bing both accept a meta
        | tag in the <head> as an alternative to uploading a file, which is the
        | form that survives a deploy: the token lives in the environment, so
        | it is set once per property and never overwritten by a build.
        |
        | Empty means the tag is not emitted at all — an empty content
        | attribute reads to the verifier as a failed proof rather than an
        | absent one.
        */
        'verification' => [
            'google' => env('GOOGLE_SITE_VERIFICATION'),
            'bing' => env('BING_SITE_VERIFICATION'),
        ],

        /*
        | The fragment the location section on the company page is addressed
        | by. Named once here because three places have to agree on it: the
        | id rendered into the markup, the URL inside the Place schema, and
        | the link the footer points at. A fragment is not a separate URL to a
        | crawler, so what it buys is a stable address for the section and a
        | subject Google can attach the map result to.
        */
        'location_anchor' => 'localisation',
    ],

];
