<?php

namespace App\Models;

use App\Support\Media;
use App\Support\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use Translatable;

    protected $guarded = [];

    protected $casts = [
        'gallery' => 'array',
        'features_fr' => 'array',
        'features_en' => 'array',
        'situations_fr' => 'array',
        'situations_en' => 'array',
        'deliverables_fr' => 'array',
        'deliverables_en' => 'array',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function quoteRequests(): HasMany
    {
        return $this->hasMany(QuoteRequest::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('position');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function toCard(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->tr('title'),
            'tagline' => $this->tr('tagline'),
            'excerpt' => $this->tr('excerpt'),
            'icon' => $this->icon,
            'cover_image' => Media::url($this->cover_image),
            'features' => $this->tr('features') ?? [],
            'starting_price' => $this->starting_price,
            'is_featured' => $this->is_featured,
            'family' => $this->tr('family'),
            'cover_position' => $this->cover_position ?: 'center 50%',
            'need' => $this->quote_need,
        ];
    }

    public function toDetail(): array
    {
        return array_merge($this->toCard(), [
            'body' => $this->tr('body'),
            'gallery' => Media::urls($this->gallery),
            'situations' => $this->tr('situations') ?? [],
            'deliverables' => $this->tr('deliverables') ?? [],
            'limits' => $this->tr('limits'),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
        ]);
    }
}
