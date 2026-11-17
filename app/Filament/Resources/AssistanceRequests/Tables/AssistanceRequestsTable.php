<?php

namespace App\Filament\Resources\AssistanceRequests\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssistanceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label('Référence')->searchable()->weight('bold'),
                TextColumn::make('path')->label('Parcours')->badge()
                    ->formatStateUsing(fn ($state) => $state === 'visit' ? 'Visite technique' : 'Consultation'),
                TextColumn::make('name')->label('Nom')->searchable(),
                TextColumn::make('phone')->label('Téléphone'),
                TextColumn::make('status')->label('Statut')->badge(),
                TextColumn::make('created_at')->label('Reçue le')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([SelectFilter::make('status')->options(['new' => 'Nouvelle', 'eligible' => 'Éligible', 'scheduled' => 'Planifiée', 'done' => 'Traitée', 'declined' => 'Refusée'])])
            ->recordActions([EditAction::make()]);
    }
}
