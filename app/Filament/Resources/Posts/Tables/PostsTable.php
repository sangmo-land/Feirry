<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('')
                    ->disk('public')
                    ->height(40)
                    ->width(60),

                TextColumn::make('title_fr')
                    ->label('Titre')
                    ->searchable()
                    ->weight('bold')
                    ->limit(60),

                TextColumn::make('category.name_fr')
                    ->label('Rubrique')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('author.name')
                    ->label('Auteur')
                    ->toggleable(),

                TextColumn::make('published_at')
                    ->label('Publié le')
                    ->dateTime('d/m/Y')
                    ->placeholder('—')
                    ->color(fn ($record) => $record->published_at?->isFuture() ? 'warning' : null)
                    ->description(fn ($record) => $record->published_at?->isFuture() ? 'Programmé' : null)
                    ->sortable(),

                TextColumn::make('views')
                    ->label('Vues')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                ToggleColumn::make('is_featured')->label('À la une'),
                ToggleColumn::make('is_published')->label('Publié'),
            ])
            ->filters([
                SelectFilter::make('post_category_id')
                    ->label('Rubrique')
                    ->relationship('category', 'name_fr')
                    ->preload(),
                TernaryFilter::make('is_published')->label('Publié'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
