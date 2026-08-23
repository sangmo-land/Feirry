<?php

namespace App\Models;

use App\Support\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostCategory extends Model
{
    use Translatable;

    protected $guarded = [];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
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
            'color' => $this->color,
            'posts_count' => $this->posts_count ?? null,
        ];
    }
}
