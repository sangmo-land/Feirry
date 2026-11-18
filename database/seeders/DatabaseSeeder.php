<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Support\V7\ContentInstaller;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // The password comes from the environment, never from the repository.
        // Without ADMIN_PASSWORD a random one is generated and printed once.
        $password = env('ADMIN_PASSWORD') ?: Str::password(20);

        $admin = User::firstOrNew(['email' => env('ADMIN_EMAIL', 'admin@feirry.org')]);

        if (! $admin->exists) {
            $admin->fill([
                'name' => 'Administrateur FEIRRY',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ])->save();

            if (! env('ADMIN_PASSWORD')) {
                $this->command?->warn("Admin password (shown once): {$password}");
            }
        }

        $this->call([
            ServiceSeeder::class,
            ProjectSeeder::class,
            TeamSeeder::class,
            ContentSeeder::class,
            CareerSeeder::class,
            PageHeroSeeder::class,
        ]);

        // V7: real content in, demonstration content unpublished.
        ContentInstaller::install();

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
