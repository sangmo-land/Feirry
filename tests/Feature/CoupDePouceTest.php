<?php

namespace Tests\Feature;

use App\Models\AssistanceRequest;
use App\Models\Campaign;
use App\Models\User;
use App\Support\V7\ContentInstaller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Automated part of the Coup de Pouce V2 test plan (CDC §22). */
class CoupDePouceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        ContentInstaller::install();
        Storage::fake('local');
        Mail::fake();
    }

    private function open(array $over = []): Campaign
    {
        $c = Campaign::firstOrFail();
        $c->update(array_merge(['status' => 'open', 'announced_at' => now()->subDay(), 'starts_at' => now()->subDay(), 'ends_at' => now()->addMonth()], $over));

        return $c->fresh();
    }

    private function formA(array $over = []): array
    {
        return array_merge([
            'path' => 'consultation', 'idempotency_key' => 'key-a-'.uniqid(),
            'city' => 'Bonamoussadi, Douala', 'project_type' => 'maison', 'progress' => 'plans',
            'land_area' => 'inconnu', 'levels' => 'r+1', 'description' => 'Mes plans respectent-ils les règles ?',
            'name' => 'Client Test', 'phone' => '+237 670 000 000', 'meeting_mode' => 'office', 'privacy' => '1',
        ], $over);
    }

    private function formB(array $over = []): array
    {
        return array_merge([
            'path' => 'visit', 'idempotency_key' => 'key-b-'.uniqid(),
            'city' => 'Logbessou, Douala', 'site_location' => 'Derrière la pharmacie', 'project_type' => 'maison', 'progress' => 'elevation',
            'description' => 'Fissures sur un mur porteur', 'name' => 'Client Test', 'phone' => '670000000', 'privacy' => '1',
        ], $over);
    }

    /* NAV — programme page, both forms and the EN version respond */
    public function test_programme_pages_respond(): void
    {
        foreach (['/coup-de-pouce', '/coup-de-pouce/consultation', '/coup-de-pouce/visite', '/en/coup-de-pouce', '/en/coup-de-pouce/consultation', '/en/coup-de-pouce/visite'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    /* CAM-01 — in preparation the forms are closed and nothing is stored */
    public function test_preparation_edition_refuses_requests(): void
    {
        $this->assertSame('preparation', Campaign::first()->publicState());
        $this->post('/coup-de-pouce/demandes', $this->formA())->assertSessionHas('error', 'campaign_closed');
        $this->assertSame(0, AssistanceRequest::count());
    }

    /* CAM-02 — a past edition is never shown as open */
    public function test_a_past_edition_never_shows_as_open(): void
    {
        $c = $this->open(['announced_at' => now()->subMonths(2), 'starts_at' => now()->subMonth(), 'ends_at' => now()->subDay()]);
        $this->assertFalse($c->accepting());
        $this->assertNull(Campaign::current());
        $this->assertSame('closed', $c->publicState());
    }

    /* CAM-03 — paused: closed, unless the waiting list is enabled */
    public function test_paused_edition_and_waiting_list(): void
    {
        $c = $this->open(['status' => 'paused']);
        $this->assertSame('paused', $c->publicState());
        $this->post('/coup-de-pouce/demandes', $this->formA())->assertSessionHas('error', 'campaign_closed');

        $c->update(['waitlist' => true]);
        $this->assertSame('waitlist', $c->fresh()->publicState());
        $this->post('/coup-de-pouce/demandes', $this->formA())->assertRedirect('/coup-de-pouce/confirmation');
    }

    /* CAM-03b — a request sent on the waiting list is stored as « En attente » and told so */
    public function test_waitlist_request_is_marked_waiting(): void
    {
        $this->open(['status' => 'paused', 'waitlist' => true]);
        $this->post('/coup-de-pouce/demandes', $this->formA())->assertRedirect('/coup-de-pouce/confirmation');
        $this->assertSame('waiting', AssistanceRequest::firstOrFail()->status);
        $this->get('/coup-de-pouce/confirmation')->assertOk()->assertSee('waiting', false);
    }

    /* SEO — form pages are listed, the confirmation is not */
    public function test_sitemap_lists_forms_but_not_confirmation(): void
    {
        $xml = $this->get('/sitemap.xml')->getContent();
        $this->assertStringContainsString('/coup-de-pouce/consultation', $xml);
        $this->assertStringContainsString('/coup-de-pouce/visite', $xml);
        $this->assertStringNotContainsString('/coup-de-pouce/confirmation', $xml);
    }

    /* COM — communication and referents never reach project-request plans or contacts */
    public function test_other_inbound_requests_are_administration_only(): void
    {
        $this->post('/mon-projet', ['need' => 'construction', 'location' => 'Douala', 'name' => 'Client Test', 'phone' => '+237 670 000 000', 'contact_preference' => 'telephone', 'privacy' => '1', 'attachments' => [UploadedFile::fake()->create('plan.pdf', 20, 'application/pdf')]]);
        $q = \App\Models\QuoteRequest::firstOrFail();
        foreach (['communication', 'referent'] as $role) {
            $u = User::factory()->create(['role' => $role]);
            $this->actingAs($u)->get("/admin/fichiers/quote/{$q->id}/0")->assertForbidden();
            $this->actingAs($u)->get('/admin/quote-requests')->assertForbidden();
            $this->actingAs($u)->get('/admin')->assertOk();
        }
        $this->actingAs(User::factory()->create(['role' => 'administration']))->get("/admin/fichiers/quote/{$q->id}/0")->assertOk();
    }

    /* CAM-04 — an announced future opening shows « ouverture prochaine » */
    public function test_upcoming_edition(): void
    {
        $c = $this->open(['announced_at' => now()->addDays(3), 'starts_at' => now()->addDays(6)]);
        $this->assertSame('upcoming', $c->publicState());
        $this->assertFalse($c->accepting());
    }

    /* PUB-01 — internal capacity is never sent to the browser */
    public function test_internal_capacity_is_never_public(): void
    {
        $public = $this->open()->toPublic('fr');
        $this->assertSame(10, $public['public_consultations']);
        $this->assertArrayNotHasKey('internal_consultations', $public);
        $this->assertArrayNotHasKey('internal_visits', $public);
        $this->assertStringNotContainsString('internal_', $this->get('/coup-de-pouce')->getContent());
    }

    /* FA-01 — form A stored, no budget asked, reference format, confirmation */
    public function test_form_a_is_stored_and_confirmed(): void
    {
        $this->open();
        $this->post('/coup-de-pouce/demandes', $this->formA(['budget' => '50000000', 'email' => 'client@example.com']))
            ->assertRedirect('/coup-de-pouce/confirmation');

        $r = AssistanceRequest::firstOrFail();
        $this->assertMatchesRegularExpression('/^CDP-\d{2}-[A-HJ-NP-Z2-9]{6}$/', $r->reference);
        $this->assertNull($r->land_area);
        $this->assertSame('received', $r->status);
        $this->assertArrayNotHasKey('budget', $r->getAttributes());
        $this->assertNotNull($r->privacy_accepted_at);
        $this->assertFalse((bool) $r->marketing_consent);
        $this->assertSame(1, $r->logs()->count());
        Mail::assertSent(\App\Mail\AssistanceReceivedMail::class);

        $this->get('/coup-de-pouce/confirmation')->assertOk()->assertSee('noindex', false);
    }

    /* FA-02 — required fields */
    public function test_form_a_requires_its_fields(): void
    {
        $this->open();
        $this->post('/coup-de-pouce/demandes', ['path' => 'consultation', 'idempotency_key' => 'key-empty-0001'])
            ->assertSessionHasErrors(['city', 'project_type', 'progress', 'description', 'name', 'phone', 'privacy']);
    }

    /* FB-01 — form B stored; out-of-zone accepted and flagged; danger kept */
    public function test_form_b_is_stored_and_flags_zone_and_danger(): void
    {
        $this->open();
        $this->post('/coup-de-pouce/demandes', $this->formB())->assertRedirect('/coup-de-pouce/confirmation');
        $this->post('/coup-de-pouce/demandes', $this->formB(['city' => 'Yaoundé', 'site_location' => 'Bastos', 'danger' => '1']))->assertRedirect();

        [$a, $b] = AssistanceRequest::orderBy('id')->get()->all();
        $this->assertFalse($a->out_of_zone);
        $this->assertTrue($b->out_of_zone);
        $this->assertTrue($b->danger_reported);
    }

    public function test_form_b_requires_a_landmark(): void
    {
        $this->open();
        $this->post('/coup-de-pouce/demandes', $this->formB(['site_location' => '']))->assertSessionHasErrors('site_location');
    }

    /* UP-01 — allowed files stored privately; wrong type or size refused */
    public function test_uploads_are_private_and_checked(): void
    {
        $this->open();
        $this->post('/coup-de-pouce/demandes', $this->formA(['attachments' => [UploadedFile::fake()->create('plan.pdf', 200, 'application/pdf')]]))->assertRedirect();
        $r = AssistanceRequest::firstOrFail();
        $this->assertStringStartsWith($r->storageFolder('originaux'), $r->attachments[0]['path']);
        Storage::disk('local')->assertExists($r->attachments[0]['path']);

        $this->post('/coup-de-pouce/demandes', $this->formA(['attachments' => [UploadedFile::fake()->image('photo.jpg')]]))->assertSessionHasErrors('attachments.0');
        $this->post('/coup-de-pouce/demandes', $this->formA(['attachments' => [UploadedFile::fake()->create('big.pdf', 16000, 'application/pdf')]]))->assertSessionHasErrors('attachments.0');
        $this->post('/coup-de-pouce/demandes', $this->formB(['attachments' => [UploadedFile::fake()->create('plan.pdf', 20, 'application/pdf')]]))->assertSessionHasErrors('attachments.0');
        $this->assertSame(1, AssistanceRequest::count());
    }

    /* DUP-01 — the same submission sent twice creates one request */
    public function test_a_resent_submission_is_not_duplicated(): void
    {
        $this->open();
        $data = $this->formA(['idempotency_key' => 'same-key-123456789']);
        $this->post('/coup-de-pouce/demandes', $data)->assertRedirect('/coup-de-pouce/confirmation');
        $this->post('/coup-de-pouce/demandes', $data)->assertRedirect('/coup-de-pouce/confirmation');
        $this->assertSame(1, AssistanceRequest::count());
    }

    /* CONF-01 — the confirmation page is only reachable after a real save */
    public function test_confirmation_requires_a_saved_request(): void
    {
        $this->get('/coup-de-pouce/confirmation')->assertRedirect('/coup-de-pouce');
    }

    /* SLA — report due 5 working days after « Réalisée », history kept */
    public function test_status_changes_are_logged_and_report_due_is_set(): void
    {
        $this->open();
        $this->post('/coup-de-pouce/demandes', $this->formB());
        $r = AssistanceRequest::firstOrFail();
        $r->update(['status' => 'done']);
        $this->assertNotNull($r->fresh()->report_due_at);
        $this->assertGreaterThanOrEqual(2, $r->logs()->count());
    }

    /* SEC-02 — referents only see what is assigned to them; communication sees nothing */
    public function test_roles_restrict_access_to_requests_and_files(): void
    {
        $this->open();
        $this->post('/coup-de-pouce/demandes', $this->formA(['attachments' => [UploadedFile::fake()->create('plan.pdf', 20, 'application/pdf')]]));
        $r = AssistanceRequest::firstOrFail();
        $url = "/admin/fichiers/assistance/{$r->id}/0";

        $ref = User::factory()->create(['role' => 'referent']);
        $com = User::factory()->create(['role' => 'communication']);
        $admin = User::factory()->create(['role' => 'administration']);

        $this->get($url)->assertForbidden();
        $this->actingAs($com)->get($url)->assertForbidden();
        $this->actingAs($ref)->get($url)->assertForbidden();
        $this->actingAs($admin)->get($url)->assertOk();

        $r->update(['assigned_to' => $ref->id]);
        $this->actingAs($ref)->get($url)->assertOk();
        $this->assertTrue($r->logs()->where('action', 'download')->exists());
    }

    public function test_admin_screens_render(): void
    {
        $this->open();
        $this->post('/coup-de-pouce/demandes', $this->formB());
        $r = AssistanceRequest::firstOrFail();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/assistance-requests')->assertOk()->assertSee($r->reference);
        $this->actingAs($admin)->get("/admin/assistance-requests/{$r->id}/edit")->assertOk();
        $this->actingAs($admin)->get('/admin/assistance-requests/create')->assertOk();
        $this->actingAs($admin)->get('/admin/campaigns')->assertOk();
        $this->actingAs($admin)->get('/admin/assistance-requests?tab=planning')->assertOk();
        $this->actingAs($admin)->get('/admin')->assertOk();
        \Livewire\Livewire::actingAs($admin)->test(\App\Filament\Widgets\CdpOverview::class)->assertSee('Coup de Pouce — reçues')->assertSee('Programmées');
        \Livewire\Livewire::actingAs($admin)->test(\App\Filament\Resources\AssistanceRequests\Pages\ListAssistanceRequests::class, ['activeTab' => 'planning'])->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/admin/campaigns/'.Campaign::first()->id.'/edit')->assertOk();

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\AssistanceRequests\Pages\EditAssistanceRequest::class, ['record' => $r->getRouteKey()])
            ->fillForm(['status' => 'scheduled', 'scheduled_mode' => 'site', 'scheduled_at' => now()->addDays(2)->setTime(10, 0)->toDateTimeString()])
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertSame('scheduled', $r->fresh()->status);
        $this->assertStringContainsString('Rendez-vous le', \App\Support\CdpMessages::render('scheduled', $r->fresh()));

        $com = User::factory()->create(['role' => 'communication']);
        $this->actingAs($com)->get('/admin/assistance-requests')->assertForbidden();
    }
}
