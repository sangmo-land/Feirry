<?php

namespace App\Models;

use App\Support\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A Coup de Pouce FEIRRY session. Requests are accepted only while a campaign
 * is marked open AND today falls inside its dates (CDC §08): a past campaign
 * can never display as open, whatever its status column says.
 */
class Campaign extends Model
{
    use Translatable;

    public const STATUSES = ['draft', 'open', 'closed'];

    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'show_capacity' => 'boolean',
        'consultation_enabled' => 'boolean',
        'visit_enabled' => 'boolean',
        'documents_required' => 'boolean',
    ];

    public function requests(): HasMany
    {
        return $this->hasMany(AssistanceRequest::class);
    }

    public function isOpen(): bool
    {
        if ($this->status !== 'open') {
            return false;
        }

        $today = now()->startOfDay();

        if ($this->starts_at && $today->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $today->gt($this->ends_at)) {
            return false;
        }

        if ($this->capacity && $this->requests()->count() >= $this->capacity) {
            return false;
        }

        return true;
    }

    /** The campaign currently accepting requests, if any. */
    public static function current(): ?self
    {
        return static::where('status', 'open')->latest('starts_at')->get()->first(fn (self $c) => $c->isOpen());
    }

    /** The campaign the public page should describe: the open one, else the next announced one. */
    public static function displayed(): ?self
    {
        return static::current()
            ?? static::where('status', 'open')->whereDate('starts_at', '>', now())->orderBy('starts_at')->first();
    }

    public function toPublic(string $locale): array
    {
        return [
            'open' => $this->isOpen(),
            'starts_at' => $this->starts_at?->toDateString(),
            'ends_at' => $this->ends_at?->toDateString(),
            'capacity' => $this->show_capacity ? $this->capacity : null,
            'territory' => $this->tr('territory', $locale),
            'terms' => $this->tr('terms', $locale),
            'consultation' => $this->consultation_enabled,
            'visit' => $this->visit_enabled,
            'documents_required' => $this->documents_required,
        ];
    }
}
