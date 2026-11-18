<?php

namespace App\Filament\Resources\AssistanceRequests\Pages;

use App\Filament\Resources\AssistanceRequests\AssistanceRequestResource;
use App\Support\CdpMessages;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Storage;

class EditAssistanceRequest extends EditRecord
{
    protected static string $resource = AssistanceRequestResource::class;

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();
        abort_unless(auth()->user()?->canSeeAssistanceRequest($this->getRecord()), 403);
    }

    protected function getHeaderActions(): array
    {
        return [
            // « Copier le message » : ready-made texts, sent by a person, never automatically (CDC §14).
            Action::make('message')
                ->label('Copier un message')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('gray')
                ->modalHeading('Message type à envoyer au demandeur')
                ->modalDescription('Choisissez le modèle, relisez, puis copiez le texte (Ctrl+A, Ctrl+C) dans WhatsApp, SMS ou e-mail. Rien n’est envoyé automatiquement.')
                ->schema(fn () => [
                    Select::make('template')->label('Modèle')->options(CdpMessages::labels())->native(false)->live()
                        ->default(CdpMessages::defaultFor($this->getRecord()->status))
                        ->afterStateUpdated(fn ($state, Set $set) => $set('text', CdpMessages::render($state, $this->getRecord()))),
                    Textarea::make('text')->label('Texte')->rows(9)
                        ->default(fn () => CdpMessages::render(CdpMessages::defaultFor($this->getRecord()->status), $this->getRecord())),
                ])
                ->modalSubmitActionLabel('Noter dans l’historique')
                ->action(function (array $data) {
                    $this->getRecord()->log('message', 'Message préparé : '.(CdpMessages::labels()[$data['template'] ?? ''] ?? 'libre'));
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $upload = $data['report_upload'] ?? null;
        $name = $data['report_upload_name'] ?? null;
        unset($data['report_upload'], $data['report_upload_name']);

        foreach ((array) $upload as $path) {
            if (! $path || ! Storage::disk('local')->exists($path)) {
                continue;
            }
            $reports = $this->getRecord()->reports ?? [];
            $original = is_array($name) ? ($name[$path] ?? basename($path)) : ($name ?: basename($path));
            $reports[] = [
                'path' => $path,
                // Download name per CDC §11; never the client's name. The original name stays private metadata.
                'name' => sprintf('%s_Rapport_Preliminaire_v%02d.pdf', $this->getRecord()->reference, count($reports) + 1),
                'original_name' => $original,
                'size' => Storage::disk('local')->size($path),
                'added_at' => now()->toIso8601String(),
                'by' => auth()->id(),
            ];
            $data['reports'] = $reports;
            $this->getRecord()->log('report', 'Rapport ajouté : '.end($reports)['name']);
        }

        return $data;
    }
}
