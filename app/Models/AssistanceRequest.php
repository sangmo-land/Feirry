<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/** A Coup de Pouce request: free file consultation or technical site visit. */
class AssistanceRequest extends Model
{
    public const STATUSES = ['new', 'eligible', 'scheduled', 'done', 'declined'];

    protected $guarded = [];

    protected $casts = [
        'attachments' => 'array',
        'privacy_accepted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $request) {
            $request->reference ??= 'CDP-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        });
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
