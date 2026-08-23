<?php

namespace App\Models;

use App\Support\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOpening extends Model
{
    use Translatable;

    protected $guarded = [];

    protected $casts = [
        'requirements_fr' => 'array',
        'requirements_en' => 'array',
        'benefits_fr' => 'array',
        'benefits_en' => 'array',
        'closes_at' => 'date',
        'is_published' => 'boolean',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('closes_at')->orWhere('closes_at', '>=', today()))
            ->orderBy('position');
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
            'department' => $this->department,
            'location' => $this->location,
            'contract_type' => $this->contract_type,
            'experience_level' => $this->experience_level,
            'salary_range' => $this->salary_range,
            'closes_at' => $this->closes_at?->toDateString(),
        ];
    }

    public function toDetail(): array
    {
        return array_merge($this->toCard(), [
            'description' => $this->tr('description'),
            'requirements' => $this->tr('requirements') ?? [],
            'benefits' => $this->tr('benefits') ?? [],
        ]);
    }
}
