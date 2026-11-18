<?php

namespace Tests\Feature;

use App\Models\AssistanceRequest;
use App\Models\Campaign;
use App\Support\CdpSlots;
use App\Support\V7\ContentInstaller;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Coup de Pouce — automatic booking (CDC correctif CAMP-02, T09).
 * FEIRRY's rules of 18/11/2026: consultations on Monday 9:00–16:00 every
 * 30 minutes, visits on Wednesday and Thursday, 10 consultations and
 * 5 visits a week, a booked slot is final.
 */
class CoupDePouceBookingTest extends TestCase
{
    use RefreshDatabase;

    private Campaign $campaign;

    protected function setUp(): void
    {
        parent::setUp();
        // A fixed "today": Wednesday 18 November 2026, 13:00 in Douala.
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-11-18 13:00', 'Africa/Douala'));
        \Carbon\Carbon::setTestNow(CarbonImmutable::now());
        ContentInstaller::install();
        Storage::fake('local');
        Mail::fake();
        // The hourly limit per visitor is tested elsewhere; here many bookings are sent in a row.
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);
        // The pilot edition as installed (23/11 → 19/12), opened today.
        $this->campaign = Campaign::firstOrFail();
        $this->campaign->update(['status' => 'open', 'announced_at' => '2026-11-18']);
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        \Carbon\Carbon::setTestNow();
        parent::tearDown();
    }

    private function formA(array $over = []): array
    {
        return array_merge([
            'path' => 'consultation', 'idempotency_key' => 'book-a-'.uniqid(),
            'city' => 'Bonamoussadi, Douala', 'project_type' => 'maison', 'progress' => 'plans',
            'description' => 'Mes plans respectent-ils les règles ?',
            'name' => 'Client Test', 'phone' => '+237 670 000 000', 'privacy' => '1',
            'slot' => '2026-11-23 09:00',
        ], $over);
    }

    private function formB(array $over = []): array
    {
        return array_merge([
            'path' => 'visit', 'idempotency_key' => 'book-b-'.uniqid(),
            'city' => 'Logbessou, Douala', 'site_location' => 'Derrière la pharmacie', 'project_type' => 'maison', 'progress' => 'elevation',
            'description' => 'Fissures sur un mur porteur', 'name' => 'Client Test', 'phone' => '670000000', 'privacy' => '1',
            'slot' => '2026-11-25 08:30',
        ], $over);
    }

    public function test_the_grid_follows_feirry_rules(): void
    {
        $consultations = collect(CdpSlots::grid($this->campaign, 'consultation'));
        $visits = collect(CdpSlots::grid($this->campaign, 'visit'));

        // Four Mondays (23/11, 30/11, 7/12, 14/12) × 14 half-hours from 9:00 to 15:30.
        $this->assertCount(4 * 14, $consultations);
        $this->assertTrue($consultations->every(fn ($at) => $at->isoWeekday() === 1));
        $this->assertSame('09:00', $consultations->first()->format('H:i'));
        $this->assertSame('15:30', $consultations->where(fn ($at) => $at->isSameDay('2026-11-23'))->last()->format('H:i'));

        // Wednesdays and Thursdays only, 8:30 / 11:30 / 14:30.
        $this->assertCount(4 * 2 * 3, $visits);
        $this->assertTrue($visits->every(fn ($at) => in_array($at->isoWeekday(), [3, 4], true)));
    }

    public function test_a_consultation_is_booked_and_final(): void
    {
        $this->post('/coup-de-pouce/demandes', $this->formA(['meeting_mode' => 'video']))->assertRedirect('/coup-de-pouce/confirmation');

        $lead = AssistanceRequest::firstOrFail();
        $this->assertSame('scheduled', $lead->status);
        $this->assertSame('2026-11-23 09:00', $lead->scheduled_at->format('Y-m-d H:i'));
        $this->assertSame('video', $lead->scheduled_mode);

        $this->get('/coup-de-pouce/confirmation')->assertOk()->assertSee('2026-11-23T09:00', false);
        $this->assertFalse(CdpSlots::isBookable($this->campaign, 'consultation', '2026-11-23 09:00'));
        $this->assertTrue(CdpSlots::isBookable($this->campaign, 'consultation', '2026-11-23 09:30'));
    }

    public function test_a_visit_is_booked_on_site(): void
    {
        $this->post('/coup-de-pouce/demandes', $this->formB())->assertRedirect('/coup-de-pouce/confirmation');

        $lead = AssistanceRequest::firstOrFail();
        $this->assertSame('scheduled', $lead->status);
        $this->assertSame('site', $lead->scheduled_mode);
        $this->assertSame('2026-11-25 08:30', $lead->scheduled_at->format('Y-m-d H:i'));
    }

    /* T09 — two people, one last slot: only one gets it */
    public function test_the_same_slot_cannot_be_booked_twice(): void
    {
        $this->post('/coup-de-pouce/demandes', $this->formA())->assertRedirect('/coup-de-pouce/confirmation');
        $this->post('/coup-de-pouce/demandes', $this->formA())->assertSessionHasErrors('slot');

        $this->assertSame(1, AssistanceRequest::count());
    }

    /* The check before writing can be passed by two requests at once: the database still refuses the second. */
    public function test_the_database_refuses_a_double_booking_by_itself(): void
    {
        $this->post('/coup-de-pouce/demandes', $this->formA())->assertRedirect('/coup-de-pouce/confirmation');
        $first = AssistanceRequest::firstOrFail();

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);
        AssistanceRequest::create(array_merge($first->only(['campaign_id', 'path', 'name', 'phone', 'city', 'project_type', 'progress', 'description', 'slot_key']), ['idempotency_key' => 'race-0000000000000001', 'status' => 'scheduled']));
    }

    public function test_the_weekly_quota_closes_the_week(): void
    {
        // Ten consultations fill the week of 23 November; four half-hours stay unused.
        foreach (['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30'] as $time) {
            $this->post('/coup-de-pouce/demandes', $this->formA(['slot' => "2026-11-23 {$time}"]))->assertRedirect('/coup-de-pouce/confirmation');
        }

        $this->post('/coup-de-pouce/demandes', $this->formA(['slot' => '2026-11-23 14:00']))->assertSessionHasErrors('slot');
        $this->assertSame(10, AssistanceRequest::count());

        // The following week is untouched.
        $this->post('/coup-de-pouce/demandes', $this->formA(['slot' => '2026-11-30 09:00']))->assertRedirect('/coup-de-pouce/confirmation');

        // Five visits fill the visit week.
        foreach (['2026-11-25 08:30', '2026-11-25 11:30', '2026-11-25 14:30', '2026-11-26 08:30', '2026-11-26 11:30'] as $slot) {
            $this->post('/coup-de-pouce/demandes', $this->formB(['slot' => $slot]))->assertRedirect('/coup-de-pouce/confirmation');
        }
        $this->post('/coup-de-pouce/demandes', $this->formB(['slot' => '2026-11-26 14:30']))->assertSessionHasErrors('slot');
    }

    public function test_a_slot_is_required_and_must_belong_to_the_grid(): void
    {
        $this->post('/coup-de-pouce/demandes', $this->formA(['slot' => null]))->assertSessionHasErrors('slot');
        // A Tuesday, an hour outside the grid, a past date, a date after the edition.
        foreach (['2026-11-24 09:00', '2026-11-23 16:00', '2026-11-16 09:00', '2026-12-21 09:00', 'demain'] as $slot) {
            $this->post('/coup-de-pouce/demandes', $this->formA(['slot' => $slot]))->assertSessionHasErrors('slot');
        }
        $this->assertSame(0, AssistanceRequest::count());
    }

    public function test_a_slot_too_close_cannot_be_booked(): void
    {
        // Sunday 22 November, 10:00: Monday 9:00 is less than 24 hours away, Monday 10:00 is not.
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-11-22 10:00', 'Africa/Douala'));
        \Carbon\Carbon::setTestNow(CarbonImmutable::now());

        $this->post('/coup-de-pouce/demandes', $this->formA(['slot' => '2026-11-23 09:00']))->assertSessionHasErrors('slot');
        $this->post('/coup-de-pouce/demandes', $this->formA(['slot' => '2026-11-23 10:00']))->assertRedirect('/coup-de-pouce/confirmation');
    }

    public function test_a_resent_form_keeps_one_booking(): void
    {
        $data = $this->formA();
        $this->post('/coup-de-pouce/demandes', $data)->assertRedirect('/coup-de-pouce/confirmation');
        $this->post('/coup-de-pouce/demandes', $data)->assertRedirect('/coup-de-pouce/confirmation');

        $this->assertSame(1, AssistanceRequest::count());
    }

    public function test_a_request_set_aside_frees_its_slot(): void
    {
        $this->post('/coup-de-pouce/demandes', $this->formA());
        AssistanceRequest::firstOrFail()->update(['status' => 'out_of_scope']);

        $this->assertTrue(CdpSlots::isBookable($this->campaign, 'consultation', '2026-11-23 09:00'));
        $this->post('/coup-de-pouce/demandes', $this->formA())->assertRedirect('/coup-de-pouce/confirmation');
    }

    public function test_when_everything_is_booked_requests_go_to_the_waiting_list(): void
    {
        // One visit a week, on a one-week edition: the single seat goes, the rest waits.
        $this->campaign->update(['public_visits' => 1, 'ends_at' => '2026-11-27']);
        $this->post('/coup-de-pouce/demandes', $this->formB())->assertRedirect('/coup-de-pouce/confirmation');
        $this->post('/coup-de-pouce/demandes', $this->formB(['slot' => null]))->assertRedirect('/coup-de-pouce/confirmation');

        $this->assertSame(['scheduled', 'waiting'], AssistanceRequest::orderBy('id')->pluck('status')->all());
    }

    public function test_the_form_never_reveals_how_many_requests_exist(): void
    {
        $this->post('/coup-de-pouce/demandes', $this->formA());
        $page = $this->get('/coup-de-pouce/consultation')->assertOk()->getContent();

        $this->assertStringContainsString('2026-11-23 09:30', $page);
        $this->assertStringNotContainsString('seat_key', $page);
        $this->assertStringNotContainsString('internal_consultations', $page);
    }
}
