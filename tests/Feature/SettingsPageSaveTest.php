<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettingsPage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsPageSaveTest extends TestCase
{
    use RefreshDatabase;

    /* The page must save without map coordinates: FEIRRY has not given any. */
    public function test_settings_save_without_coordinates(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(SiteSettingsPage::class)
            ->set('data.contact__email', 'contact@feirry.org')
            ->set('data.contact__latitude', null)
            ->set('data.contact__longitude', null)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('contact@feirry.org', Setting::get('contact.email'));
    }

    public function test_one_coordinate_alone_is_refused(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(SiteSettingsPage::class)
            ->set('data.contact__latitude', '4.05')
            ->set('data.contact__longitude', null)
            ->call('save')
            ->assertHasErrors('data.contact__longitude');
    }

    /* Additif Contact : la section « Où nous trouver ? » dépend du point GPS validé dans l'admin. */
    public function test_contact_page_gets_no_position_until_feirry_saves_one(): void
    {
        $this->get('/mon-projet')->assertOk()->assertInertia(fn ($page) => $page
            ->where('site.contact.latitude', null)
            ->where('site.contact.longitude', null));

        $this->assertStringNotContainsString('GeoCoordinates', $this->get('/mon-projet')->getContent());
    }

    public function test_a_saved_position_reaches_the_page_and_the_structured_data(): void
    {
        // Test values only: not FEIRRY's address.
        Setting::put('contact.latitude', '4.050000', 'contact');
        Setting::put('contact.longitude', '9.700000', 'contact');

        $html = $this->get('/mon-projet')->assertOk()->assertInertia(fn ($page) => $page
            ->where('site.contact.latitude', '4.050000')
            ->where('site.contact.longitude', '9.700000'))->getContent();

        $this->assertStringContainsString('"@type":"GeoCoordinates","latitude":4.05,"longitude":9.7', $html);
        $this->assertStringContainsString('frame-src https://www.openstreetmap.org', $this->get('/mon-projet')->headers->get('Content-Security-Policy'));
    }

    /* Horaires communiqués par la direction le 18/11/2026 : lundi–vendredi 8h–17h, samedi 9h–13h. */
    public function test_opening_hours_reach_the_page_in_both_languages_and_the_structured_data(): void
    {
        $html = $this->get('/mon-projet')->assertOk()->assertInertia(fn ($page) => $page
            ->where('site.contact.hours', 'Lundi – vendredi : 8h – 17h · Samedi : 9h – 13h'))->getContent();

        $this->get('/en/my-project')->assertOk()->assertInertia(fn ($page) => $page
            ->where('site.contact.hours', 'Monday – Friday: 8 am – 5 pm · Saturday: 9 am – 1 pm'));

        $this->assertStringContainsString('"opens":"08:00","closes":"17:00"', $html);
        $this->assertStringContainsString('"dayOfWeek":["Saturday"],"opens":"09:00","closes":"13:00"', $html);
        $this->assertStringNotContainsString('Sunday', $html);
    }

    public function test_hours_changed_in_the_admin_replace_the_default_sentence(): void
    {
        Setting::put('contact.hours_fr', 'Lundi – vendredi : 8h – 16h', 'contact');

        $this->get('/mon-projet')->assertOk()->assertInertia(fn ($page) => $page
            ->where('site.contact.hours', 'Lundi – vendredi : 8h – 16h'));
    }
}
