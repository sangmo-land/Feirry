<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LatestQuoteRequests;
use App\Filament\Widgets\SiteOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->defaultAvatarProvider(\App\Filament\Support\InitialsAvatar::class)
            // Authenticator app (Google Authenticator, Microsoft Authenticator…). Optional per account until
            // ADMIN_MFA_REQUIRED=true, which forces every account to set it up at next sign-in.
            ->multiFactorAuthentication(
                [\Filament\Auth\MultiFactor\App\AppAuthentication::make()->recoverable()->brandName('FEIRRY Admin')],
                isRequired: (bool) config('site.admin_mfa_required'),
            )
            ->brandName(config('site.brand.name').' — Administration')
            ->favicon(asset('favicon.svg'))
            ->colors([
                'primary' => Color::hex('#fe5000'),
                'gray' => Color::Neutral,
                'danger' => Color::hex('#e01e37'),
                'success' => Color::hex('#1b7351'),
            ])
            ->font('Manrope')
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                SiteOverview::class,
                LatestQuoteRequests::class,
            ])
            // Groups follow how the client actually works: publish content,
            // maintain the company profile, then handle what comes in.
            ->navigationGroups([
                NavigationGroup::make('Contenu du site')->icon('heroicon-o-square-3-stack-3d'),
                NavigationGroup::make('Entreprise')->icon('heroicon-o-building-office'),
                NavigationGroup::make('Actualités')->icon('heroicon-o-newspaper'),
                NavigationGroup::make('Recrutement')->icon('heroicon-o-user-group'),
                NavigationGroup::make('Demandes entrantes')->icon('heroicon-o-inbox-arrow-down'),
                NavigationGroup::make('Réglages')->icon('heroicon-o-cog-6-tooth'),
            ])
            ->middleware([
                \App\Http\Middleware\SecurityHeaders::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
