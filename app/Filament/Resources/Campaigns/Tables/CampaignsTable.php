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
        return $table
            ->columns([
                TextColumn::make('name')->label('Session')->weight('bold'),
                TextColumn::make('status')->label('Statut')->badge()
                    ->formatStateUsing(fn ($state) => ['draft' => 'Brouillon', 'open' => 'Ouverte', 'closed' => 'Clôturée'][$state] ?? $state),
                TextColumn::make('starts_at')->label('Début')->date('d/m/Y'),
                TextColumn::make('ends_at')->label('Fin')->date('d/m/Y'),
                IconColumn::make('accepting')->label('Inscriptions actives')->boolean()->state(fn ($record) => $record->isOpen()),
                TextColumn::make('requests_count')->label('Demandes')->counts('requests'),
            ])
            ->recordActions([EditAction::make()]);
    }
}
