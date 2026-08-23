<?php

namespace App\Filament\Resources\JobOpenings\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobOpeningForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Le poste')
                    ->schema([
                        FormFields::titleWithSlug('fr', 'Intitulé du poste'),
                        FormFields::slug(table: 'job_openings'),
                        TextInput::make('department')
                            ->label('Département')
                            ->datalist(['Opérations', "Bureau d'études", 'QHSE', 'Administration', 'Commercial'])
                            ->maxLength(80),
                        TextInput::make('location')
                            ->label('Lieu')
                            ->default('Douala, Cameroun')
                            ->required()
                            ->maxLength(120),
                        Select::make('contract_type')
                            ->label('Type de contrat')
                            ->options([
                                'CDI' => 'CDI',
                                'CDD 12 mois' => 'CDD 12 mois',
                                'CDD 24 mois' => 'CDD 24 mois',
                                'Stage' => 'Stage',
                                "Contrat d'apprentissage" => "Contrat d'apprentissage",
                                'Mission' => 'Mission ponctuelle',
                            ])
                            ->default('CDI')
                            ->required()
                            ->native(false),
                        TextInput::make('experience_level')
                            ->label('Expérience attendue')
                            ->placeholder('5 ans minimum')
                            ->maxLength(80),
                        TextInput::make('salary_range')
                            ->label('Rémunération')
                            ->placeholder('750 000 à 1 100 000 FCFA brut / mois')
                            ->maxLength(120)
                            ->columnSpanFull(),
                        DatePicker::make('closes_at')
                            ->label('Date de clôture')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->helperText("Passée cette date l'offre disparaît du site automatiquement."),
                    ])
                    ->columns(2),

                FormFields::localeTabs(fn (string $l) => [
                    FormFields::titleWithSlug($l, 'Intitulé du poste')
                        ->columnSpanFull()
                        ->hidden($l === 'fr'),
                    FormFields::richBody($l, 'Description du poste')
                        ->statePath("description_{$l}"),
                    FormFields::list("requirements_{$l}", 'Profil recherché', 'Ajouter un critère…'),
                    FormFields::list("benefits_{$l}", 'Ce que nous offrons', 'Ajouter un avantage…'),
                ]),

                FormFields::publication(featured: false),
            ]);
    }
}
