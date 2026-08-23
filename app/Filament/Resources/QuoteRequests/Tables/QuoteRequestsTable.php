<?php

namespace App\Filament\Resources\QuoteRequests\Tables;

use App\Filament\Resources\QuoteRequests\Schemas\QuoteRequestForm;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuoteRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')
                    ->label('Référence')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->fontFamily('mono'),

                TextColumn::make('created_at')
                    ->label('Reçue')
                    ->dateTime('d/m/Y H:i')
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('d/m/Y H:i'))
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Prospect')
                    ->searchable()
                    ->description(fn ($record) => $record->company ?: $record->email),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->copyable()
                    ->url(fn ($record) => 'tel:'.preg_replace('/\s+/', '', (string) $record->phone)),

                TextColumn::make('project_type')
                    ->label('Projet')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('budget_range')
                    ->label('Budget')
                    ->toggleable(),

                TextColumn::make('location')
                    ->label('Lieu')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                SelectColumn::make('status')
                    ->label('Statut')
                    ->options(QuoteRequestForm::STATUSES)
                    ->selectablePlaceholder(false)
                    ->rules(['required']),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(QuoteRequestForm::STATUSES)
                    ->multiple(),
                SelectFilter::make('service_id')
                    ->label('Métier')
                    ->relationship('service', 'title_fr')
                    ->preload(),
                Filter::make('uncontacted')
                    ->label('Jamais rappelées')
                    ->query(fn (Builder $query) => $query->whereNull('contacted_at')),
            ])
            ->recordActions([
                Action::make('call')
                    ->label('Appeler')
                    ->icon('heroicon-o-phone')
                    ->color('gray')
                    ->url(fn ($record) => 'tel:'.preg_replace('/\s+/', '', (string) $record->phone)),
                Action::make('email')
                    ->label('Répondre')
                    ->icon('heroicon-o-envelope')
                    ->color('gray')
                    ->url(fn ($record) => 'mailto:'.$record->email.'?subject='.rawurlencode("Votre demande de devis {$record->reference}")),
                EditAction::make()->label('Suivi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aucune demande de devis')
            ->emptyStateDescription('Les demandes envoyées depuis la page « Demander un devis » apparaissent ici.');
    }
}
