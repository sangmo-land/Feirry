<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssistanceRequest;
use App\Mail\AssistanceReceivedMail;
use App\Models\AssistanceRequest;
use App\Models\Campaign;
use App\Support\CdpSlots;
use App\Support\Seo;
use App\Support\V7\LeadNotifier;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Le Coup de Pouce FEIRRY (CDC Coup de Pouce V2).
 * Public: presentation, two forms, a confirmation shown only after the
 * server has really stored the request. Everything else is in the admin.
 */
class CoupDePouceController extends Controller
{
    public function index(): Response
    {
        Seo::set([
            'title' => __('v7.seo.cdp.title'),
            'description' => __('v7.seo.cdp.description'),
        ]);

        return Inertia::render('V7/CoupDePouce', ['campaign' => static::publicCampaign()]);
    }

    public function form(string $path): Response|RedirectResponse
    {
        $campaign = static::publicCampaign();

        Seo::set([
            'title' => __("v7.seo.cdp_{$path}.title"),
            'description' => __("v7.seo.cdp_{$path}.description"),
        ]);

        return Inertia::render('V7/CoupDePouceForm', [
            'path' => $path,
            'campaign' => $campaign,
            // Days and times that can be booked; free or taken, never a count.
            'slots' => ($live = Campaign::current()) && $live->publicState() === 'open' ? CdpSlots::publicList($live, $path) : [],
            'limits' => [
                'pdf_max_files' => config('site.cdp.pdf_max_files'),
                'pdf_max_mb' => (int) round(config('site.cdp.pdf_max_kb') / 1024),
                'photo_max_files' => config('site.cdp.photo_max_files'),
                'photo_max_mb' => (int) round(config('site.cdp.photo_max_kb') / 1024),
            ],
        ]);
    }

    public function consultation(): Response|RedirectResponse
    {
        return $this->form('consultation');
    }

    public function visit(): Response|RedirectResponse
    {
        return $this->form('visit');
    }

    public function store(StoreAssistanceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // A resent submission (double click, network retry) returns the
        // request already stored, never a second one (CDC §22, DUP-01).
        $existing = AssistanceRequest::where('idempotency_key', $data['idempotency_key'])->first();

        if ($existing) {
            return $this->confirm($existing);
        }

        $campaign = Campaign::current();
        $enabled = $campaign && ($data['path'] === 'visit' ? $campaign->visit_enabled : $campaign->consultation_enabled);

        if (! $enabled) {
            return back()->with('error', 'campaign_closed');
        }

        $visit = $data['path'] === 'visit';

        // Automatic booking (CAMP-02): while a slot is free, one must be chosen
        // and it is final. When every slot is taken the request goes to the
        // waiting list instead.
        $slot = $data['slot'] ?? null;
        // No grid (slots not configured, or all in the past): requests are handled by hand, as before.
        $hasGrid = $campaign->publicState() === 'open' && (bool) CdpSlots::grid($campaign, $data['path']);
        $bookable = $hasGrid && CdpSlots::anyFree($campaign, $data['path']);
        if ($bookable && (! $slot || ! CdpSlots::isBookable($campaign, $data['path'], $slot))) {
            throw ValidationException::withMessages(['slot' => __($slot ? 'v7.errors.slot_taken' : 'v7.errors.slot_required')]);
        }
        $at = $bookable ? CdpSlots::parse($slot) : null;

        $attributes = [
            'campaign_id' => $campaign->id,
            'idempotency_key' => $data['idempotency_key'],
            'path' => $data['path'],
            'source' => 'web',
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'city' => $data['city'],
            'project_type' => $data['project_type'],
            'progress' => $data['progress'],
            'land_area' => $visit ? null : ($data['land_area'] ?? null),
            'footprint_area' => $visit ? null : ($data['footprint_area'] ?? null),
            'levels' => $visit ? null : ($data['levels'] ?? null),
            'description' => $data['description'],
            'meeting_mode' => $visit ? null : ($data['meeting_mode'] ?? null),
            'site_location' => $visit ? $data['site_location'] : null,
            'gps' => $visit ? ($data['gps'] ?? null) : null,
            'availability' => $visit ? ($data['availability'] ?? null) : null,
            'access_constraints' => $visit ? ($data['access_constraints'] ?? null) : null,
            'danger_reported' => $visit && ! empty($data['danger']),
            'out_of_zone' => $visit && ! $campaign->inZone($data['city'].' '.$data['site_location']),
            'locale' => app()->getLocale(),
            'privacy_accepted_at' => now(),
            'notice_version' => config('site.cdp.notice_version'),
            'marketing_consent' => ! empty($data['marketing']),
            'marketing_consent_at' => ! empty($data['marketing']) ? now() : null,
            // Booked → scheduled. Every slot taken, or edition on its waiting list → waiting list.
            'status' => $at ? 'scheduled' : ($hasGrid || $campaign->publicState() === 'waitlist' ? 'waiting' : 'received'),
        ];

        if ($at) {
            $attributes += [
                'scheduled_at' => $at,
                'scheduled_mode' => $visit ? 'site' : (($data['meeting_mode'] ?? null) ?: 'office'),
                'slot_key' => CdpSlots::slotKey($campaign, $data['path'], $slot),
            ];
        }

        $lead = $this->book($attributes, $campaign, $at);

        $lead->update(['attachments' => $this->storeFiles($request->file('attachments', []), $lead)]);

        LeadNotifier::send($visit ? 'Coup de Pouce — visite' : 'Coup de Pouce — consultation', $lead->reference, url("/admin/assistance-requests/{$lead->id}/edit"));

        if ($lead->email) {
            try {
                Mail::to($lead->email)->send(new AssistanceReceivedMail($lead));
            } catch (\Throwable $e) {
                Log::warning("Acknowledgement mail failed for {$lead->reference}");
            }
        }

        return $this->confirm($lead);
    }

