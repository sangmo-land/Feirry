<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Resolves a stored image reference to something a browser can load.
 *
 * Two kinds of value live in these columns. The seeded content holds absolute
 * URLs to stock photography; anything the client uploads through the admin is
 * a path on the `public` disk — `projects/covers/xyz.jpg` — which is served from
 * public/storage. Handing that raw path to an <img> resolves it against the
 * current page instead, so an uploaded picture silently 404s.
 */
class Media
{
    public static function url(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        // Already absolute, or already rooted at the document root.
        if (str_starts_with($value, 'http://')
            || str_starts_with($value, 'https://')
            || str_starts_with($value, '//')
            || str_starts_with($value, '/')) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }

    /** The same, for a stored list of images. */
    public static function urls(?array $values): array
    {
        return collect($values ?? [])
            ->map(fn ($value) => static::url((string) $value))
            ->filter()
            ->values()
            ->all();
    }
}
