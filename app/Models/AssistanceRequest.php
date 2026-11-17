<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * A Coup de Pouce request: fiche A (consultation de dossier) or fiche B
 * (visite de chantier). Received ≠ selected ≠ scheduled ≠ done (CDC §08):
 * every count and message relies on these real states.
 */
class AssistanceRequest extends Model
{
    public const STATUSES = [
        'received' => 'Reçue',
        'incomplete' => 'Dossier incomplet',
        'studying' => 'En étude',
        'waiting' => 'En attente',
        'scheduled' => 'Programmée',
        'done' => 'Réalisée',
        'report_sent' => 'Rapport transmis',
        'closed' => 'Clôturée',
        'out_of_scope' => 'Hors périmètre',
    ];

    /** Check-in target: verified within 48 working hours (CDC §21). */
    public const VERIFY_HOURS = 48;

    /** Report due 5 working days after the intervention. */
    public const REPORT_WORKING_DAYS = 5;

    protected $guarded = [];

    protected $casts = [
        'attachments' => 'array',
        'reports' => 'array',
        'danger_reported' => 'boolean',
        'out_of_zone' => 'boolean',
        'marketing_consent' => 'boolean',
        'privacy_accepted_at' => 'datetime',
        'marketing_consent_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'report_due_at' => 'date',
        'report_sent_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $request) {
            $request->reference ??= static::newReference();
        });

        static::updating(function (self $request) {
            if ($request->isDirty('status')) {
                $request->log('status', static::STATUSES[$request->getOriginal('status')] ?? $request->getOriginal('status'), static::STATUSES[$request->status] ?? $request->status);

                if ($request->status === 'done' && ! $request->report_due_at) {
                    $request->report_due_at = now()->addWeekdays(static::REPORT_WORKING_DAYS);
                }

                if ($request->status === 'report_sent' && ! $request->report_sent_at) {
                    $request->report_sent_at = now();
                }
            }

            foreach (['assigned_to' => 'Affectation modifiée', 'scheduled_at' => 'Rendez-vous modifié', 'decision_reason' => 'Motif de décision saisi'] as $field => $label) {
                if ($request->isDirty($field)) {
                    $request->log('update', $label);
                }
            }
        });

        static::created(fn (self $r) => $r->log('created', $r->source === 'office' ? 'Enregistrée au bureau' : 'Reçue par le site'));
    }

    /**
     * CDP-YY-XXXXXX, drawn at random from an alphabet without look-alike
     * characters. Non-sequential: it reveals neither volume nor order.
     */
    public static function newReference(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $ref = 'CDP-'.now()->format('y').'-'.$code;
        } while (static::where('reference', $ref)->exists());

        return $ref;
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AssistanceAuditLog::class)->latest('id');
    }

    public function log(string $action, ?string $from = null, ?string $to = null): void
    {
        if (! $this->exists) {
            return;
        }

        AssistanceAuditLog::create([
            'assistance_request_id' => $this->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'detail' => $to !== null ? "{$from} → {$to}" : $from,
        ]);
    }

    /** Private folder: /coup-de-pouce/editions/{edition}/demandes/{reference}/{kind}. */
    public function storageFolder(string $kind = 'originaux'): string
    {
        $edition = $this->campaign?->edition ?: 'hors-edition';

        return "coup-de-pouce/editions/{$edition}/demandes/{$this->reference}/{$kind}";
    }

    public function needsVerification(): bool
    {
        return $this->status === 'received' && $this->created_at?->lt(now()->subHours(static::VERIFY_HOURS));
    }

    public function reportOverdue(): bool
    {
        return $this->status === 'done' && $this->report_due_at && now()->startOfDay()->gt($this->report_due_at);
    }

    public function isVisit(): bool
    {
        return $this->path === 'visit';
    }
}
