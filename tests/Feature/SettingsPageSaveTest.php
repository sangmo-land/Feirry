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
}
