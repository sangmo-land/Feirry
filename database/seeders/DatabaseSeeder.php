<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@feirry.org'],
            [
                'name' => 'Administrateur FEIRRY',
                'password' => Hash::make('feirry2026'),
                'email_verified_at' => now(),
            ],
        );

        $this->call([
            ServiceSeeder::class,
            ProjectSeeder::class,
            TeamSeeder::class,
            ContentSeeder::class,
            CareerSeeder::class,
            PageHeroSeeder::class,
        ]);

        // Mirror the config defaults into the editable settings table so the
        // client can change them from the admin panel without a deploy.
        foreach (['brand.name', 'brand.legal_name', 'brand.tagline'] as $key) {
            Setting::put($key, config("site.{$key}"), 'brand');
        }

        foreach (['contact.address', 'contact.city', 'contact.phone', 'contact.email', 'contact.whatsapp', 'contact.hours_fr', 'contact.hours_en', 'contact.maps_url', 'contact.latitude', 'contact.longitude'] as $key) {
            Setting::put($key, config("site.{$key}"), 'contact');
        }
    }
}
