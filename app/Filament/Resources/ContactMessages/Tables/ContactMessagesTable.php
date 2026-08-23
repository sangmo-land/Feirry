<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('is_read')
                    ->label('Lu')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-s-envelope')
                    ->trueColor('gray')
                    ->falseColor('warning'),

                TextColumn::make('created_at')
                    ->label('Reçu')
                    ->dateTime('d/m/Y H:i')
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('d/m/Y H:i'))
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Expéditeur')
                    ->searchable()
                    ->weight(fn ($record) => $record->is_read ? null : 'bold')
                    ->description(fn ($record) => $record->email),

                TextColumn::make('subject')
                    ->label('Objet')
                    ->searchable()
                    ->placeholder('—')
                    ->limit(40),

                TextColumn::make('message')
                    ->label('Message')
                    ->limit(70)
                    ->tooltip(fn ($record) => $record->message)
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('replied_at')
                    ->label('Répondu')
                    ->dateTime('d/m/Y')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_read')
                    ->label('Lu')
                    ->trueLabel('Lus')
                    ->falseLabel('Non lus'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Lire')
                    ->after(fn ($record) => $record->update(['is_read' => true])),
                Action::make('reply')
                    ->label('Répondre')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('gray')
                    ->url(fn ($record) => 'mailto:'.$record->email.'?subject='.rawurlencode('Re : '.($record->subject ?: 'Votre message')))
                    ->after(fn ($record) => $record->update(['is_read' => true, 'replied_at' => now()])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markRead')
                        ->label('Marquer comme lus')
                        ->icon('heroicon-o-envelope-open')
                        ->action(fn (Collection $records) => $records->each->update(['is_read' => true]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aucun message')
            ->emptyStateDescription('Les messages envoyés depuis la page Contact arrivent ici.');
    }
}
