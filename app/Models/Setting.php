<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = [];

    public $timestamps = true;

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings.all'));
        static::deleted(fn () => Cache::forget('settings.all'));
    }

    public static function all_cached(): array
    {
        return Cache::rememberForever('settings.all', function () {
            // The site reads settings on every request, including before the
            // first migration has run on a fresh deploy. Falling back to an
            // empty set keeps config/site.php in charge instead of 500ing.
            try {
                return static::query()
                    ->get()
                    ->mapWithKeys(fn (self $s) => [$s->key => $s->castValue()])
                    ->all();
            } catch (QueryException) {
                return [];
            }
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::all_cached()[$key] ?? $default;
    }

    public static function put(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'group' => $group,
                'type' => $type,
                'value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value,
            ],
        );
    }

    public function castValue(): mixed
    {
        return match ($this->type) {
            'json', 'array' => json_decode((string) $this->value, true) ?? [],
            'bool', 'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOL),
            'int', 'integer' => (int) $this->value,
            default => $this->value,
        };
    }
}
