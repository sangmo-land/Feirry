<?php

namespace App\Models;

use App\Support\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * An edition of the Coup de Pouce FEIRRY programme (CDC Coup de Pouce V2).
 *
 * The section itself is permanent; only the edition banner, its dates and the
 * request forms are switched on and off here. Status semantics (§05):
 *   draft  — préparation: page visible, requests closed;
 *   open   — requests accepted inside the dates;
 *   paused — en pause / complet: closed, unless the waiting list is open;
 *   closed — édition terminée: dated banner hidden.
 * Nothing in here is a quota enforced on visitors: selection is human (§08).
 */
class Campaign extends Model
{
    use Translatable;

    public const STATUSES = ['draft', 'open', 'paused', 'closed'];

    protected $guarded = [];

    protected $casts = [
        'announced_at' => 'date',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'waitlist' => 'boolean',
        'consultation_enabled' => 'boolean',
        'visit_enabled' => 'boolean',
    ];

    public function requests(): HasMany
    {
        return $this->hasMany(AssistanceRequest::class);
    }

    /** Within the edition's dates (an undated edition is never "current"). */
    public function inWindow(): bool
    {
        $today = now()->startOfDay();

        if (! $this->announced_at && ! $this->starts_at) {
            return false;
        }

        $from = $this->announced_at ?? $this->starts_at;

        return $today->gte($from) && (! $this->ends_at || $today->lte($this->ends_at));
    }

    /** Requests can be submitted right now. */
    public function accepting(): bool
    {
        return $this->inWindow() && ($this->status === 'open' || ($this->status === 'paused' && $this->waitlist));
    }

    /** The public state shown on the page. */
    public function publicState(): string
    {
        if ($this->status === 'closed' || ($this->ends_at && now()->startOfDay()->gt($this->ends_at))) {
            return 'closed';
        }

        if ($this->status === 'paused') {
            return $this->waitlist && $this->inWindow() ? 'waitlist' : 'paused';
        }

        if ($this->status === 'open' && $this->inWindow()) {
            return 'open';
        }

        // Prepared or not yet started: "upcoming" only when a date is confirmed.
        $from = $this->announced_at ?? $this->starts_at;

        return $this->status === 'open' && $from && now()->startOfDay()->lt($from) ? 'upcoming' : 'preparation';
    }

    /** The edition the public page describes: the latest one not archived. */
    public static function displayed(): ?self
    {
        return static::query()
            ->orderByRaw("CASE status WHEN 'open' THEN 0 WHEN 'paused' THEN 1 WHEN 'draft' THEN 2 ELSE 3 END")
            ->latest('id')
            ->first();
    }

    /** The edition currently accepting requests, if any. */
    public static function current(): ?self
    {
        $c = static::displayed();

        return $c && $c->accepting() ? $c : null;
    }

    /** Only the public parameters (CDC §12: GET config returns public values only). */
    public function toPublic(string $locale): array
    {
        $state = $this->publicState();
        $dated = in_array($state, ['open', 'waitlist', 'upcoming', 'paused'], true);

        return [
            'state' => $state,
            'open' => $this->accepting(),
            'edition' => $dated ? $this->tr('banner', $locale) : null,
            'announced_at' => $dated ? $this->announced_at?->toDateString() : null,
            'starts_at' => $dated ? $this->starts_at?->toDateString() : null,
            'ends_at' => $dated ? $this->ends_at?->toDateString() : null,
            'public_consultations' => $this->public_consultations,
            'public_visits' => $this->public_visits,
            'zone' => $this->tr('zone', $locale),
            'terms' => $this->tr('terms', $locale),
            'confirmation' => $this->tr('confirmation', $locale),
            'support_whatsapp' => $this->support_whatsapp,
            'consultation' => $this->consultation_enabled,
            'visit' => $this->visit_enabled,
        ];
    }

    /** Is a visit location inside the pilot zone? Unknown zone → assume inside. */
    public function inZone(?string $place): bool
    {
        $keywords = array_filter(array_map('trim', explode(',', mb_strtolower((string) $this->visit_zone_keywords))));

        if (! $keywords || blank($place)) {
            return true;
        }

        $place = mb_strtolower($place);

        foreach ($keywords as $k) {
            if (str_contains($place, $k)) {
                return true;
            }
        }

        return false;
    }
}
