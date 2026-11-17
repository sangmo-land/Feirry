<?php

namespace App\Filament\Resources\Offers\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class OffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('title_fr')->label('Formule')->weight('bold'),
                TextColumn::make('price_label_fr')->label('Prix'),
                IconColumn::make('is_recommended')->label('Recommandée')->boolean(),
                ToggleColumn::make('is_published')->label('Publiée'),
            ])
            ->recordActions([EditAction::make()]);
    }
}
