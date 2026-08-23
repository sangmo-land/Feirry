<?php

namespace App\Models;

use App\Support\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use Translatable;

    protected $table = 'faqs';

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
            'group' => $this->group,
            'question' => $this->tr('question'),
            'answer' => $this->tr('answer'),
        ];
    }
}
