<?php

namespace App\Models;

use App\Support\Media;
use App\Support\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use Translatable;

    protected $guarded = [];

    protected $casts = ['is_published' => 'boolean'];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('position');
    }

    public function toArrayLocalised(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'issuer' => $this->issuer,
            'logo' => Media::url($this->logo),
            'description' => $this->tr('description'),
            'year' => $this->year,
        ];
    }
}
