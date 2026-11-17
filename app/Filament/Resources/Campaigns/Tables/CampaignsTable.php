<?php

namespace App\Filament\Resources\Campaigns\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CampaignsTable
{
    public static function configure(Table $table): Table
    {
        $states = ['open' => 'Ouverte', 'waitlist' => 'Liste d’attente', 'upcoming' => 'Ouverture prochaine', 'paused' => 'En pause', 'preparation' => 'Préparation', 'closed' => 'Terminée'];

        return $table
            ->columns([
                TextColumn::make('name')->label('Édition')->weight('bold'),
                TextColumn::make('status')->label('État saisi')->badge()
                    ->formatStateUsing(fn ($state) => ['draft' => 'Préparation', 'open' => 'Ouverte', 'paused' => 'En pause', 'closed' => 'Clôturée'][$state] ?? $state),
                TextColumn::make('public_state')->label('Affiché sur le site')->badge()->color('gray')
                    ->state(fn ($record) => $states[$record->publicState()] ?? $record->publicState()),
                TextColumn::make('announced_at')->label('Ouverture')->date('d/m/Y'),
                TextColumn::make('starts_at')->label('Interventions du')->date('d/m/Y'),
                TextColumn::make('ends_at')->label('au')->date('d/m/Y'),
                IconColumn::make('accepting')->label('Formulaires actifs')->boolean()->state(fn ($record) => $record->accepting()),
                TextColumn::make('requests_count')->label('Demandes')->counts('requests'),
            ])
            ->recordActions([EditAction::make()]);
    }
}
