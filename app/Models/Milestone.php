<?php

namespace App\Models;

use App\Support\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use Translatable;

    protected $guarded = [];

    protected $casts = ['is_published' => 'boolean'];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('year');
    }

    public function toArrayLocalised(): array
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'title' => $this->tr('title'),
            'description' => $this->tr('description'),
            'image' => $this->image,
        ];
    }
}
