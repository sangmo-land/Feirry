<?php

namespace App\Models;

use App\Support\Media;
use App\Support\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use Translatable;

    public const STATUSES = ['completed', 'ongoing', 'upcoming'];

    protected $guarded = [];

    protected $casts = [
        'gallery' => 'array',
        'services_used' => 'array',
        'completed_at' => 'date',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'budget_public' => 'boolean',
        'families' => 'array',
        'roles' => 'array',
        'deliverables_fr' => 'array',
        'deliverables_en' => 'array',
        'visual_is_provisional' => 'boolean',
        'photo_rights_confirmed' => 'boolean',
    ];

    /** Filter families of the projects page (CDC §07). */
    public const FAMILIES = ['architecture', 'construction', 'design', 'suivi'];

    /** What FEIRRY actually did on the project. */
    public const ROLES = ['design', 'build', 'follow', 'expert'];

    public const STAGES = ['design', 'works', 'done', 'tbc'];

    public const VISIBILITIES = ['draft', 'published', 'archived'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        // Drafts and archived sheets are never public, nor indexed.
        return $query->where('is_published', true)->where('visibility', 'published');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function formattedBudget(): ?string
    {
        if (! $this->budget_public || ! $this->budget_xaf) {
            return null;
        }

        if ($this->budget_xaf >= 1_000_000_000) {
            return number_format($this->budget_xaf / 1_000_000_000, 1, ',', ' ').' Md FCFA';
        }

        return number_format($this->budget_xaf / 1_000_000, 0, ',', ' ').' M FCFA';
    }

    public function toCard(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->tr('title'),
            'excerpt' => $this->tr('excerpt'),
            'client_name' => $this->client_name,
            'location' => $this->location,
            'city' => $this->city,
            'cover_image' => Media::url($this->cover_image),
            'year' => $this->year,
            'status' => $this->status,
            'progress' => $this->progress,
            'surface_m2' => $this->surface_m2,
            'duration_months' => $this->duration_months,
            'budget' => $this->formattedBudget(),
            'is_featured' => $this->is_featured,
            'category' => $this->relationLoaded('category') && $this->category
                ? $this->category->toArrayLocalised()
                : null,
            'families' => $this->families ?? [],
            'roles' => $this->roles ?? [],
            'stage' => $this->stage ?: 'tbc',
            'stage_note' => $this->tr('stage_note'),
            'programme' => $this->tr('programme'),
            'mission' => $this->tr('mission'),
            'period' => $this->period,
            'cover_position' => $this->cover_position ?: 'center 50%',
            'provisional' => $this->visual_is_provisional,
        ];
    }

    public function toDetail(): array
    {
        return array_merge($this->toCard(), [
            'body' => $this->tr('body'),
            'challenge' => $this->tr('challenge'),
            'solution' => $this->tr('solution'),
            'outcome' => $this->tr('outcome'),
            'gallery' => Media::urls($this->gallery),
            'services_used' => $this->services_used ?? [],
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'completed_at' => $this->completed_at?->toDateString(),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'deliverables' => $this->tr('deliverables') ?? [],
            'photo_credits' => $this->photo_credits,
        ]);
    }
}
