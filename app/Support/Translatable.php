<?php

namespace App\Support;

/**
 * Column-per-locale translation. Every translatable model stores `field_fr`
 * and `field_en`; `tr()` resolves the active locale and falls back to French,
 * which is the working language of the Douala office.
 */
trait Translatable
{
    public function tr(string $field, ?string $locale = null): mixed
    {
        $locale = $locale ?: app()->getLocale();

        $value = $this->getAttribute("{$field}_{$locale}");

        if (blank($value)) {
            $value = $this->getAttribute("{$field}_".config('app.fallback_locale', 'fr'));
        }

        return $value;
    }

    /** Localise a list of fields into a plain array for Inertia. */
    public function trMany(array $fields, ?string $locale = null): array
    {
        return collect($fields)
            ->mapWithKeys(fn (string $field) => [$field => $this->tr($field, $locale)])
            ->all();
    }
}
