<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\JobApplication;
use App\Models\Post;
use App\Models\Project;
use App\Models\QuoteRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Cette semaine';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $since = now()->subDays(7);

        $newQuotes = QuoteRequest::query()->where('created_at', '>=', $since)->count();
        $openQuotes = QuoteRequest::query()->whereIn('status', ['new', 'qualified', 'site_visit'])->count();
        $unread = ContactMessage::query()->where('is_read', false)->count();
        $applications = JobApplication::query()->where('status', 'received')->count();

        // Seven-day counts, oldest first, for the sparklines.
        $quoteTrend = collect(range(6, 0))
            ->map(fn (int $days) => QuoteRequest::query()
                ->whereDate('created_at', now()->subDays($days)->toDateString())
                ->count())
            ->all();

        return [
            Stat::make('Demandes de devis', $newQuotes)
                ->description($openQuotes.' en cours de traitement')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($quoteTrend)
                ->color($newQuotes > 0 ? 'success' : 'gray'),

            Stat::make('Messages non lus', $unread)
                ->description($unread > 0 ? 'À traiter' : 'Boîte à jour')
                ->descriptionIcon($unread > 0 ? 'heroicon-m-envelope' : 'heroicon-m-check-circle')
                ->color($unread > 0 ? 'warning' : 'gray'),

            Stat::make('Candidatures à trier', $applications)
                ->description('Statut « reçue »')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color($applications > 0 ? 'warning' : 'gray'),

            Stat::make('Réalisations en ligne', Project::query()->where('is_published', true)->count())
                ->description(Project::query()->where('status', 'ongoing')->count().' chantiers en cours')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Articles publiés', Post::query()->published()->count())
                ->description(number_format(Post::query()->sum('views'), 0, ',', ' ').' lectures cumulées')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('gray'),
        ];
    }
}
