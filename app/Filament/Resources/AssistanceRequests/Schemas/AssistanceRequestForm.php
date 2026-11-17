<?php

namespace App\Filament\Resources\AssistanceRequests\Schemas;

use App\Filament\Support\LeadAttachments;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssistanceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Traitement')
                ->schema([
                    TextInput::make('reference')->label('Référence')->disabled(),
                    Select::make('status')->label('Statut')
                        ->options(['new' => 'Nouvelle', 'eligible' => 'Éligible', 'scheduled' => 'Planifiée', 'done' => 'Traitée', 'declined' => 'Refusée (motivée)'])
                        ->required()->native(false),
                    Textarea::make('internal_notes')->label('Notes internes')->rows(4)->columnSpanFull(),
                ])->columns(2),
            Section::make('Demande')
                ->schema([
                    TextInput::make('path')->label('Parcours')->disabled()
                        ->formatStateUsing(fn ($state) => $state === 'visit' ? 'Visite technique' : 'Consultation de dossier'),
                    TextInput::make('name')->label('Nom')->disabled(),
                    TextInput::make('phone')->label('Téléphone')->disabled(),
                    TextInput::make('email')->label('E-mail')->disabled(),
                    TextInput::make('city')->label('Ville')->disabled(),
                    TextInput::make('project_type')->label('Type de projet')->disabled(),
                    TextInput::make('meeting_mode')->label('Rendez-vous')->disabled()
                        ->formatStateUsing(fn ($state) => ['office' => 'Au bureau', 'video' => 'En visio'][$state] ?? $state),
                    TextInput::make('availability')->label('Disponibilité')->disabled(),
                    Textarea::make('description')->label('Description')->disabled()->columnSpanFull(),
                    TextInput::make('site_location')->label('Localisation du terrain')->disabled()->columnSpanFull(),
                    Textarea::make('objective')->label('Objectif')->disabled()->columnSpanFull(),
                    Textarea::make('access_constraints')->label('Contraintes d’accès')->disabled()->columnSpanFull(),
                    LeadAttachments::field('assistance'),
                ])->columns(2),
        ]);
    }
}
