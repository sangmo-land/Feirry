<?php

namespace App\Models;

use App\Support\Media;
use App\Support\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use Translatable;

    protected $guarded = [];

    protected $casts = [
        'expertise' => 'array',
        'is_leadership' => 'boolean',
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
            'slug' => $this->slug,
            'name' => $this->name,
            'role' => $this->tr('role'),
            'bio' => $this->tr('bio'),
            'photo' => Media::url($this->photo),
            'email' => $this->email,
            'phone' => $this->phone,
            'linkedin' => $this->linkedin,
            'expertise' => $this->expertise ?? [],
            'years_experience' => $this->years_experience,
            'is_leadership' => $this->is_leadership,
        ];
    }
}
