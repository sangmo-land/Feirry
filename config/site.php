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

    /*
    | Only verified information (CDC V7 §00): every value below comes from the
    | FEIRRY letterhead or was confirmed by the director. Anything not yet
    | confirmed is null and simply not displayed — never invented.
    */
    'brand' => [
        'name' => 'FEIRRY',
        'legal_name' => 'FEIRRY Construction Designs',
        // Carried untranslated, in both locales: it is part of the identity.
        'tagline' => 'Shaping a brighter tomorrow',
        'founded' => 2021,
    ],

    'contact' => [
        'address' => 'Carrefour BP Cité',
        'district' => null,
        'city' => 'Douala',
        'region' => 'Littoral',
        'country' => 'Cameroun',
        'postal_box' => null,
        'secondary_presence' => 'Yaoundé',
        'phone' => '+237 670 729 579',
        'phone_label' => 'MTN',
        'phone_secondary' => '+237 696 645 970',
        'phone_secondary_label' => 'Orange',
        'whatsapp' => '237670729579',
        'email' => 'contact@feirry.org',
        'email_rh' => 'contact@feirry.org',
        'email_devis' => 'contact@feirry.org',
        // Opening hours given by FEIRRY's management on 18/11/2026. The two
        // sentences can be changed in the admin; `opening_hours` is the same
        // information for search engines and must be kept in step with them.
        'hours_fr' => 'Lundi – vendredi : 8h – 17h · Samedi : 9h – 13h',
        'hours_en' => 'Monday – Friday: 8 am – 5 pm · Saturday: 9 am – 1 pm',
        'opening_hours' => [
            ['days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], 'opens' => '08:00', 'closes' => '17:00'],
            ['days' => ['Saturday'], 'opens' => '09:00', 'closes' => '13:00'],
        ],
        'maps_url' => null,
        'latitude' => null,
        'longitude' => null,
    ],

    'legal' => [
        'rccm' => 'RC/DLA/2021/B/72',
        'niu' => 'M012115429036Z',
        'capital' => null,
        'insurer' => null,
    ],

    /*
    | Where new leads are announced. Set LEADS_NOTIFY_EMAIL in the server's
    | .env; nothing is sent while it is empty (requests stay in the admin).
    */
    'leads' => [
        'notify' => env('LEADS_NOTIFY_EMAIL'),
    ],

    /*
    | Coup de Pouce FEIRRY — upload limits (CDC Coup de Pouce §06, §07).
    | Initial values proposed by the specification; adjust to the hosting
    | (PHP upload_max_filesize / post_max_size must allow them).
    */
    /* Network security: reverse proxies allowed to forward the client IP, HTTPS links. */
    'trusted_proxies' => env('TRUSTED_PROXIES'),
    'force_https' => (bool) env('FORCE_HTTPS', env('APP_ENV') === 'production'),

    /* Two-factor sign-in for every admin account (CDC Coup de Pouce §22). */
    'admin_mfa_required' => (bool) env('ADMIN_MFA_REQUIRED', false),

    'cdp' => [
        'pdf_max_files' => (int) env('CDP_PDF_MAX_FILES', 3),
        'pdf_max_kb' => (int) env('CDP_PDF_MAX_KB', 15360),
        'photo_max_files' => (int) env('CDP_PHOTO_MAX_FILES', 5),
        'photo_max_kb' => (int) env('CDP_PHOTO_MAX_KB', 8192),
        /*
        | Automatic booking (décision FEIRRY du 18/11/2026). Times are Africa/Douala.
        | Days: 1 = Monday … 7 = Sunday. A booked slot is final for the applicant.
        | Weekly quota = the edition's announced places (10 consultations, 5 visits).
        | Consultations: Monday 9:00–16:00, 30 minutes each.
        | Visits: Wednesday and Thursday, 8:30–17:30, three windows of three hours
        | (travel included) — to be confirmed by FEIRRY.
        */
        'slots' => [
            'consultation' => ['days' => [1], 'times' => ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30']],
            'visit' => ['days' => [3, 4], 'times' => ['08:30', '11:30', '14:30']],
            // A slot can be booked until this many hours before it starts.
            'lead_hours' => (int) env('CDP_SLOT_LEAD_HOURS', 24),
        ],
        // Version of the privacy notice the applicant agreed to.
        'notice_version' => '2026-11-v1',
    ],

    // Filled from the admin once the official pages are confirmed.
    'social' => [
        'facebook' => null,
        'linkedin' => null,
        'instagram' => null,
        'tiktok' => null,
        'youtube' => null,
    ],

    // No figures are published until FEIRRY validates them (CDC §00).
    'stats' => [],

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
        'feirry' => 'feirry',
        'expertises' => 'expertise',
        'projets' => 'projects',
        'offres' => 'offers',
        'coup-de-pouce' => 'coup-de-pouce',
        'conseils' => 'advice',
        'mon-projet' => 'my-project',
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
