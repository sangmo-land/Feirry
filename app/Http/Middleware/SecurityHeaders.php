<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security headers set by the application itself, so they hold on any
 * server (Apache, Nginx, PHP-FPM) and are covered by tests.
 *
 * Public site: strict Content-Security-Policy with a per-request nonce —
 * only FEIRRY's own scripts run, nothing injected can execute.
 * Admin (Filament/Livewire needs inline Alpine code): a narrower policy
 * that still forbids framing, plugins, foreign forms and base hijacking.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = $request->is('admin', 'admin/*', 'livewire/*', 'livewire-*/*');

        if (! $admin) {
            Vite::useCspNonce();
        }

        $response = $next($request);

        $headers = $response->headers;
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()');
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $headers->remove('X-Powered-By');

        if ($admin) {
            // The back-office never appears in search engines.
            $headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        if ($request->isSecure() && app()->isProduction()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // The Vite dev server (npm run dev) serves scripts from another origin: no CSP while it runs.
        if (! Vite::isRunningHot()) {
            $headers->set('Content-Security-Policy', $admin ? $this->adminPolicy() : $this->publicPolicy());
        }

        return $response;
    }

    private function publicPolicy(): string
    {
        $nonce = Vite::cspNonce();

        return implode('; ', array_filter([
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: blob:",
            "font-src 'self' data:",
            "connect-src 'self'",
            "frame-src https://www.openstreetmap.org https://www.google.com",
            "media-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            app()->isProduction() ? 'upgrade-insecure-requests' : null,
        ]));
    }

    private function adminPolicy(): string
    {
        return implode('; ', [
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ]);
    }
}
