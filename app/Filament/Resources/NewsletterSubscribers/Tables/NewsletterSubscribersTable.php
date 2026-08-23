<?php

namespace App\Filament\Resources\NewsletterSubscribers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class NewsletterSubscribersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('email')->label('E-mail')->searchable()->copyable()->weight('bold'),
                TextColumn::make('name')->label('Nom')->placeholder('—'),
                TextColumn::make('locale')->label('Langue')->badge()->color('gray'),
                TextColumn::make('created_at')->label('Inscrit le')->dateTime('d/m/Y')->sortable(),
                ToggleColumn::make('is_active')->label('Actif'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
