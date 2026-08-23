<?php

namespace App\Models;

use App\Support\Media;
use App\Support\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use Translatable;

    protected $guarded = [];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('position');
    }

    public function toArrayLocalised(): array
    {
        return [
            'id' => $this->id,
            'author_name' => $this->author_name,
            'author_role' => $this->tr('author_role'),
            'company' => $this->company,
            'photo' => Media::url($this->photo),
            'content' => $this->tr('content'),
            'rating' => $this->rating,
            'project' => $this->relationLoaded('project') && $this->project ? [
                'slug' => $this->project->slug,
                'title' => $this->project->tr('title'),
            ] : null,
        ];
    }
}
