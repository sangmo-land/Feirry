<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

/** Coup de Pouce editions (CDC Coup de Pouce V2 §08, §17). */
class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Édition')
                ->description('Préparation = inscriptions fermées. Ouverte = formulaires actifs entre la date d’ouverture et la fin. En pause = formulaires coupés (ou liste d’attente si cochée). Clôturée = plus aucune bannière datée. Une édition passée ne s’affiche jamais comme ouverte.')
                ->schema([
                    TextInput::make('name')->label('Nom interne')->required()->maxLength(160)
                        ->live(onBlur: true)->afterStateUpdated(fn ($state, $set, $get) => $get('slug') ? null : $set('slug', Str::slug($state))),
                    TextInput::make('slug')->label('Identifiant')->required()->maxLength(160)->unique(ignoreRecord: true),
                    TextInput::make('edition')->label('Code d’édition (dossier de stockage)')->required()->maxLength(60)
                        ->helperText('ex. 2026-fin-annee — sert au rangement privé des pièces et rapports.'),
                    Select::make('status')->label('État')->required()->native(false)->default('draft')
                        ->options(['draft' => 'Préparation (fermée)', 'open' => 'Ouverte', 'paused' => 'En pause / complète', 'closed' => 'Clôturée']),
                    DatePicker::make('announced_at')->label('Ouverture des demandes')->native(false)->displayFormat('d/m/Y')
                        ->helperText('Affichée « Ouverture prochaine » seulement si la date est confirmée.'),
                    DatePicker::make('starts_at')->label('Début des interventions')->native(false)->displayFormat('d/m/Y'),
                    DatePicker::make('ends_at')->label('Fin des interventions')->native(false)->displayFormat('d/m/Y')->afterOrEqual('starts_at'),
                    Toggle::make('waitlist')->label('Accepter une liste d’attente quand l’édition est en pause'),
                    Toggle::make('consultation_enabled')->label('Parcours A — Consultation de dossier')->default(true),
                    Toggle::make('visit_enabled')->label('Parcours B — Visite de chantier')->default(true),
                ])->columns(2),
            Section::make('Places')
                ->description('Seules les places annoncées sont publiques. La capacité interne ne s’affiche jamais sur le site.')
                ->schema([
                    TextInput::make('public_consultations')->label('Consultations annoncées / semaine')->numeric()->minValue(0),
                    TextInput::make('public_visits')->label('Visites annoncées / semaine')->numeric()->minValue(0),
                    TextInput::make('internal_consultations')->label('Capacité interne consultations (privé)')->numeric()->minValue(0),
                    TextInput::make('internal_visits')->label('Capacité interne visites (privé)')->numeric()->minValue(0),
                ])->columns(2),
            Section::make('Zone et contact')
                ->schema([
                    TextInput::make('visit_zone_keywords')->label('Mots-clés de la zone de visite')
                        ->helperText('Séparés par des virgules, ex. douala. Hors zone : la demande est acceptée avec un message neutre.'),
                    TextInput::make('support_whatsapp')->label('WhatsApp d’assistance (facultatif)')->tel(),
                ])->columns(2),
            FormFields::localeTabs(fn (string $l) => [
                TextInput::make("banner_{$l}")->label('Libellé public de l’édition')->maxLength(160)->columnSpanFull(),
                TextInput::make("zone_{$l}")->label('Zone affichée')->maxLength(160)->columnSpanFull(),
                Textarea::make("terms_{$l}")->label('Conditions (une par ligne)')->rows(8)->columnSpanFull(),
                Textarea::make("confirmation_{$l}")->label('Texte de confirmation (vide = texte validé par défaut)')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }
}
