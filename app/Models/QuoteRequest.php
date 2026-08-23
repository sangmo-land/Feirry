<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QuoteRequest extends Model
{
    public const STATUSES = ['new', 'qualified', 'site_visit', 'proposal_sent', 'won', 'lost'];

    protected $guarded = [];

    protected $casts = ['contacted_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (self $quote) {
            $quote->reference ??= 'DEV-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        });
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
