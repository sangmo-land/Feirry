<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Support\Seo;
use Database\Seeders\ContentSeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * What a crawler is handed.
 *
 * The site is client-rendered, so almost nothing these assertions look at is
 * visible in a browser's view-source out of habit — it is there because
 * something puts it there deliberately, and a refactor that drops it would
 * otherwise show up only as traffic that stops arriving.
 */
class SearchEngineTest extends TestCase
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
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* robots.txt */
    /* ------------------------------------------------------------------ */

    public function test_robots_txt_is_served_by_the_application(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('Sitemap: '.route('sitemap'), false);
        $response->assertSee('Disallow: /admin', false);
    }

    /**
     * A file in public/ is handed over by the web server before Laravel is
     * reached, which is how the route above ends up dead in production while
     * still passing every test that calls it directly.
     */
    public function test_no_static_robots_file_shadows_the_route(): void
    {
        $this->assertFileDoesNotExist(public_path('robots.txt'));
    }

    /* ------------------------------------------------------------------ */
    /* sitemap.xml */
    /* ------------------------------------------------------------------ */

    public function test_sitemap_is_valid_xml_and_lists_both_languages(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = simplexml_load_string($response->getContent());

        $this->assertNotFalse($xml, 'The sitemap did not parse as XML.');

        $locations = [];

        foreach ($xml->url as $url) {
            $locations[] = (string) $url->loc;
        }

        $this->assertContains(route('home'), $locations);
        $this->assertContains(route('en.home'), $locations);
        $this->assertContains(route('feirry'), $locations);
        $this->assertContains(route('en.feirry'), $locations);
    }

    /**
     * Google reads two URLs as translations of one page only when each of them
     * names the other. A one-way declaration is ignored, and the two languages
     * go on competing for the same query.
     */
    public function test_every_sitemap_entry_declares_reciprocal_alternates(): void
    {
        $xml = simplexml_load_string($this->get('/sitemap.xml')->getContent());

        $this->assertNotEmpty($xml->url);

        foreach ($xml->url as $url) {
            // Walked rather than collected: casting a SimpleXMLElement to an
            // array yields its children, so collect() over a node list quietly
            // returns the first node's contents instead of the list.
            $alternates = [];

            foreach ($url->children('xhtml', true)->link as $link) {
                $alternates[(string) $link->attributes()['hreflang']] = (string) $link->attributes()['href'];
            }

            $this->assertArrayHasKey('fr', $alternates, "Missing fr alternate on {$url->loc}");
            $this->assertArrayHasKey('en', $alternates, "Missing en alternate on {$url->loc}");
            $this->assertArrayHasKey('x-default', $alternates, "Missing x-default on {$url->loc}");

            // The entry has to appear in its own alternate list, or the group
            // is not reciprocal.
            $this->assertContains((string) $url->loc, array_values($alternates));

            // x-default belongs to the language that owns the root.
            $this->assertSame($alternates['fr'], $alternates['x-default']);
        }
    }

    /* ------------------------------------------------------------------ */
    /* Verification */
    /* ------------------------------------------------------------------ */

    public function test_search_console_verification_tag_is_emitted_when_a_token_is_set(): void
    {
        config()->set('site.seo.verification.google', 'test-google-token');

        $this->get('/')->assertSee(
            '<meta name="google-site-verification" content="test-google-token">',
            false,
        );
    }

    /** An empty content attribute reads to the verifier as a failed proof. */
    public function test_no_verification_tag_is_emitted_without_a_token(): void
    {
        config()->set('site.seo.verification.google', null);
        config()->set('site.seo.verification.bing', null);

        $this->get('/')->assertDontSee('google-site-verification', false);
    }

    /* ------------------------------------------------------------------ */
    /* The location section */
    /* ------------------------------------------------------------------ */

    /**
     * The address in the structured data and the address on the page are the
     * same string. Dashboard edits reach the markup through SiteSettings, and
     * a schema still quoting config would quietly start describing a different
     * building than the one the page names.
     */
    public function test_schema_follows_the_address_edited_from_the_dashboard(): void
    {
        Setting::put('contact.address', 'Rue Njo-Njo, Immeuble Sawa');
        Setting::put('contact.city', 'Kribi');

        $schema = $this->schemaFrom($this->get('/')->getContent());

        $this->assertStringContainsString('Rue Njo-Njo', $schema['address']['streetAddress']);
        $this->assertSame('Kribi', $schema['address']['addressLocality']);
    }

    /** The one JSON-LD block in the document head, decoded. */
    protected function schemaFrom(string $html): array
    {
        preg_match(
            '#<script type="application/ld\+json">(.*?)</script>#s',
            $html,
            $matches,
        );

        $this->assertNotEmpty($matches, 'No JSON-LD block found in the page.');

        $decoded = json_decode(trim($matches[1]), true);

        $this->assertIsArray($decoded, 'The JSON-LD block did not decode: '.json_last_error_msg());

        return $decoded;
    }
}
