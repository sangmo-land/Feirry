<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('')
                    ->disk('public')
                    ->height(44)
                    ->width(66),

                TextColumn::make('title_fr')
                    ->label('Métier')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->tagline_fr),

                TextColumn::make('starting_price')
                    ->label('Prix indicatif')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('quote_requests_count')
                    ->label('Devis reçus')
                    ->counts('quoteRequests')
                    ->badge()
                    ->color('gray'),

                ToggleColumn::make('is_featured')->label('En avant'),
                ToggleColumn::make('is_published')->label('Publié'),

                TextColumn::make('updated_at')
                    ->label('Modifié')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('Publié'),
                TernaryFilter::make('is_featured')->label('Mis en avant'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
