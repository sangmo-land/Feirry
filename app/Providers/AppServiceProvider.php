<?php

namespace App\Providers;

use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Only the reverse proxies listed in TRUSTED_PROXIES (e.g. Cloudflare, load balancer)
        // may set X-Forwarded-For / X-Forwarded-Proto. Empty = the server is reached directly.
        $proxies = config('site.trusted_proxies');
        if ($proxies) {
            TrustProxies::at($proxies === '*' ? '*' : array_map('trim', explode(',', $proxies)));
        }

        // Debug pages reveal code, paths and settings: never in production, even if APP_DEBUG=true was left on.
        if ($this->app->isProduction() && config('app.debug') && ! env('ALLOW_DEBUG_IN_PRODUCTION')) {
            config(['app.debug' => false]);
        }

        // Admin passwords (profile, team accounts): 12 characters with letters and digits.
        Password::defaults(fn () => Password::min(12)->letters()->numbers());

        // Every generated link in HTTPS in production (no mixed content, secure redirects).
        if (config('site.force_https')) {
            URL::forceScheme('https');
        }
    }
}
