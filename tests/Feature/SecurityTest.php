<?php

namespace Tests\Feature;

use App\Models\AssistanceRequest;
use App\Models\Campaign;
use App\Models\Project;
use App\Models\User;
use App\Support\SafeHtml;
use App\Support\V7\ContentInstaller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Simulated attacks and protections (security audit of 17/11/2026). */
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        ContentInstaller::install();
        Storage::fake('local');
        Mail::fake();
    }

    private function project(array $over = []): array
    {
        return array_merge(['need' => 'construction', 'location' => 'Douala', 'name' => 'Test Sécurité', 'phone' => '+237 670 000 000', 'contact_preference' => 'telephone', 'privacy' => '1'], $over);
    }

    private function openCampaign(): void
    {
        Campaign::first()->update(['status' => 'open', 'announced_at' => now()->subDay(), 'starts_at' => now()->subDay(), 'ends_at' => now()->addMonth()]);
        config(['site.cdp.slots.consultation' => null, 'site.cdp.slots.visit' => null]);
    }

    public function test_security_headers_and_strict_csp_on_public_pages(): void
    {
        $r = $this->get('/coup-de-pouce')->assertOk();
        foreach (['X-Content-Type-Options' => 'nosniff', 'X-Frame-Options' => 'SAMEORIGIN', 'Referrer-Policy' => 'strict-origin-when-cross-origin'] as $h => $v) {
            $r->assertHeader($h, $v);
        }
        $r->assertHeader('Permissions-Policy');
        $csp = $r->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
        $this->assertStringNotContainsString("'unsafe-inline' 'unsafe-eval'", $csp);
        $this->assertStringNotContainsString("script-src 'self' 'unsafe-inline'", $csp);

        // Every inline script carries the nonce of this response.
        preg_match("/'nonce-([^']+)'/", $csp, $m);
        preg_match_all('/<script(?![^>]*\bsrc=)(?![^>]*application\/json)[^>]*>/', $r->getContent(), $tags);
        foreach ($tags[0] as $tag) {
            $this->assertStringContainsString('nonce="'.$m[1].'"', $tag);
        }
        $this->assertNotSame($m[1], $this->get('/')->headers->get('Content-Security-Policy') ? preg_replace("/.*'nonce-([^']+)'.*/s", '$1', $this->get('/')->headers->get('Content-Security-Policy')) : '');
    }

    public function test_admin_pages_have_security_headers_and_are_not_indexed(): void
    {
        $r = $this->get('/admin/login')->assertOk();
        $r->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $r->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->assertStringContainsString("frame-ancestors 'self'", $r->headers->get('Content-Security-Policy'));
    }

    public function test_forged_ip_headers_do_not_bypass_rate_limits(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            $res = $this->withHeaders(['X-Forwarded-For' => "203.0.113.{$i}"])->post('/mon-projet', $this->project(['name' => "Spam {$i}"]));
        }
        $res->assertStatus(429);
    }

    public function test_admin_rich_text_cannot_inject_scripts(): void
    {
        $dirty = '<p onclick="steal()">Texte<script>alert(1)</script><img src=x onerror=alert(2)><a href="javascript:alert(3)">lien</a><iframe src="https://evil.test"></iframe></p>';
        $clean = SafeHtml::clean($dirty);
        foreach (['<script', 'onclick', 'onerror', 'javascript:', '<iframe'] as $bad) {
            $this->assertStringNotContainsStringIgnoringCase($bad, $clean);
        }
        $this->assertStringContainsString('Texte', $clean);

        $p = Project::published()->firstOrFail();
        $p->update(['body_fr' => $dirty]);
        $page = $this->get('/projets/'.$p->slug)->assertOk()->getContent();
        $this->assertStringNotContainsString('alert(1)', $page);
        $this->assertStringNotContainsString('steal()', $page);
    }

    public function test_structured_data_cannot_break_out_of_its_script_tag(): void
    {
        $p = Project::published()->firstOrFail();
        $p->update(['title_fr' => 'Villa</script><script>alert(1)</script>']);
        $page = $this->get('/projets/'.$p->slug)->getContent();
        $this->assertStringNotContainsString('</script><script>alert(1)', $page);
    }

    public function test_disguised_or_forbidden_files_are_rejected(): void
    {
        // A script or an executable renamed .pdf / .dwg: the real content is checked, not the name.
        foreach (['plan.pdf' => '<?php system($_GET["c"]); ?>', 'plan.dwg' => "MZ\x90\x00binary-executable", 'photo.jpg' => '<html><script>x</script></html>'] as $name => $content) {
            $tmp = tempnam(sys_get_temp_dir(), 'up');
            file_put_contents($tmp, $content);
            $file = new UploadedFile($tmp, $name, null, null, true);
            $this->post('/mon-projet', $this->project(['attachments' => [$file]]))->assertSessionHasErrors('attachments.0');
        }
        // A genuine PDF is still accepted.
        $tmp = tempnam(sys_get_temp_dir(), 'up');
        file_put_contents($tmp, "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF");
        $this->post('/mon-projet', $this->project(['attachments' => [new UploadedFile($tmp, 'plan.pdf', null, null, true)]]))->assertSessionHasNoErrors();

        $this->openCampaign();
        $svg = UploadedFile::fake()->createWithContent('photo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
        $this->post('/coup-de-pouce/demandes', [
            'path' => 'visit', 'idempotency_key' => 'sec-svg-0000000001', 'city' => 'Douala', 'site_location' => 'Akwa', 'project_type' => 'maison',
            'progress' => 'toiture', 'description' => 'Infiltrations en toiture', 'name' => 'Test', 'phone' => '670000000', 'privacy' => '1', 'attachments' => [$svg],
        ])->assertSessionHasErrors('attachments.0');
        $this->assertSame(0, AssistanceRequest::count());
    }

    public function test_public_forms_cannot_set_internal_fields(): void
    {
        $this->openCampaign();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->post('/coup-de-pouce/demandes', [
            'path' => 'consultation', 'idempotency_key' => 'sec-mass-000000001', 'city' => 'Douala', 'project_type' => 'maison', 'progress' => 'plans',
            'description' => 'Question sur mes plans', 'name' => 'Test', 'phone' => '670000000', 'privacy' => '1',
            'status' => 'closed', 'assigned_to' => $admin->id, 'reference' => 'CDP-26-AAAAAA', 'internal_notes' => 'x', 'source' => 'office',
        ])->assertRedirect();
        $r = AssistanceRequest::firstOrFail();
        $this->assertSame('received', $r->status);
        $this->assertNull($r->assigned_to);
        $this->assertNotSame('CDP-26-AAAAAA', $r->reference);
        $this->assertNull($r->internal_notes);
        $this->assertSame('web', $r->source);
    }

    public function test_honeypot_blocks_bots(): void
    {
        $this->post('/mon-projet', $this->project(['website' => 'http://spam.test']))->assertSessionHasErrors('website');
        $this->post('/newsletter', ['email' => 'bot@spam.test', 'website' => 'x'])->assertSessionHasErrors('website');
    }

    public function test_admin_is_closed_to_visitors_and_limited_by_role(): void
    {
        $this->get('/admin')->assertRedirect();
        $this->get('/admin/users')->assertRedirect();

        $ref = User::factory()->create(['role' => 'referent']);
        $com = User::factory()->create(['role' => 'communication']);
        foreach (['/admin/users', '/admin/campaigns', '/admin/quote-requests', '/admin/job-applications'] as $u) {
            $this->actingAs($ref)->get($u)->assertForbidden();
            $this->actingAs($com)->get($u)->assertForbidden();
        }
        // Unknown roles never get in.
        $odd = User::factory()->create(['role' => 'visiteur']);
        $this->actingAs($odd)->get('/admin')->assertForbidden();
    }

    public function test_admin_passwords_must_be_strong(): void
    {
        $v = validator(['p' => 'feirry2026'], ['p' => \Illuminate\Validation\Rules\Password::defaults()]);
        $this->assertTrue($v->fails());
        $v = validator(['p' => 'Chantier-Douala-2026'], ['p' => \Illuminate\Validation\Rules\Password::defaults()]);
        $this->assertFalse($v->fails());
    }

    public function test_debug_mode_is_forced_off_in_production(): void
    {
        config(['app.debug' => true]);
        $this->app['env'] = 'production';
        (new \App\Providers\AppServiceProvider($this->app))->boot();
        $this->assertFalse(config('app.debug'));
    }

    public function test_session_cookies_are_secure_in_production_by_default(): void
    {
        $this->assertSame('lax', config('session.same_site'));
        $this->assertTrue(config('session.http_only'));
        $cfg = require base_path('config/session.php');
        $this->assertArrayHasKey('secure', $cfg);
    }

    public function test_confirmation_cannot_be_used_to_read_another_request(): void
    {
        $this->openCampaign();
        $this->post('/coup-de-pouce/demandes', ['path' => 'consultation', 'idempotency_key' => 'sec-conf-00000001', 'city' => 'Douala', 'project_type' => 'maison', 'progress' => 'plans', 'description' => 'Question sur mes plans', 'name' => 'Victime', 'phone' => '670000000', 'privacy' => '1']);
        $ref = AssistanceRequest::firstOrFail()->reference;
        $this->flushSession();
        $this->get('/coup-de-pouce/confirmation?reference='.$ref)->assertRedirect('/coup-de-pouce');
        $this->assertStringNotContainsString('Victime', $this->get('/coup-de-pouce')->getContent());
    }
}
