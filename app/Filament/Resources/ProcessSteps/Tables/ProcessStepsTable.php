<?php

namespace App\Filament\Resources\ProcessSteps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ProcessStepsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('position')->label('#')->badge()->color('gray'),
                TextColumn::make('title_fr')->label('Étape')->searchable()->weight('bold'),
                TextColumn::make('duration_fr')->label('Durée')->placeholder('—'),
                ToggleColumn::make('is_published')->label('Publiée'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
