<?php

namespace App\Filament\Resources\Faqs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class FaqsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('group')
                    ->label('Rubrique')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'general' => 'Généralités',
                        'services' => 'Prestations',
                        'process' => 'Chantier',
                        'quote' => 'Devis',
                        default => $state,
                    })
                    ->color('gray'),
                TextColumn::make('question_fr')->label('Question')->searchable()->weight('bold')->limit(70),
                ToggleColumn::make('is_published')->label('Publiée'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
