<?php

namespace App\Filament\Resources\JobOpenings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class JobOpeningsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('title_fr')
                    ->label('Poste')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->department),

                TextColumn::make('contract_type')
                    ->label('Contrat')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('location')
                    ->label('Lieu')
                    ->toggleable(),

                TextColumn::make('applications_count')
                    ->label('Candidatures')
                    ->counts('applications')
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'success' : 'gray'),

                TextColumn::make('closes_at')
                    ->label('Clôture')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->color(fn ($record) => $record->closes_at?->isPast() ? 'danger' : null)
                    ->sortable(),

                ToggleColumn::make('is_published')->label('Publiée'),
            ])
            ->filters([
                SelectFilter::make('contract_type')
                    ->label('Type de contrat')
                    ->options(fn () => \App\Models\JobOpening::query()
                        ->distinct()
                        ->pluck('contract_type', 'contract_type')
                        ->all()),
                TernaryFilter::make('is_published')->label('Publiée'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
