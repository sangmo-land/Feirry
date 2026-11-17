<?php

namespace App\Models;

use App\Support\Media;
use App\Support\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use Translatable;

    protected $guarded = [];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
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
            'excerpt' => $this->tr('excerpt'),
            'cover_image' => Media::url($this->cover_image),
            'tags' => $this->tags ?? [],
            'reading_minutes' => $this->reading_minutes,
            'views' => $this->views,
            'published_at' => $this->published_at?->toIso8601String(),
            'is_featured' => $this->is_featured,
            'category' => $this->relationLoaded('category') && $this->category
                ? $this->category->toArrayLocalised()
                : null,
            'author' => $this->relationLoaded('author') && $this->author
                ? ['name' => $this->author->name]
                : null,
        ];
    }

    public function toDetail(): array
    {
        return array_merge($this->toCard(), [
            'body' => \App\Support\SafeHtml::clean($this->tr('body')),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
        ]);
    }
}
