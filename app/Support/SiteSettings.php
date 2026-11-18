<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Reads the `settings` table with config/site.php as the fallback, so the
 * site works out of the box and the client can still edit everything from
 * the admin panel.
 */
class SiteSettings
{
    public static function get(string $key, mixed $default = null): mixed
    {
        $stored = Setting::get($key);

        if (filled($stored)) {
            return $stored;
        }

        return $default ?? config("site.{$key}");
    }

    public static function brandName(): string
    {
        return (string) static::get('brand.name', config('site.brand.name'));
    }

    /**
     * The networks the company is on, in the order they are shown.
     *
     * Read one key at a time rather than through get(), because an empty value
     * has to mean something different here than it does everywhere else on the
     * settings page. Clearing the phone number restores the one that shipped —
     * a site with no phone number is a mistake. Clearing a social link is not a
     * mistake: it means the company is not on that network, and the icon has to
     * disappear rather than spring back. A key that was never saved at all is
     * still filled from config/site.php.
     */
    public static function socialLinks(): array
    {
        return collect(array_keys(config('site.social')))
            ->mapWithKeys(function (string $network) {
                $stored = Setting::get("social.{$network}");

                return [$network => $stored === null ? config("site.social.{$network}") : $stored];
            })
            ->filter(fn ($url) => filled($url))
            ->all();
    }

    /** Everything the React layout needs, in one localised payload. */
    public static function forFrontend(string $locale): array
    {
        $contact = config('site.contact');

        return [
            'brand' => [
                'name' => static::brandName(),
                'legal_name' => static::get('brand.legal_name', config('site.brand.legal_name')),
                'tagline' => static::get('brand.tagline', config('site.brand.tagline')),
                'founded' => (int) static::get('brand.founded', config('site.brand.founded')),
            ],
            'contact' => [
                'address' => static::get('contact.address', $contact['address']),
                'district' => $contact['district'],
                'city' => static::get('contact.city', $contact['city']),
                'region' => $contact['region'],
                'country' => $contact['country'],
                'postal_box' => $contact['postal_box'],
                'phone' => static::get('contact.phone', $contact['phone']),
                'phone_label' => $contact['phone_label'] ?? null,
                'phone_secondary' => $contact['phone_secondary'],
                'phone_secondary_label' => $contact['phone_secondary_label'] ?? null,
                'secondary_presence' => $contact['secondary_presence'] ?? null,
                'whatsapp' => static::get('contact.whatsapp', $contact['whatsapp']),
                'email' => static::get('contact.email', $contact['email']),
                'email_rh' => $contact['email_rh'],
                'email_devis' => $contact['email_devis'],
                'hours' => static::get("contact.hours_{$locale}", $contact["hours_{$locale}"]),
                'maps_url' => static::get('contact.maps_url', $contact['maps_url']),
                'latitude' => static::get('contact.latitude', $contact['latitude']),
                'longitude' => static::get('contact.longitude', $contact['longitude']),
            ],
            'legal' => array_filter(config('site.legal'), fn ($v) => filled($v)),
            'social' => static::socialLinks(),
            'stats' => collect(config('site.stats'))->map(fn (array $s) => [
                'value' => $s['value'],
                'suffix' => $s['suffix'],
                'label' => $s["label_{$locale}"],
            ])->all(),
        ];
    }
}
