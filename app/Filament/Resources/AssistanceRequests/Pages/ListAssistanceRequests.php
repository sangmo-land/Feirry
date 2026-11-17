<?php

namespace App\Filament\Resources\AssistanceRequests\Pages;

use App\Filament\Resources\AssistanceRequests\AssistanceRequestResource;
use App\Filament\Widgets\CdpOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAssistanceRequests extends ListRecords
{
    protected static string $resource = AssistanceRequestResource::class;

    protected function getHeaderWidgets(): array
    {
        return [CdpOverview::class];
    }

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Demande reçue au bureau')];
    }
}
