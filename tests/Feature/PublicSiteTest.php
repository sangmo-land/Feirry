<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use Database\Seeders\CareerSeeder;
use Database\Seeders\ContentSeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            ServiceSeeder::class,
            ProjectSeeder::class,
            TeamSeeder::class,
            ContentSeeder::class,
            CareerSeeder::class,
        ]);
    }

    public function test_every_public_route_responds(): void
    {
        $paths = [
            '/', '/entreprise', '/metiers', '/realisations', '/methode',
            '/equipe', '/actualites', '/carrieres', '/faq', '/contact',
            '/devis', '/mentions-legales', '/confidentialite',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_detail_pages_render_for_seeded_records(): void
    {
        $this->get('/metiers/'.Service::first()->slug)->assertOk();
        $this->get('/realisations/'.Project::first()->slug)->assertOk();
        $this->get('/actualites/'.Post::published()->first()->slug)->assertOk();
    }

    public function test_unpublished_records_are_hidden(): void
    {
        $service = Service::first();
        $service->update(['is_published' => false]);

        $this->get('/metiers/'.$service->slug)->assertNotFound();

        $project = Project::first();
        $project->update(['is_published' => false]);

        $this->get('/realisations/'.$project->slug)->assertNotFound();
    }

    public function test_scheduled_posts_are_not_public(): void
    {
        $post = Post::published()->first();
        $post->update(['published_at' => now()->addWeek()]);

        $this->get('/actualites/'.$post->slug)->assertNotFound();
    }

    public function test_projects_can_be_filtered_by_category_and_status(): void
    {
        $this->get('/realisations?categorie=residentiel')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('filters.categorie', 'residentiel')
                ->has('projects'));

        $response = $this->get('/realisations?statut=ongoing')->assertOk();

        $statuses = collect($response->viewData('page')['props']['projects'])->pluck('status')->unique();

        $this->assertEquals(['ongoing'], $statuses->values()->all());
    }

    public function test_blog_search_narrows_results(): void
    {
        $response = $this->get('/actualites?q=permis')->assertOk();

        $titles = collect($response->viewData('page')['props']['posts']['data'])->pluck('title');

        $this->assertNotEmpty($titles);
        $this->assertTrue($titles->every(fn (string $t) => str_contains(mb_strtolower($t), 'permis')));
    }

    public function test_the_url_decides_the_rendered_language(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('locale', 'fr'));

        $this->get('/en')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('locale', 'en'));

        // No session involved: the same request in either order still answers
        // in whichever language its own path names.
        $this->withSession(['locale' => 'en'])
            ->get('/metiers')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('locale', 'fr'));
    }

    public function test_english_pages_are_served_under_translated_paths(): void
    {
        $this->get('/en/expertise')
            ->assertOk()
            ->assertSee('Structural shell', escape: false);

        // Words spelled the same in both languages need the prefix to exist
        // twice; without it these two would collide on one route.
        $this->get('/contact')->assertOk();
        $this->get('/en/contact')->assertOk();
        $this->get('/faq')->assertOk();
        $this->get('/en/faq')->assertOk();

        // Record slugs are not translated: a single `slug` column serves both.
        $this->get('/en/expertise/gros-oeuvre')->assertOk();

        $this->get('/en/metiers')->assertNotFound();
    }

    public function test_each_page_declares_its_counterpart_in_the_other_language(): void
    {
        $this->get('/entreprise')
            ->assertOk()
            ->assertSee('<html lang="fr">', escape: false)
            ->assertSee('hreflang="en" href="'.url('/en/company').'"', escape: false);

        $this->get('/en/company')
            ->assertOk()
            ->assertSee('<html lang="en">', escape: false)
            ->assertSee('hreflang="fr" href="'.url('/entreprise').'"', escape: false);
    }

    public function test_the_old_locale_endpoint_forwards_to_the_equivalent_page(): void
    {
        $this->get('/langue/en', ['referer' => url('/entreprise')])
            ->assertRedirect(url('/en/company'));

        $this->get('/langue/fr', ['referer' => url('/en/news')])
            ->assertRedirect(url('/actualites'));

        $this->get('/langue/de')->assertNotFound();
    }

    public function test_sitemap_lists_published_content_only(): void
    {
        $hidden = Project::first();
        $hidden->update(['is_published' => false]);

        $response = $this->get('/sitemap.xml')->assertOk();

        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertDontSee($hidden->slug);
        $response->assertSee(Service::first()->slug);
    }

    public function test_robots_points_at_the_sitemap_and_blocks_admin(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('sitemap.xml');
    }

    public function test_seo_metadata_is_rendered_server_side(): void
    {
        $project = Project::first();

        $this->get('/realisations/'.$project->slug)
            ->assertOk()
            ->assertSee('<meta property="og:type" content="article">', escape: false)
            ->assertSee('rel="canonical"', escape: false)
            ->assertSee('application/ld+json', escape: false);
    }

    public function test_legal_pages_are_excluded_from_indexing(): void
    {
        $this->get('/mentions-legales')
            ->assertOk()
            ->assertSee('name="robots" content="noindex, nofollow"', escape: false);
    }

    public function test_post_view_counter_increments(): void
    {
        $post = Post::published()->first();
        $before = $post->views;

        $this->get('/actualites/'.$post->slug)->assertOk();

        $this->assertSame($before + 1, $post->fresh()->views);
    }
}
