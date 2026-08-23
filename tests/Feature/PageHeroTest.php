<?php

namespace Tests\Feature;

use App\Models\PageHero;
use Database\Seeders\PageHeroSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PageHeroTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PageHeroSeeder::class);
    }

    /** The hero the page renders, as it reaches the front end. */
    private function heroFor(string $path): ?array
    {
        return $this->get($path)->assertOk()->viewData('page')['props']['hero'] ?? null;
    }

    public function test_every_fixed_page_is_seeded_with_the_copy_it_shipped_with(): void
    {
        $this->assertCount(count(PageHero::PAGES), PageHero::all());

        $hero = $this->heroFor('/entreprise');

        $this->assertSame('Seize ans de chantiers, une culture du geste juste', $hero['title']);
        $this->assertNotNull($hero['image']);
    }

    public function test_editing_a_hero_changes_the_page(): void
    {
        PageHero::where('key', 'team')->first()->update([
            'title_fr' => 'Un titre choisi dans le tableau de bord',
            'lead_fr' => 'Une accroche choisie dans le tableau de bord.',
        ]);

        $hero = $this->heroFor('/equipe');

        $this->assertSame('Un titre choisi dans le tableau de bord', $hero['title']);
        $this->assertSame('Une accroche choisie dans le tableau de bord.', $hero['lead']);
    }

    public function test_clearing_a_field_falls_back_to_the_shipped_copy(): void
    {
        // Null, not empty string: the front end only falls back on null, and
        // an emptied admin field must restore the built-in wording rather than
        // leave a hole in the page.
        PageHero::where('key', 'team')->first()->update([
            'title_fr' => '', 'title_en' => '', 'lead_fr' => '', 'lead_en' => '',
        ]);

        $hero = $this->heroFor('/equipe');

        $this->assertNull($hero['title']);
        $this->assertNull($hero['lead']);
    }

    public function test_an_uploaded_image_resolves_through_the_storage_link(): void
    {
        PageHero::where('key', 'team')->first()->update(['image' => 'uploads/photo.jpg']);

        // Root-relative, not absolute. The public disk used to hang its URLs
        // off APP_URL, which made every picture cross-origin as soon as the
        // site answered on another host and had the admin's upload previews
        // refused by CORS. What this test is really about is unchanged: a
        // stored path must come back rooted at the storage link rather than
        // resolving against whatever page happens to be rendering it.
        $this->assertSame('/storage/uploads/photo.jpg', $this->heroFor('/equipe')['image']);
    }

    public function test_a_remote_image_is_left_alone(): void
    {
        PageHero::where('key', 'team')->first()->update(['image' => 'https://cdn.example.com/a.jpg']);

        $this->assertSame('https://cdn.example.com/a.jpg', $this->heroFor('/equipe')['image']);
    }

    public function test_both_languages_of_a_page_share_one_row(): void
    {
        PageHero::where('key', 'about')->first()->update([
            'title_fr' => 'Titre français',
            'title_en' => 'English title',
        ]);

        $this->assertSame('Titre français', $this->heroFor('/entreprise')['title']);
        $this->assertSame('English title', $this->heroFor('/en/company')['title']);
    }

    public function test_a_locale_left_empty_falls_back_to_french(): void
    {
        PageHero::where('key', 'about')->first()->update([
            'title_fr' => 'Seulement en français',
            'title_en' => null,
        ]);

        $this->assertSame('Seulement en français', $this->heroFor('/en/company')['title']);
    }

    public function test_record_backed_pages_keep_their_own_hero(): void
    {
        // A project, post or job draws its hero from its own row, so these
        // routes must resolve to no page hero at all rather than borrowing
        // the listing page's one.
        $this->assertNull(PageHero::keyForRoute('projects.show'));
        $this->assertNull(PageHero::keyForRoute('blog.show'));
        $this->assertNull(PageHero::keyForRoute(null));

        $this->assertSame('projects', PageHero::keyForRoute('projects.index'));
        $this->assertSame('projects', PageHero::keyForRoute('en.projects.index'));
    }

    public function test_the_cache_is_dropped_when_a_hero_is_saved(): void
    {
        $this->heroFor('/equipe');
        $this->assertNotNull(Cache::get('page_heroes.all'));

        PageHero::where('key', 'team')->first()->update(['title_fr' => 'Nouveau']);

        $this->assertSame('Nouveau', $this->heroFor('/equipe')['title']);
    }

    public function test_the_cache_holds_plain_rows_not_models(): void
    {
        // A cached Eloquent instance is serialized, and any process whose
        // autoloader predates the class unserializes it as an incomplete
        // object and fatals on the first method call.
        $this->heroFor('/equipe');

        foreach (Cache::get('page_heroes.all') as $row) {
            $this->assertIsNotObject($row);
        }
    }

    public function test_the_seeder_never_overwrites_an_edit(): void
    {
        PageHero::where('key', 'team')->first()->update(['title_fr' => 'Écrit par le client']);

        $this->seed(PageHeroSeeder::class);

        $this->assertSame('Écrit par le client', PageHero::where('key', 'team')->first()->title_fr);
    }
}
