<?php

namespace App\Models;

use App\Support\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProcessStep extends Model
{
    use Translatable;

    protected $guarded = [];

    protected $casts = [
        'deliverables_fr' => 'array',
        'deliverables_en' => 'array',
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('position');
    }

    public function toArrayLocalised(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->tr('title'),
            'description' => $this->tr('description'),
            'icon' => $this->icon,
            'duration' => $this->tr('duration'),
            'deliverables' => $this->tr('deliverables') ?? [],
        ];
    }
}
