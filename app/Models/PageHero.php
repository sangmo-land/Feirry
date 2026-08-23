<?php

namespace App\Models;

use App\Support\Media;
use App\Support\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

/**
 * The opening block of a fixed page.
 *
 * The set of keys is closed: each one answers to a route registered in code,
 * so the admin edits these rows but never creates or deletes them. `PAGES`
 * below is the whole list, and it is also what the seeder and the Filament
 * resource read, so a page cannot exist in one place and not the others.
 */
class PageHero extends Model
{
    use Translatable;

    protected $table = 'page_heroes';

    protected $guarded = [];

    /**
     * key => [route name (unprefixed), label shown in the admin].
     *
     * The route name is how a request finds its hero: English routes carry an
     * `en.` prefix, which is stripped before the lookup, so both spellings of
     * a page resolve to the same row.
     */
    public const PAGES = [
        'home' => ['home', 'Accueil'],
        'about' => ['about', 'Entreprise'],
        'services' => ['services.index', 'Métiers'],
        'projects' => ['projects.index', 'Réalisations'],
        'process' => ['process', 'Méthode'],
        'team' => ['team', 'Équipe'],
        'blog' => ['blog.index', 'Actualités'],
        'careers' => ['careers.index', 'Carrières'],
        'faq' => ['faq', 'FAQ'],
        'contact' => ['contact', 'Contact'],
        'quote' => ['quote', 'Demande de devis'],
        'legal_notice' => ['legal.notice', 'Mentions légales'],
        'legal_privacy' => ['legal.privacy', 'Confidentialité'],
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('page_heroes.all'));
        static::deleted(fn () => Cache::forget('page_heroes.all'));
    }

    /** The page key a route name belongs to, or null for record-backed pages. */
    public static function keyForRoute(?string $route): ?string
    {
        if (blank($route)) {
            return null;
        }

        // `en.about` and `about` are the same page in two languages.
        $route = preg_replace('/^[a-z]{2}\./', '', $route);

        foreach (static::PAGES as $key => [$name]) {
            if ($name === $route) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Every hero, keyed by page, as plain rows.
     *
     * Read on each page render, so it is cached and dropped whenever a row is
     * saved. Deliberately arrays and not models: a cached Eloquent instance is
     * serialized, and any process whose autoloader predates the class — a
     * long-running worker, a dev server started before the file existed —
     * unserializes it as an incomplete object and fatals on the first method
     * call. Setting stores scalars for the same reason.
     *
     * A missing table — a fresh checkout before migrating — falls back to an
     * empty set rather than taking the whole site down.
     */
    public static function allCached(): array
    {
        return Cache::rememberForever('page_heroes.all', function () {
            try {
                return static::query()->get()
                    ->keyBy('key')
                    ->map(fn (self $hero) => $hero->only([
                        'key',
                        'title_fr', 'title_en',
                        'title_accent_fr', 'title_accent_en',
                        'lead_fr', 'lead_en',
                        'image',
                    ]))
                    ->all();
            } catch (QueryException) {
                return [];
            }
        });
    }

    /**
     * The payload for one page, or null where nothing has been set.
     *
     * Blank fields come back as null rather than as empty strings: the front
     * end falls back to its translation string for anything null, so clearing
     * a field in the admin restores the built-in copy instead of leaving a
     * hole in the page.
     */
    public static function forPage(?string $key, ?string $locale = null): ?array
    {
        if (blank($key)) {
            return null;
        }

        $row = static::allCached()[$key] ?? null;

        if (! $row) {
            return null;
        }

        $locale = $locale ?: app()->getLocale();

        return [
            'title' => static::pick($row, 'title', $locale),
            'title_accent' => static::pick($row, 'title_accent', $locale),
            'lead' => static::pick($row, 'lead', $locale),
            'image' => Media::url($row['image'] ?? null),
        ];
    }

    /** The locale's value, falling back to French the way Translatable does. */
    protected static function pick(array $row, string $field, string $locale): ?string
    {
        foreach ([$locale, config('site.default_locale')] as $candidate) {
            $value = $row["{$field}_{$candidate}"] ?? null;

            if (filled($value)) {
                return (string) $value;
            }
        }

        return null;
    }

    public function label(): string
    {
        return static::PAGES[$this->key][1] ?? $this->key;
    }

    public function routeName(): ?string
    {
        return static::PAGES[$this->key][0] ?? null;
    }
}
