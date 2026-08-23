<?php

namespace App\Filament\Resources\Milestones\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class MilestonesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('year')
            ->columns([
                TextColumn::make('year')->label('Année')->badge()->sortable(),
                TextColumn::make('title_fr')->label('Titre')->searchable()->weight('bold'),
                ToggleColumn::make('is_published')->label('Publiée'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
