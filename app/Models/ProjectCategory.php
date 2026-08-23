<?php

namespace App\Models;

use App\Support\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectCategory extends Model
{
    use Translatable;

    protected $guarded = [];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function toArrayLocalised(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->tr('name'),
            'description' => $this->tr('description'),
            'icon' => $this->icon,
            'projects_count' => $this->projects_count ?? null,
        ];
    }
}
