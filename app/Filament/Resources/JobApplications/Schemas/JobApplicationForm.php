<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobApplicationForm
{
    public const STATUSES = [
        'received' => 'Reçue',
        'screening' => 'En cours de tri',
        'interview' => 'Entretien',
        'offer' => 'Proposition faite',
        'hired' => 'Recrutée',
        'rejected' => 'Écartée',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Suivi')
                    ->schema([
                        Select::make('status')
                            ->label('Statut')
                            ->options(self::STATUSES)
                            ->default('received')
                            ->required()
                            ->native(false),
                        Textarea::make('internal_notes')
                            ->label('Notes internes')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Candidature')
                    ->schema([
                        Select::make('job_opening_id')
                            ->label('Offre')
                            ->relationship('jobOpening', 'title_fr')
                            ->disabled(),
                        TextInput::make('name')->label('Nom')->disabled(),
                        TextInput::make('email')->label('E-mail')->disabled(),
                        TextInput::make('phone')->label('Téléphone')->disabled(),
                        TextInput::make('years_experience')->label("Années d'expérience")->disabled(),
                        TextInput::make('linkedin')->label('LinkedIn')->disabled(),
                        Textarea::make('cover_letter')
                            ->label('Lettre de motivation')
                            ->rows(8)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}
