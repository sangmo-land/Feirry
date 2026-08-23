<?php

namespace App\Filament\Resources\JobApplications\Tables;

use App\Filament\Resources\JobApplications\Schemas\JobApplicationForm;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class JobApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Reçue')
                    ->dateTime('d/m/Y')
                    ->since()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Candidat')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->email),

                TextColumn::make('jobOpening.title_fr')
                    ->label('Offre')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('years_experience')
                    ->label('Expérience')
                    ->suffix(' ans')
                    ->placeholder('—')
                    ->toggleable(),

                SelectColumn::make('status')
                    ->label('Statut')
                    ->options(JobApplicationForm::STATUSES)
                    ->selectablePlaceholder(false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(JobApplicationForm::STATUSES)
                    ->multiple(),
                SelectFilter::make('job_opening_id')
                    ->label('Offre')
                    ->relationship('jobOpening', 'title_fr')
                    ->preload(),
            ])
            ->recordActions([
                Action::make('cv')
                    ->label('CV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    // CVs live on the private disk, so they are streamed rather
                    // than exposed under a public URL.
                    ->action(fn ($record) => $record->cv_path && Storage::disk('local')->exists($record->cv_path)
                        ? Storage::disk('local')->download($record->cv_path, $record->name.' - CV.'.pathinfo($record->cv_path, PATHINFO_EXTENSION))
                        : null)
                    ->visible(fn ($record) => filled($record->cv_path)),
                EditAction::make()->label('Suivi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->emptyStateHeading('Aucune candidature');
    }
}
