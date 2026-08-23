<?php

namespace App\Filament\Resources\Testimonials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('author_name')
                    ->label('Auteur')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn ($record) => trim(collect([$record->author_role_fr, $record->company])->filter()->implode(' · '))),

                TextColumn::make('content_fr')
                    ->label('Témoignage')
                    ->limit(70)
                    ->tooltip(fn ($record) => $record->content_fr),

                TextColumn::make('project.title_fr')
                    ->label('Chantier')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('rating')
                    ->label('Note')
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state)),

                ToggleColumn::make('is_featured')->label('En avant'),
                ToggleColumn::make('is_published')->label('Publié'),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('Publié'),
                TernaryFilter::make('is_featured')->label('Mis en avant'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