    public function confirmation(): Response|RedirectResponse
    {
        $done = session('cdp_done');

        // Reachable only right after a real, stored submission (CDC §02).
        if (! $done) {
            return redirect(\App\Support\LocalePath::to('/coup-de-pouce', app()->getLocale()));
        }

        Seo::set(['title' => __('v7.seo.cdp_confirmation.title'), 'noindex' => true]);

        return Inertia::render('V7/CoupDePouceConfirmation', [
            'reference' => $done['reference'],
            'path' => $done['path'],
            'message' => static::publicCampaign()['confirmation'] ?? null,
            'waiting' => (bool) ($done['waiting'] ?? false),
            'slot' => $done['slot'] ?? null,
            'mode' => $done['mode'] ?? null,
        ]);
    }

    /* ----------------------------------------------------------------- */

    /**
     * Writes the request. With a slot, takes the first free numbered seat of
     * that week: the unique indexes on slot_key and seat_key refuse a second
     * request for the same slot, or one beyond the weekly quota, even when two
     * people send at the same instant.
     */
    protected function book(array $attributes, Campaign $campaign, ?\Carbon\CarbonImmutable $at): AssistanceRequest
    {
        if (! isset($attributes['slot_key'])) {
            return AssistanceRequest::create($attributes);
        }

        for ($seat = 1, $quota = CdpSlots::quota($campaign, $attributes['path']); $seat <= $quota; $seat++) {
            try {
                return AssistanceRequest::create($attributes + ['seat_key' => CdpSlots::seatKey($campaign, $attributes['path'], $at, $seat)]);
            } catch (UniqueConstraintViolationException $e) {
                // A resend of the same form that crossed this one: hand back the stored request.
                if ($same = AssistanceRequest::where('idempotency_key', $attributes['idempotency_key'])->first()) {
                    return $same;
                }
                if (AssistanceRequest::where('slot_key', $attributes['slot_key'])->exists()) {
                    throw ValidationException::withMessages(['slot' => __('v7.errors.slot_taken')]);
                }
                // Otherwise that seat was just taken: try the next one.
            }
        }

        throw ValidationException::withMessages(['slot' => __('v7.errors.slot_week_full')]);
    }

    protected function confirm(AssistanceRequest $lead): RedirectResponse
    {
        session()->flash('cdp_done', [
            'reference' => $lead->reference,
            'path' => $lead->path,
            'waiting' => $lead->status === 'waiting',
            'slot' => $lead->slot_key ? $lead->scheduled_at?->format('Y-m-d\TH:i') : null,
            'mode' => $lead->slot_key ? $lead->scheduled_mode : null,
        ]);

        return redirect(\App\Support\LocalePath::to('/coup-de-pouce/confirmation', app()->getLocale()));
    }

    /** @param  UploadedFile[]  $files */
    protected function storeFiles(array $files, AssistanceRequest $lead): array
    {
        return collect($files)
            ->filter(fn ($f) => $f instanceof UploadedFile && $f->isValid())
            ->map(function (UploadedFile $file) use ($lead) {
                $mime = $file->getMimeType();

                return [
                    // Random server-side name: the client's file name never builds a path.
                    'path' => $file->store($lead->storageFolder('originaux'), 'local'),
                    'name' => mb_substr(preg_replace('/[^\pL\pN ._()-]/u', '_', $file->getClientOriginalName()), 0, 120),
                    'mime' => $mime,
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toIso8601String(),
                ];
            })
            ->values()
            ->all();
    }

    public static function publicCampaign(): array
    {
        $campaign = Campaign::displayed();

        return $campaign
            ? $campaign->toPublic(app()->getLocale())
            : ['state' => 'preparation', 'open' => false, 'edition' => null, 'announced_at' => null, 'starts_at' => null, 'ends_at' => null, 'public_consultations' => null, 'public_visits' => null, 'zone' => null, 'terms' => null, 'confirmation' => null, 'support_whatsapp' => null, 'consultation' => true, 'visit' => true];
    }
}
