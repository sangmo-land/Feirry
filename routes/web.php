<?php

use App\Http\Controllers\Site\BlogController;
use App\Http\Controllers\Site\CareerController;
use App\Http\Controllers\Site\LegalController;
use App\Http\Controllers\Site\LocaleController;
use App\Http\Controllers\Site\NewsletterController;
use App\Http\Controllers\Site\SitemapController;
use App\Http\Controllers\Site\CoupDePouceController;
use App\Http\Controllers\Site\V7Controller;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
| Registered once and emitted twice, so the two languages cannot drift apart.
| French keeps the root and every URL it has always had; English is served
| beneath /en with translated path segments, which is what lets `contact` and
| `faq` — identical words in both languages — exist in both.
|
| The locale is decided by the prefix rather than by the session: the two
| languages now have distinct addresses, so a crawler (and anyone sharing a
| link) reaches the language the URL names.
|
| Record slugs stay in French on both sides. `slug` is a single column, so
| translating those means a schema change and an English slug typed for every
| service, project, post and job opening.
|
| Route names are shared: `about` resolves to the French URL, `en.about` to
| the English one, and the front end asks for a path by its French spelling.
*/

$site = function (string $locale) {
    // French is the canonical spelling; every other locale maps through.
    $path = fn (string $segment) => $locale === 'fr'
        ? $segment
        : (config("site.segments.{$segment}") ?? $segment);

    $home = $locale === 'fr' ? '' : "/{$locale}";
    $to = fn (string $segment, string $suffix = '') => $home.'/'.$path($segment).$suffix;

    /*
    | V7 — the eight sections of the menu (CDC §02).
    */
    Route::get('/', [V7Controller::class, 'home'])->name('home');
    Route::get($path('feirry'), [V7Controller::class, 'feirry'])->name('feirry');
    Route::get($path('expertises'), [V7Controller::class, 'expertises'])->name('expertises');
    Route::get($path('expertises').'/{service:slug}', [V7Controller::class, 'expertise'])
        ->name('expertises.show')
        ->missing(fn () => redirect($to('expertises'), 301));
    Route::get($path('projets'), [V7Controller::class, 'projets'])->name('projets');
    Route::get($path('projets').'/{project:slug}', [V7Controller::class, 'projet'])
        ->name('projets.show')
        ->missing(fn () => redirect($to('projets'), 301));
    Route::get($path('offres'), [V7Controller::class, 'offres'])->name('offres');
    Route::get($path('coup-de-pouce'), [CoupDePouceController::class, 'index'])->name('cdp');
    Route::get($path('coup-de-pouce').'/consultation', [CoupDePouceController::class, 'consultation'])->name('cdp.consultation');
    Route::get($path('coup-de-pouce').'/visite', [CoupDePouceController::class, 'visit'])->name('cdp.visit');
    Route::get($path('coup-de-pouce').'/confirmation', [CoupDePouceController::class, 'confirmation'])->name('cdp.confirmation');
    Route::post($path('coup-de-pouce').'/demandes', [CoupDePouceController::class, 'store'])
        ->middleware('throttle:8,60')
        ->name('cdp.store');
    Route::get($path('conseils'), [V7Controller::class, 'conseils'])->name('conseils');
    Route::get($path('conseils').'/{post:slug}', [BlogController::class, 'show'])
        ->name('conseils.show')
        ->missing(fn () => redirect($to('conseils'), 301));
    Route::get($path('mon-projet'), [V7Controller::class, 'monProjet'])->name('projet');
    Route::post($path('mon-projet'), [V7Controller::class, 'storeProjet'])
        ->middleware('throttle:6,60')
        ->name('projet.store');

    Route::get($path('mentions-legales'), [LegalController::class, 'notice'])->name('legal.notice');
    Route::get($path('confidentialite'), [LegalController::class, 'privacy'])->name('legal.privacy');

    // Kept out of the menu until FEIRRY confirms the section (audit, step 0).
    Route::get($path('carrieres'), [CareerController::class, 'index'])->name('careers.index');
    Route::get($path('carrieres').'/{jobOpening:slug}', [CareerController::class, 'show'])->name('careers.show');
    Route::post($path('carrieres').'/{jobOpening:slug}/'.$path('candidature'), [CareerController::class, 'apply'])
        ->middleware('throttle:6,60')
        ->name('careers.apply');

    Route::post('newsletter', NewsletterController::class)
        ->middleware('throttle:10,60')
        ->name('newsletter.store');

    /*
    | 301s from the URLs of the previous site (CDC §02, SEO-01). Where the old
    | and new English segments are the same word (expertise, projects), the
    | address simply is the new page.
    */
    $redirects = [
        'entreprise' => $to('feirry'),
        'methode' => $to('feirry', '#methode'),
        'equipe' => $to('feirry'),
        'faq' => $to('feirry'),
        'actualites' => $to('conseils'),
        'devis' => $to('mon-projet'),
        'contact' => $to('mon-projet'),
    ];

    if ($locale === 'fr') {
        $redirects += ['metiers' => $to('expertises'), 'realisations' => $to('projets')];

        Route::get('realisations/{slug}', fn (string $slug) => redirect(
            Project::published()->where('slug', $slug)->exists() ? $to('projets', "/{$slug}") : $to('projets'), 301,
        ));
        Route::get('metiers/{slug}', fn () => redirect($to('expertises'), 301));
    }

    Route::get($path('actualites').'/{slug}', fn (string $slug) => redirect($to('conseils', "/{$slug}"), 301));

    foreach ($redirects as $from => $target) {
        Route::permanentRedirect($path($from), $target);
    }
};

/*
| The locale that owns the root takes no prefix; every other one is served
| under a prefix equal to its code, which is the rule SetLocale reads back off
| the path. Adding a third language is a config entry and a column of segments.
*/
$default = config('site.default_locale');

foreach (array_keys(config('site.locales')) as $locale) {
    if ($locale === $default) {
        $site($locale);

        continue;
    }

    Route::prefix($locale)->name("{$locale}.")->group(fn () => $site($locale));
}

/*
| Kept for links published before the prefix existed: it now forwards to the
| equivalent address in the language asked for rather than setting a session
| flag nothing reads any more.
*/
Route::get('/langue/{locale}', LocaleController::class)->name('locale.switch');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::get('/admin/fichiers/{type}/{id}/{index}', \App\Http\Controllers\Admin\LeadFileController::class)
    ->middleware('web')
    ->whereNumber(['id', 'index'])
    ->name('admin.lead-file');
