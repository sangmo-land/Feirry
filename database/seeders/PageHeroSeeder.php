<?php

namespace Database\Seeders;

use App\Models\PageHero;
use Illuminate\Database\Seeder;

/**
 * Seeds one row per fixed page, carrying over the copy and the photograph the
 * site already shipped with.
 *
 * The point is that the admin opens on the live page rather than on a blank
 * form: the client edits what is there instead of having to rewrite it. Rows
 * are matched on `key` and only filled where they are still empty, so running
 * this again after the client has been in never overwrites their work.
 */
class PageHeroSeeder extends Seeder
{
    /**
     * The photographs each page shipped with, now held on the public disk
     * under heroes/ rather than pulled from a stock library at render time.
     * Replacing the file replaces the picture; the client can also upload
     * their own through the admin, which writes to uploads/ instead.
     */
    private const IMAGES = [
        'home' => 'heroes/home.jpg',
        'about' => 'heroes/about.jpg',
        'services' => 'heroes/services.jpg',
        'projects' => 'heroes/projects.jpg',
        'process' => 'heroes/process.jpg',
        'team' => 'heroes/team.jpg',
        'blog' => 'heroes/blog.jpg',
        'careers' => 'heroes/careers.jpg',
        'faq' => 'heroes/faq.jpg',
        'contact' => 'heroes/contact.jpg',
        'quote' => 'heroes/quote.jpg',
        // The two legal pages open on type alone, and stay that way unless the
        // client picks a picture for them.
        'legal_notice' => null,
        'legal_privacy' => null,
    ];

    /** Where each page's copy lived in the translation files. */
    private const COPY = [
        'home' => ['home.hero_title_1', 'home.hero_title_2', 'home.hero_lead'],
        'about' => ['about.hero_title', null, 'about.hero_lead'],
        'services' => ['services.hero_title', null, 'services.hero_lead'],
        'projects' => ['projects.hero_title', null, 'projects.hero_lead'],
        'process' => ['process.hero_title', null, 'process.hero_lead'],
        'team' => ['team.hero_title', null, 'team.hero_lead'],
        'blog' => ['blog.hero_title', null, 'blog.hero_lead'],
        'careers' => ['careers.hero_title', null, 'careers.hero_lead'],
        'faq' => ['faq.hero_title', null, 'faq.hero_lead'],
        'contact' => ['contact.hero_title', null, 'contact.hero_lead'],
        'quote' => ['quote.hero_title', null, 'quote.hero_lead'],
        // Titled by the controller from the legal document itself.
        'legal_notice' => [null, null, null],
        'legal_privacy' => [null, null, null],
    ];

    public function run(): void
    {
        $strings = [
            'fr' => require base_path('lang/fr/site.php'),
            'en' => require base_path('lang/en/site.php'),
        ];

        $position = 0;

        foreach (array_keys(PageHero::PAGES) as $key) {
            [$titleKey, $accentKey, $leadKey] = self::COPY[$key];

            $values = ['position' => $position++];

            foreach (['fr', 'en'] as $locale) {
                $values["title_{$locale}"] = $this->lookup($strings[$locale], $titleKey);
                $values["title_accent_{$locale}"] = $this->lookup($strings[$locale], $accentKey);
                $values["lead_{$locale}"] = $this->lookup($strings[$locale], $leadKey);
            }

            $values['image'] = self::IMAGES[$key];

            $hero = PageHero::firstOrNew(['key' => $key]);

            // Only fill what is still empty: a second run must not undo an edit.
            foreach ($values as $column => $value) {
                if ($column === 'position' || blank($hero->getAttribute($column))) {
                    $hero->setAttribute($column, $value);
                }
            }

            $hero->save();
        }
    }

    private function lookup(array $strings, ?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $value = data_get($strings, $path);

        return is_string($value) ? $value : null;
    }
}
