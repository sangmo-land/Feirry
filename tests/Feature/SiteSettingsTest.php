<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_config_values_are_used_when_nothing_is_stored(): void
    {
        $this->assertSame(config('site.brand.name'), SiteSettings::brandName());
    }

    public function test_a_stored_setting_overrides_the_config_default(): void
    {
        Setting::put('brand.name', 'BATIPRO', 'brand');

        $this->assertSame('BATIPRO', SiteSettings::brandName());
    }

    public function test_an_override_reaches_the_public_site(): void
    {
        Setting::put('contact.phone', '+237 6 00 00 00 00', 'contact');

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('site.contact.phone', '+237 6 00 00 00 00'));
    }

    public function test_saving_a_setting_busts_the_cache(): void
    {
        SiteSettings::brandName();

        Setting::put('brand.name', 'SOCOBAT', 'brand');

        $this->assertSame('SOCOBAT', SiteSettings::brandName());
    }

    public function test_the_frontend_payload_is_localised(): void
    {
        $fr = SiteSettings::forFrontend('fr');
        $en = SiteSettings::forFrontend('en');

        $this->assertNotSame($fr['stats'][0]['label'], $en['stats'][0]['label']);
        $this->assertNotSame($fr['contact']['hours'], $en['contact']['hours']);
    }

    public function test_the_slogan_is_never_translated(): void
    {
        // It belongs to the identity, not to the copy: the same words carry
        // across every locale, and a stored override does too.
        $this->assertSame(
            SiteSettings::forFrontend('fr')['brand']['tagline'],
            SiteSettings::forFrontend('en')['brand']['tagline'],
        );

        Setting::put('brand.tagline', 'Building what lasts', 'brand');

        foreach (['fr', 'en'] as $locale) {
            $this->assertSame('Building what lasts', SiteSettings::forFrontend($locale)['brand']['tagline']);
        }
    }

    public function test_settings_survive_a_missing_table(): void
    {
        // Simulates a deploy where code is live before migrations have run.
        \Illuminate\Support\Facades\Schema::drop('settings');
        \Illuminate\Support\Facades\Cache::forget('settings.all');

        $this->assertSame(config('site.brand.name'), SiteSettings::brandName());
    }
}
