<?php

namespace Tests\Feature;

use App\Models\AssistanceRequest;
use App\Models\Campaign;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Models\User;
use App\Support\V7\ContentInstaller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Automated part of the CDC V7 test matrix (§13). */
class V7SiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        ContentInstaller::install();
        Storage::fake('local');
    }

    private function validProject(array $over = []): array
    {
        return array_merge([
            'need' => 'construction',
            'location' => 'Bonamoussadi, Douala',
            'name' => 'Client Test',
            'phone' => '+237 670 000 000',
            'contact_preference' => 'telephone',
            'privacy' => '1',
        ], $over);
    }

    /* NAV-01 — every section of the menu opens, in both languages */
    public function test_every_v7_page_responds_in_both_languages(): void
    {
        foreach (['/', '/feirry', '/expertises', '/expertises/geotechnique', '/projets', '/projets/tours-jumelles-japoma', '/offres', '/coup-de-pouce', '/conseils', '/mon-projet', '/mentions-legales', '/confidentialite',
            '/en', '/en/feirry', '/en/expertise', '/en/projects', '/en/offers', '/en/coup-de-pouce', '/en/advice', '/en/my-project', '/en/legal-notice', '/en/privacy'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    /* SEO-01 — old URLs are redirected permanently */
    public function test_old_urls_redirect_permanently(): void
    {
        $map = ['/entreprise' => '/feirry', '/methode' => '/feirry#methode', '/equipe' => '/feirry', '/faq' => '/feirry', '/metiers' => '/expertises',
            '/metiers/gros-oeuvre' => '/expertises', '/realisations' => '/projets', '/actualites' => '/conseils', '/devis' => '/mon-projet', '/contact' => '/mon-projet',
            '/realisations/tours-jumelles-japoma' => '/projets/tours-jumelles-japoma', '/en/company' => '/en/feirry', '/en/quote' => '/en/my-project'];

        foreach ($map as $from => $to) {
            $this->get($from)->assertStatus(301)->assertRedirect($to);
        }
    }

    public function test_sitemap_lists_the_v7_pages_and_no_old_urls(): void
    {
        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString('/coup-de-pouce', $xml);
        $this->assertStringContainsString('/en/offers', $xml);
        $this->assertStringNotContainsString('/realisations', $xml);
        $this->assertStringNotContainsString('/devis', $xml);
    }

    /* Content: nothing invented, drafts never public */
    public function test_demonstration_content_is_not_public(): void
    {
        $html = $this->get('/')->getContent();

        foreach (['2009', 'Bonanjo', '42 engins', 'ISO 9001', 'BP 12457'] as $fake) {
            $this->assertStringNotContainsString($fake, $html);
        }

        $this->assertStringContainsString('RC\\/DLA\\/2021\\/B\\/72', $html);
    }

    public function test_draft_and_archived_projects_are_hidden(): void
    {
        Project::where('slug', 'duplex-batcham')->update(['visibility' => 'draft']);
        $this->get('/projets/duplex-batcham')->assertNotFound();
        $this->assertStringNotContainsString('duplex-batcham', $this->get('/sitemap.xml')->getContent());
    }

    /* FOR-01 — a valid request is really stored, with a reference and a private file */
    public function test_a_project_request_is_stored_with_reference_and_private_document(): void
    {
        Mail::fake();
        config(['site.leads.notify' => 'equipe@example.test']);

        $this->post('/mon-projet', $this->validProject([
            'attachments' => [UploadedFile::fake()->create('plan.pdf', 200, 'application/pdf')],
        ]))->assertRedirect()->assertSessionHas('reference');

        $lead = QuoteRequest::firstOrFail();
        $this->assertStringStartsWith('FEI-', $lead->reference);
        $this->assertNotNull($lead->privacy_accepted_at);
        Storage::disk('local')->assertExists($lead->attachments[0]['path']);
        $this->assertFileDoesNotExist(public_path('storage/'.$lead->attachments[0]['path']));
        Mail::assertSent(\App\Mail\NewLeadMail::class);
    }

    public function test_phone_or_email_is_enough_but_one_is_required(): void
    {
        $this->post('/mon-projet', $this->validProject(['phone' => null, 'email' => 'client@example.com']))->assertSessionHas('reference');
        $this->post('/mon-projet', $this->validProject(['phone' => null, 'email' => null, 'name' => 'Autre']))->assertSessionHasErrors(['phone', 'email']);
    }

    /* FOR-02 — invalid data gives clear errors */
    public function test_invalid_project_requests_are_rejected(): void
    {
        $this->post('/mon-projet', [])->assertSessionHasErrors(['need', 'location', 'name', 'privacy']);
        $this->post('/mon-projet', $this->validProject(['phone' => '123']))->assertSessionHasErrors('phone');
        $this->post('/mon-projet', $this->validProject(['website' => 'spam']))->assertSessionHasErrors('website');
        $this->assertSame(0, QuoteRequest::count());
    }

    /* UPL-01 — forbidden or oversized files are rejected */
    public function test_forbidden_or_oversized_files_are_rejected(): void
    {
        $this->post('/mon-projet', $this->validProject(['attachments' => [UploadedFile::fake()->create('virus.exe', 10, 'application/x-msdownload')]]))
            ->assertSessionHasErrors('attachments.0');
        $this->post('/mon-projet', $this->validProject(['attachments' => [UploadedFile::fake()->create('big.pdf', 11000, 'application/pdf')]]))
            ->assertSessionHasErrors('attachments.0');
        $this->assertSame(0, QuoteRequest::count());
    }

    public function test_a_resubmitted_request_is_not_duplicated(): void
    {
        $this->post('/mon-projet', $this->validProject());
        $this->post('/mon-projet', $this->validProject());
        $this->assertSame(1, QuoteRequest::count());
    }

    /* CAM-01 — a closed campaign accepts nothing */
    public function test_closed_campaign_refuses_requests(): void
    {
        $this->post('/coup-de-pouce', ['path' => 'consultation', 'name' => 'A B', 'phone' => '670000000', 'city' => 'Douala', 'description' => 'x', 'meeting_mode' => 'office', 'privacy' => '1'])
            ->assertSessionHas('error', 'campaign_closed');
        $this->assertSame(0, AssistanceRequest::count());
    }

    public function test_a_past_campaign_never_shows_as_open(): void
    {
        $c = Campaign::first();
        $c->update(['status' => 'open', 'starts_at' => now()->subMonth(), 'ends_at' => now()->subDay()]);
        $this->assertFalse($c->fresh()->isOpen());
        $this->assertNull(Campaign::current());
    }

    public function test_open_campaign_stores_a_site_visit_request(): void
    {
        Campaign::first()->update(['status' => 'open', 'starts_at' => now()->subDay(), 'ends_at' => now()->addMonth()]);

        $this->post('/coup-de-pouce', ['path' => 'visit', 'name' => 'A B', 'phone' => '670000000', 'privacy' => '1'])
            ->assertSessionHasErrors(['site_location', 'objective', 'availability']);

        $this->post('/coup-de-pouce', ['path' => 'visit', 'name' => 'A B', 'phone' => '670000000', 'site_location' => 'Japoma', 'objective' => 'Avis sur le terrain', 'availability' => 'Samedi matin', 'privacy' => '1'])
            ->assertSessionHas('reference');

        $this->assertStringStartsWith('CDP-', AssistanceRequest::firstOrFail()->reference);
    }

    public function test_campaign_capacity_closes_registration(): void
    {
        $c = Campaign::first();
        $c->update(['status' => 'open', 'capacity' => 1]);
        AssistanceRequest::create(['campaign_id' => $c->id, 'path' => 'visit', 'name' => 'X']);
        $this->assertFalse($c->fresh()->isOpen());
    }

    /* DATA-01 — documents only for signed-in administrators */
    public function test_lead_documents_require_an_administrator(): void
    {
        $this->post('/mon-projet', $this->validProject(['attachments' => [UploadedFile::fake()->create('plan.pdf', 20, 'application/pdf')]]));
        $lead = QuoteRequest::firstOrFail();
        $url = "/admin/fichiers/quote/{$lead->id}/0";

        $this->get($url)->assertForbidden();
        $this->actingAs(User::factory()->create())->get($url)->assertOk();
    }

    public function test_no_secret_is_sent_to_the_browser(): void
    {
        $html = $this->get('/')->getContent();

        foreach (['APP_KEY', 'MAIL_PASSWORD', 'DB_PASSWORD', config('app.key')] as $secret) {
            $this->assertStringNotContainsString($secret, $html);
        }
    }
}
