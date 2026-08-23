<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\NewsletterSubscriber;
use App\Models\QuoteRequest;
use App\Models\Service;
use Database\Seeders\CareerSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FormSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([ServiceSeeder::class, CareerSeeder::class]);
    }

    /* ---------------------------------------------------------------- */
    /* Quote requests                                                    */
    /* ---------------------------------------------------------------- */

    public function test_a_quote_request_is_stored_with_a_reference(): void
    {
        $response = $this->from('/devis')->post('/devis', [
            'name' => 'Thierry Kamdem',
            'email' => 'thierry@example.cm',
            'phone' => '+237 6 99 00 11 22',
            'project_type' => 'residential',
            'location' => 'Bonamoussadi, Douala',
            'service_id' => Service::first()->id,
            'budget_range' => '50_150',
            'timeline' => '6_months',
            'surface_m2' => 450,
            'message' => 'Villa R+1 sur un terrain de 600 m².',
        ]);

        $response->assertRedirect('/devis')->assertSessionHas('reference');

        $quote = QuoteRequest::sole();

        $this->assertSame('Thierry Kamdem', $quote->name);
        $this->assertSame('new', $quote->status);
        $this->assertMatchesRegularExpression('/^DEV-\d{6}-[A-Z0-9]{4}$/', $quote->reference);
    }

    public function test_a_quote_request_requires_the_essential_fields(): void
    {
        $this->from('/devis')
            ->post('/devis', ['name' => 'A'])
            ->assertSessionHasErrors(['name', 'email', 'phone', 'project_type', 'location']);

        $this->assertSame(0, QuoteRequest::count());
    }

    public function test_the_quote_honeypot_rejects_bots(): void
    {
        $this->from('/devis')->post('/devis', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'phone' => '+237600000000',
            'project_type' => 'other',
            'location' => 'Douala',
            'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertSame(0, QuoteRequest::count());
    }

    public function test_a_quote_attachment_is_stored_on_the_public_disk(): void
    {
        Storage::fake('public');

        $this->post('/devis', [
            'name' => 'Marcel Ebongue',
            'email' => 'marcel@example.cm',
            'phone' => '+237 6 99 00 11 22',
            'project_type' => 'commercial',
            'location' => 'Bonanjo, Douala',
            'attachment' => UploadedFile::fake()->create('plans.pdf', 400, 'application/pdf'),
        ])->assertRedirect();

        $quote = QuoteRequest::sole();

        $this->assertNotNull($quote->attachment);
        Storage::disk('public')->assertExists($quote->attachment);
    }

    /* ---------------------------------------------------------------- */
    /* Contact                                                           */
    /* ---------------------------------------------------------------- */

    public function test_a_contact_message_is_stored_unread(): void
    {
        $this->post('/contact', [
            'name' => 'Estelle Nguema',
            'email' => 'estelle@example.cm',
            'phone' => '+237 6 77 88 99 00',
            'subject' => 'Réhabilitation',
            'message' => 'Nous souhaitons un diagnostic structurel sur un immeuble à Akwa.',
        ])->assertRedirect()->assertSessionHas('success');

        $message = ContactMessage::sole();

        $this->assertFalse($message->is_read);
        $this->assertNull($message->replied_at);
        $this->assertNotNull($message->ip_address);
    }

    public function test_a_contact_message_needs_real_content(): void
    {
        $this->from('/contact')
            ->post('/contact', ['name' => 'X', 'email' => 'not-an-email', 'message' => 'court'])
            ->assertSessionHasErrors(['name', 'email', 'message']);
    }

    /* ---------------------------------------------------------------- */
    /* Newsletter                                                        */
    /* ---------------------------------------------------------------- */

    public function test_newsletter_signup_is_idempotent(): void
    {
        $this->post('/newsletter', ['email' => 'Client@Example.CM'])->assertRedirect();
        $this->post('/newsletter', ['email' => 'client@example.cm'])->assertRedirect();

        $this->assertSame(1, NewsletterSubscriber::count());
        $this->assertSame('client@example.cm', NewsletterSubscriber::sole()->email);
    }

    public function test_resubscribing_reactivates_a_cancelled_address(): void
    {
        NewsletterSubscriber::create([
            'email' => 'back@example.cm',
            'is_active' => false,
            'unsubscribed_at' => now()->subMonth(),
        ]);

        $this->post('/newsletter', ['email' => 'back@example.cm'])->assertRedirect();

        $subscriber = NewsletterSubscriber::sole();

        $this->assertTrue($subscriber->is_active);
        $this->assertNull($subscriber->unsubscribed_at);
    }

    /* ---------------------------------------------------------------- */
    /* Job applications                                                  */
    /* ---------------------------------------------------------------- */

    public function test_an_application_stores_the_cv_off_the_public_disk(): void
    {
        Storage::fake('local');

        $opening = JobOpening::first();

        $this->post("/carrieres/{$opening->slug}/candidature", [
            'name' => 'Rodrigue Manga',
            'email' => 'rodrigue@example.cm',
            'phone' => '+237 6 55 44 33 22',
            'years_experience' => 8,
            'cv' => UploadedFile::fake()->create('cv.pdf', 300, 'application/pdf'),
        ])->assertRedirect()->assertSessionHas('success');

        $application = JobApplication::sole();

        $this->assertSame($opening->id, $application->job_opening_id);
        $this->assertSame('received', $application->status);

        // CVs are personal data and must never sit under the web root.
        Storage::disk('local')->assertExists($application->cv_path);
        $this->assertStringStartsWith('applications/', $application->cv_path);
    }

    public function test_an_application_rejects_a_non_document_upload(): void
    {
        $opening = JobOpening::first();

        $this->from("/carrieres/{$opening->slug}")
            ->post("/carrieres/{$opening->slug}/candidature", [
                'name' => 'Test',
                'email' => 'test@example.cm',
                'phone' => '+237600000000',
                'cv' => UploadedFile::fake()->create('virus.exe', 10),
            ])
            ->assertSessionHasErrors('cv');

        $this->assertSame(0, JobApplication::count());
    }

    public function test_applications_are_refused_for_a_closed_opening(): void
    {
        $opening = JobOpening::first();
        $opening->update(['is_published' => false]);

        $this->post("/carrieres/{$opening->slug}/candidature", [
            'name' => 'Test',
            'email' => 'test@example.cm',
            'phone' => '+237600000000',
            'cv' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ])->assertNotFound();
    }
}
