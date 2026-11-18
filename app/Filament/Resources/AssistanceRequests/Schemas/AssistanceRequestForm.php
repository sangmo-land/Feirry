<?php

namespace App\Filament\Resources\AssistanceRequests\Schemas;

use App\Filament\Support\LeadAttachments;
use App\Http\Requests\StoreAssistanceRequest as Rules;
use App\Models\AssistanceRequest;
use App\Models\Campaign;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class AssistanceRequestForm
{
    public const LABELS = [
        'maison' => 'Maison', 'immeuble' => 'Immeuble', 'commerce' => 'Commerce', 'renovation' => 'Rénovation', 'autre' => 'Autre',
        'idee' => 'Idée', 'plans' => 'Plans', 'chantier' => 'Chantier', 'termine' => 'Terminé',
        'fondations' => 'Fondations', 'elevation' => 'Élévation', 'toiture' => 'Toiture', 'finitions' => 'Finitions',
        'plain-pied' => 'Plain-pied', 'r+1' => 'R+1', 'r+2' => 'R+2', 'r+3' => 'R+3', 'r+4+' => 'R+4 ou plus', 'inconnu' => 'Ne sait pas',
    ];

    private static function opts(array $keys): array
    {
        return collect($keys)->mapWithKeys(fn ($k) => [$k => self::LABELS[$k] ?? $k])->all();
    }

    public static function configure(Schema $schema): Schema
    {
        $isNew = fn ($record) => $record === null;
        // Applicant data is read-only once received from the site; editable for office entries.
        $locked = fn ($record) => $record !== null && $record->source === 'web';

        return $schema->components([
            Section::make('Traitement')
                ->description('Chaque changement de statut, d’affectation ou de programmation est inscrit dans l’historique.')
                ->hidden($isNew)
                ->schema([
                    TextInput::make('reference')->label('Référence')->disabled(),
                    Select::make('status')->label('Statut')->options(AssistanceRequest::STATUSES)->required()->native(false),
                    Select::make('assigned_to')->label('Responsable')
                        ->options(fn () => User::whereIn('role', ['admin', 'administration', 'referent'])->pluck('name', 'id'))
                        ->searchable()->native(false)
                        ->disabled(fn () => ! auth()->user()?->hasRole('admin', 'administration')),
                    Textarea::make('decision_reason')->label('Motif de décision (refus, hors périmètre, attente)')->rows(2)
                        ->visible(fn (Get $get) => in_array($get('status'), ['out_of_scope', 'waiting', 'incomplete', 'closed'], true)),
                    DateTimePicker::make('scheduled_at')->label('Date programmée')->native(false)->seconds(false)->displayFormat('d/m/Y H:i'),
                    Select::make('scheduled_mode')->label('Mode')->options(['office' => 'Au bureau', 'video' => 'Visioconférence', 'site' => 'Sur chantier'])->native(false),
                    TextInput::make('scheduled_team')->label('Équipe / intervenants')->maxLength(255),
                    DateTimePicker::make('report_due_at')->label('Rapport attendu le')->native(false)->seconds(false)->displayFormat('d/m/Y')
                        ->helperText('Calculé automatiquement : 5 jours ouvrés après « Réalisée ».'),
                    FileUpload::make('report_upload')->label('Ajouter un rapport (PDF, privé)')
                        ->disk('local')->visibility('private')->acceptedFileTypes(['application/pdf'])->maxSize(20480)
                        ->directory(fn ($record) => $record?->storageFolder('rapports'))
                        ->storeFileNamesIn('report_upload_name')->dehydrated(true)->columnSpanFull(),
                    TextEntry::make('reports_list')->label('Rapports transmis')->columnSpanFull()
                        ->state(fn ($record) => self::fileLinks($record, 'assistance-report', $record?->reports ?? [], 'Aucun rapport')),
                    Textarea::make('internal_notes')->label('Notes internes (jamais visibles du demandeur)')->rows(4)->columnSpanFull(),
                ])->columns(2),

            Section::make('Demande')
                ->schema([
                    Select::make('path')->label('Parcours')->options(['consultation' => 'A — Consultation de dossier', 'visit' => 'B — Visite de chantier'])
                        ->required()->native(false)->live()->disabled($locked),
                    Select::make('campaign_id')->label('Édition')->options(fn () => Campaign::orderByDesc('starts_at')->pluck('name', 'id'))->native(false)->disabled($locked),
                    TextInput::make('name')->label('Nom')->required()->disabled($locked),
                    TextInput::make('phone')->label('Téléphone / WhatsApp')->required()->disabled($locked),
                    TextInput::make('email')->label('E-mail')->email()->disabled($locked),
                    TextInput::make('city')->label('Ville / quartier')->required()->disabled($locked),
                    Select::make('project_type')->label('Type')->required()->native(false)->disabled($locked)
                        ->options(fn (Get $get) => self::opts($get('path') === 'visit' ? Rules::BUILDING_TYPES : Rules::PROJECT_TYPES)),
                    Select::make('progress')->label('Avancement / phase')->required()->native(false)->disabled($locked)
                        ->options(fn (Get $get) => self::opts($get('path') === 'visit' ? Rules::PHASES : Rules::PROGRESS)),
                    TextInput::make('land_area')->label('Surface du terrain (m²)')->numeric()->disabled($locked)->visible(fn (Get $get) => $get('path') !== 'visit'),
                    TextInput::make('footprint_area')->label('Emprise au sol (m²)')->numeric()->disabled($locked)->visible(fn (Get $get) => $get('path') !== 'visit'),
                    Select::make('levels')->label('Niveaux')->options(self::opts(Rules::LEVELS))->native(false)->disabled($locked)->visible(fn (Get $get) => $get('path') !== 'visit'),
                    Select::make('meeting_mode')->label('Mode souhaité')->options(['office' => 'Au bureau', 'video' => 'En visio'])->native(false)->disabled($locked)->visible(fn (Get $get) => $get('path') !== 'visit'),
                    TextInput::make('site_location')->label('Repère / localisation')->disabled($locked)->visible(fn (Get $get) => $get('path') === 'visit'),
                    TextInput::make('gps')->label('GPS')->disabled($locked)->visible(fn (Get $get) => $get('path') === 'visit'),
                    TextInput::make('availability')->label('Disponibilités indicatives')->disabled($locked)->visible(fn (Get $get) => $get('path') === 'visit'),
                    TextInput::make('access_constraints')->label('Informations d’accès')->disabled($locked)->visible(fn (Get $get) => $get('path') === 'visit'),
                    Toggle::make('danger_reported')->label('Danger immédiat signalé')->disabled($locked)->visible(fn (Get $get) => $get('path') === 'visit'),
                    Toggle::make('out_of_zone')->label('Hors zone de l’édition')->disabled()->visible(fn (Get $get) => $get('path') === 'visit'),
                    Textarea::make('description')->label('Question / motif')->required()->rows(4)->disabled($locked)->columnSpanFull(),
                    TextEntry::make('attachments_list')->label('Pièces du demandeur (privées)')->columnSpanFull()->hidden($isNew)
                        ->state(fn ($record) => self::fileLinks($record, 'assistance', $record?->attachments ?? [], 'Aucune pièce')),
                    TextEntry::make('consent')->label('Consentements')->hidden($isNew)->columnSpanFull()
                        ->state(fn ($record) => $record ? sprintf(
                            'Traitement : %s (notice %s) · Informations commerciales : %s',
                            $record->privacy_accepted_at?->format('d/m/Y H:i') ?? '—',
                            $record->notice_version ?? '—',
                            $record->marketing_consent ? 'oui, le '.$record->marketing_consent_at?->format('d/m/Y') : 'non',
                        ) : null),
                ])->columns(2),

            Section::make('Historique')
                ->hidden($isNew)->collapsible()
                ->schema([
                    TextEntry::make('history')->hiddenLabel()->columnSpanFull()
                        ->state(fn ($record) => new HtmlString($record?->logs()->with('user')->latest('id')->get()->map(fn ($l) => sprintf(
                            '<div style="padding:4px 0;border-bottom:1px solid rgba(127,127,127,.2)"><strong>%s</strong> · %s · %s</div>',
                            e($l->created_at?->format('d/m/Y H:i')),
                            e($l->user?->name ?? 'Système'),
                            e($l->detail ?: $l->action),
                        ))->implode('') ?: '<em>Aucun événement</em>')),
                ]),
        ]);
    }

    private static function fileLinks($record, string $type, array $files, string $empty): HtmlString
    {
        if (! $record || ! $files) {
            return new HtmlString('<em>'.e($empty).'</em>');
        }

        return new HtmlString(collect($files)->map(function ($file, $i) use ($record, $type) {
            $url = route('admin.lead-file', ['type' => $type, 'id' => $record->id, 'index' => $i]);
            $kb = isset($file['size']) ? ' ('.round($file['size'] / 1024).' Ko)' : '';

            return '<a href="'.e($url).'" style="color:#FE5000;font-weight:600">'.e($file['name'] ?? 'document').'</a>'.$kb;
        })->implode('<br>'));
    }
}
