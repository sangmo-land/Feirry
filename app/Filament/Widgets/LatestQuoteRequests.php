<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\QuoteRequests\QuoteRequestResource;
use App\Filament\Resources\QuoteRequests\Schemas\QuoteRequestForm;
use App\Models\QuoteRequest;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestQuoteRequests extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Dernières demandes de devis')
            ->description('Les demandes non traitées, la plus récente en premier.')
            ->query(
                QuoteRequest::query()
                    ->whereIn('status', ['new', 'qualified', 'site_visit'])
                    ->latest()
            )
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('reference')
                    ->label('Référence')
                    ->fontFamily('mono')
                    ->weight('bold'),

                TextColumn::make('created_at')
                    ->label('Reçue')
                    ->since()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Prospect')
                    ->description(fn (QuoteRequest $record) => $record->company ?: $record->email)
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->copyable(),

                TextColumn::make('project_type')
                    ->label('Projet')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('budget_range')
                    ->label('Budget')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => QuoteRequestForm::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => $state === 'new' ? 'warning' : 'info'),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Ouvrir')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (QuoteRequest $record) => QuoteRequestResource::getUrl('edit', ['record' => $record])),
            ])
            ->emptyStateHeading('Rien en attente')
            ->emptyStateDescription('Toutes les demandes de devis ont été traitées.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    protected function getTableQuery(): ?Builder
    {
        return null;
    }
}
