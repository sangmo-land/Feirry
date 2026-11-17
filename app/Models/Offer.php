<?php

namespace App\Models;

use App\Support\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use Translatable;

    protected $guarded = [];

    protected $casts = [
        'included_fr' => 'array', 'included_en' => 'array',
        'excluded_fr' => 'array', 'excluded_en' => 'array',
        'steps_fr' => 'array', 'steps_en' => 'array',
        'price_factors_fr' => 'array', 'price_factors_en' => 'array',
        'is_recommended' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('position');
    }

    public function toCard(): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->tr('title'),
            'subtitle' => $this->tr('subtitle'),
            'audience' => $this->tr('audience'),
            'included' => $this->tr('included') ?? [],
            'excluded' => $this->tr('excluded') ?? [],
            'steps' => $this->tr('steps') ?? [],
            'price_factors' => $this->tr('price_factors') ?? [],
            'price_label' => $this->tr('price_label'),
            'cta' => $this->tr('cta'),
            'need' => $this->quote_need,
            'recommended' => $this->is_recommended,
        ];
    }
}
