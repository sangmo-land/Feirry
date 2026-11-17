<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AssistanceRequests\AssistanceRequestResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** Coup de Pouce counters (CDC §13): received, to process, scheduled, closed. */
class CdpOverview extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        $q = fn () => AssistanceRequestResource::getEloquentQuery();
        $late = $q()->get()->filter(fn ($r) => $r->needsVerification() || $r->reportOverdue())->count();

        return [
            Stat::make('Reçues', $q()->count()),
            Stat::make('À traiter', $q()->whereIn('status', ['received', 'incomplete', 'studying'])->count())
                ->description($late ? "{$late} en alerte (48 h / rapport)" : 'Aucune alerte')
                ->color($late ? 'danger' : 'success'),
            Stat::make('Programmées', $q()->where('status', 'scheduled')->count()),
            Stat::make('Clôturées', $q()->whereIn('status', ['report_sent', 'closed', 'out_of_scope'])->count()),
        ];
    }
}
