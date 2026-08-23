<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('year', 'desc')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('')
                    ->disk('public')
                    ->height(44)
                    ->width(66),

                TextColumn::make('title_fr')
                    ->label('Projet')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->client_name),

                TextColumn::make('category.name_fr')
                    ->label('Typologie')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('location')
                    ->label('Lieu')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('État')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'completed' => 'Livré',
                        'ongoing' => 'En cours',
                        'upcoming' => 'À venir',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'completed' => 'success',
                        'ongoing' => 'warning',
                        default => 'info',
                    })
                    ->sortable(),

                TextColumn::make('year')
                    ->label('Année')
                    ->sortable(),

                TextColumn::make('surface_m2')
                    ->label('Surface')
                    ->numeric(thousandsSeparator: ' ')
                    ->suffix(' m²')
                    ->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('is_featured')->label('En avant'),
                ToggleColumn::make('is_published')->label('Publié'),
            ])
            ->filters([
                SelectFilter::make('project_category_id')
                    ->label('Typologie')
                    ->relationship('category', 'name_fr')
                    ->preload(),
                SelectFilter::make('status')
                    ->label('État')
                    ->options([
                        'completed' => 'Livré',
                        'ongoing' => 'En cours',
                        'upcoming' => 'À venir',
                    ]),
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
