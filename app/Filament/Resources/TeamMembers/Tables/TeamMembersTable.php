<?php

namespace App\Filament\Resources\TeamMembers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TeamMembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                ImageColumn::make('photo')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->height(40),

                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->role_fr),

                TextColumn::make('years_experience')
                    ->label('Expérience')
                    ->suffix(' ans')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(),

                ToggleColumn::make('is_leadership')->label('Direction'),
                ToggleColumn::make('is_published')->label('Publié'),
            ])
            ->filters([
                TernaryFilter::make('is_leadership')->label('Direction'),
                TernaryFilter::make('is_published')->label('Publié'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
