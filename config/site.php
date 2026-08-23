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
    ],

];
