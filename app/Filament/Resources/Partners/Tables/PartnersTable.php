<?php

namespace App\Filament\Resources\Partners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('logo')->label('')->disk('public')->height(30),
                TextColumn::make('name')->label('Nom')->searchable()->weight('bold'),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'client' => 'Client',
                        'partner' => 'Partenaire',
                        'supplier' => 'Fournisseur',
                        default => $state,
                    })
                    ->color('gray'),
                ToggleColumn::make('is_published')->label('Publié'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
