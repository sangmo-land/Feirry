<?php

namespace App\Filament\Resources\AssistanceRequests\Tables;

use App\Filament\Resources\AssistanceRequests\Schemas\AssistanceRequestForm;
use App\Models\AssistanceRequest;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssistanceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label('Référence')->searchable()->weight('bold')->copyable(),
                TextColumn::make('created_at')->label('Reçue le')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('path')->label('Type')->badge()
                    ->formatStateUsing(fn ($state) => $state === 'visit' ? 'B · Visite' : 'A · Consultation')
                    ->color(fn ($state) => $state === 'visit' ? 'warning' : 'info'),
                TextColumn::make('city')->label('Quartier')->searchable()->limit(28),
                TextColumn::make('name')->label('Demandeur')->searchable()->toggleable(),
                TextColumn::make('status')->label('Statut')->badge()
                    ->formatStateUsing(fn ($state) => AssistanceRequest::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'received', 'incomplete' => 'danger', 'studying', 'waiting' => 'warning', 'scheduled' => 'info', 'done', 'report_sent', 'closed' => 'success', default => 'gray',
                    }),
                TextColumn::make('assignee.name')->label('Responsable')->placeholder('—'),
                TextColumn::make('alert')->label('Alerte')->badge()->color('danger')
                    ->state(fn (AssistanceRequest $r) => collect([
                        $r->danger_reported ? 'Danger signalé' : null,
                        $r->needsVerification() ? 'À vérifier (+48 h)' : null,
                        $r->reportOverdue() ? 'Rapport en retard' : null,
                    ])->filter()->values()->all() ?: null)
                    ->placeholder('—'),
                TextColumn::make('source')->label('Origine')->formatStateUsing(fn ($s) => $s === 'office' ? 'Bureau' : 'Site')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('path')->label('Type')->options(['consultation' => 'A · Consultation', 'visit' => 'B · Visite']),
                SelectFilter::make('status')->label('Statut')->options(AssistanceRequest::STATUSES)->multiple(),
                SelectFilter::make('campaign_id')->label('Édition')->relationship('campaign', 'name'),
                Filter::make('city')->label('Quartier')
                    ->schema([\Filament\Forms\Components\TextInput::make('city')->label('Quartier contient')])
                    ->query(fn (Builder $query, array $data) => $query->when($data['city'] ?? null, fn ($q, $v) => $q->where('city', 'like', "%{$v}%"))),
                Filter::make('period')->label('Période')
                    ->schema([DatePicker::make('from')->label('Du')->native(false), DatePicker::make('until')->label('Au')->native(false)])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
                        ->when($data['until'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Exporter (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->visible(fn () => auth()->user()?->hasRole('admin', 'administration'))
                    ->requiresConfirmation()
                    ->modalDescription('L’export contient des données personnelles. Il est inscrit au journal de chaque demande exportée. Ne le partagez pas hors de l’équipe.')
                    ->action(fn ($livewire) => self::export($livewire->getFilteredTableQuery())),
            ])
            ->recordActions([EditAction::make()->label('Ouvrir')]);
    }

    /** Logged export (CDC §13). Plans and photos are never included. */
    public static function export(Builder $query): StreamedResponse
    {
        $rows = (clone $query)->with(['assignee', 'campaign'])->get();
        $rows->each(fn (AssistanceRequest $r) => $r->log('export', 'Incluse dans un export CSV'));
        $label = fn ($v) => AssistanceRequestForm::LABELS[$v] ?? $v;

        return response()->streamDownload(function () use ($rows, $label) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Référence', 'Reçue le', 'Édition', 'Type', 'Origine', 'Statut', 'Responsable', 'Nom', 'Téléphone', 'E-mail', 'Ville / quartier', 'Projet', 'Avancement', 'Danger', 'Hors zone', 'Programmée le', 'Rapport attendu', 'Rapport transmis', 'Accord commercial'], ';');
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->reference, $r->created_at?->format('d/m/Y H:i'), $r->campaign?->name,
                    $r->isVisit() ? 'Visite' : 'Consultation', $r->source === 'office' ? 'Bureau' : 'Site',
                    AssistanceRequest::STATUSES[$r->status] ?? $r->status, $r->assignee?->name,
                    $r->name, $r->phone, $r->email, $r->city, $label($r->project_type), $label($r->progress),
                    $r->danger_reported ? 'oui' : '', $r->out_of_zone ? 'oui' : '',
                    $r->scheduled_at?->format('d/m/Y H:i'), $r->report_due_at?->format('d/m/Y'), $r->report_sent_at?->format('d/m/Y'),
                    $r->marketing_consent ? 'oui' : 'non',
                ], ';');
            }
            fclose($out);
        }, 'coup-de-pouce-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
