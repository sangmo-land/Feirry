<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** Coup de Pouce counters (CDC §13): received, to process, scheduled, closed. */
class CdpOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    /** Aggregated counts only: the communication role sees figures, never requests (CDC §10). */
    public static function canView(): bool
    {
        return (bool) auth()->user()?->hasRole('admin', 'administration', 'referent', 'communication');
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $q = fn () => $user?->hasRole('referent')
            ? \App\Models\AssistanceRequest::where('assigned_to', $user->id)
            : \App\Models\AssistanceRequest::query();
        $late = $q()->get()->filter(fn ($r) => $r->needsVerification() || $r->reportOverdue())->count();

        return [
            Stat::make('Coup de Pouce — reçues', $q()->count()),
            Stat::make('À traiter', $q()->whereIn('status', ['received', 'incomplete', 'studying'])->count())
                ->description($late ? "{$late} en alerte (48 h / rapport)" : 'Aucune alerte')
                ->color($late ? 'danger' : 'success'),
            Stat::make('Programmées', $q()->where('status', 'scheduled')->count()),
            Stat::make('Clôturées', $q()->whereIn('status', ['report_sent', 'closed', 'out_of_scope'])->count()),
        ];
    }
}
