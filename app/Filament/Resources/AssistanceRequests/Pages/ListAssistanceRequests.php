<?php

namespace App\Filament\Resources\AssistanceRequests\Pages;

use App\Filament\Resources\AssistanceRequests\AssistanceRequestResource;
use App\Filament\Widgets\CdpOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAssistanceRequests extends ListRecords
{
    protected static string $resource = AssistanceRequestResource::class;

    protected static ?string $title = 'Demandes Coup de Pouce';

    protected function getHeaderWidgets(): array
    {
        return [CdpOverview::class];
    }

    /** Views: everything, to process, planning (scheduled by date), awaiting a report (CDC §10). */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Toutes'),
            'todo' => Tab::make('À traiter')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['received', 'incomplete', 'studying'])),
            'planning' => Tab::make('Planning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'scheduled')->reorder('scheduled_at')),
            'reports' => Tab::make('Rapports à rendre')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'done')->reorder('report_due_at')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Demande reçue au bureau')];
    }
}
