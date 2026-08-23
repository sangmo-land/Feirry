<?php

use App\Http\Controllers\Site\BlogController;
use App\Http\Controllers\Site\CareerController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\LegalController;
use App\Http\Controllers\Site\LocaleController;
use App\Http\Controllers\Site\NewsletterController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\ProjectController;
use App\Http\Controllers\Site\QuoteController;
use App\Http\Controllers\Site\ServiceController;
use App\Http\Controllers\Site\SitemapController;
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

    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get($path('entreprise'), [PageController::class, 'about'])->name('about');
    Route::get($path('methode'), [PageController::class, 'process'])->name('process');
    Route::get($path('equipe'), [PageController::class, 'team'])->name('team');
    Route::get($path('faq'), [PageController::class, 'faq'])->name('faq');

    Route::get($path('metiers'), [ServiceController::class, 'index'])->name('services.index');
    Route::get($path('metiers').'/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

    Route::get($path('realisations'), [ProjectController::class, 'index'])->name('projects.index');
    Route::get($path('realisations').'/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');

    Route::get($path('actualites'), [BlogController::class, 'index'])->name('blog.index');
    Route::get($path('actualites').'/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

    Route::get($path('carrieres'), [CareerController::class, 'index'])->name('careers.index');
    Route::get($path('carrieres').'/{jobOpening:slug}', [CareerController::class, 'show'])->name('careers.show');
    Route::post($path('carrieres').'/{jobOpening:slug}/'.$path('candidature'), [CareerController::class, 'apply'])
        ->middleware('throttle:6,60')
        ->name('careers.apply');

    Route::get($path('contact'), [ContactController::class, 'index'])->name('contact');
    Route::post($path('contact'), [ContactController::class, 'store'])
        ->middleware('throttle:8,60')
        ->name('contact.store');

    Route::get($path('devis'), [QuoteController::class, 'index'])->name('quote');
    Route::post($path('devis'), [QuoteController::class, 'store'])
        ->middleware('throttle:8,60')
        ->name('quote.store');

    Route::post('newsletter', NewsletterController::class)
        ->middleware('throttle:10,60')
        ->name('newsletter.store');

    Route::get($path('mentions-legales'), [LegalController::class, 'notice'])->name('legal.notice');
    Route::get($path('confidentialite'), [LegalController::class, 'privacy'])->name('legal.privacy');
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
