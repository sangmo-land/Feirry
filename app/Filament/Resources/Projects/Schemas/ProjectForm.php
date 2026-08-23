<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Support\FormFields;
use App\Models\Service;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité du chantier')
                    ->schema([
                        FormFields::titleWithSlug('fr', 'Nom du projet'),
                        FormFields::slug(table: 'projects'),
                        TextInput::make('client_name')
                            ->label('Maître d’ouvrage')
                            ->maxLength(150),
                        Select::make('project_category_id')
                            ->label('Typologie')
                            ->relationship('category', 'name_fr')
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ])
                    ->columns(2),

                Section::make('Localisation')
                    ->schema([
                        TextInput::make('location')
                            ->label('Adresse affichée')
                            ->default('Douala, Cameroun')
                            ->required()
                            ->maxLength(180),
                        TextInput::make('city')
                            ->label('Ville')
                            ->default('Douala')
                            ->required()
                            ->maxLength(80),
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step('0.0000001')
                            ->placeholder('4.0483'),
                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step('0.0000001')
                            ->placeholder('9.7043'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Chiffres clés')
                    ->schema([
                        Select::make('status')
                            ->label('État du chantier')
                            ->options([
                                'completed' => 'Livré',
                                'ongoing' => 'En cours',
                                'upcoming' => 'À venir',
                            ])
                            ->default('completed')
                            ->required()
                            ->live()
                            ->native(false),
                        TextInput::make('progress')
                            ->label('Avancement (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(100)
                            ->visible(fn (callable $get) => $get('status') !== 'completed')
                            ->helperText('Affiché sous forme de barre sur la fiche projet.'),
                        TextInput::make('year')
                            ->label('Année')
                            ->numeric()
                            ->minValue(1990)
                            ->maxValue((int) date('Y') + 5),
                        DatePicker::make('completed_at')
                            ->label('Date de réception')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        TextInput::make('surface_m2')
                            ->label('Surface (m²)')
                            ->numeric()
                            ->suffix('m²'),
                        TextInput::make('duration_months')
                            ->label('Durée (mois)')
                            ->numeric()
                            ->suffix('mois'),
                        TextInput::make('budget_xaf')
                            ->label('Budget')
                            ->numeric()
                            ->suffix('FCFA')
                            ->helperText('Montant en francs CFA, sans espace ni séparateur.'),
                        Toggle::make('budget_public')
                            ->label('Afficher le budget publiquement')
                            ->helperText('Décoché, le montant reste interne.'),
                    ])
                    ->columns(2),

                FormFields::localeTabs(fn (string $l) => [
                    FormFields::titleWithSlug($l, 'Nom du projet')
                        ->columnSpanFull()
                        ->hidden($l === 'fr'),
                    FormFields::excerpt($l),
                    Textarea::make("challenge_{$l}")
                        ->label('La contrainte')
                        ->rows(4)
                        ->columnSpanFull(),
                    Textarea::make("solution_{$l}")
                        ->label('Notre réponse')
                        ->rows(4)
                        ->columnSpanFull(),
                    Textarea::make("outcome_{$l}")
                        ->label('Le résultat')
                        ->rows(4)
                        ->columnSpanFull(),
                    FormFields::richBody($l, 'Récit du chantier'),
                ]),

                Section::make('Visuels')
                    ->schema([
                        FormFields::image('cover_image', 'Image de couverture', 'projects/covers'),
                        FormFields::gallery('gallery', 'Galerie du chantier', 'projects/gallery'),
                    ])
                    ->columns(1),

                Section::make('Métiers mobilisés')
                    ->schema([
                        Select::make('services_used')
                            ->label('Métiers')
                            ->multiple()
                            ->options(fn () => Service::query()->pluck('title_fr', 'slug')->all())
                            ->helperText('Crée les liens vers les pages métier depuis la fiche projet.'),
                    ]),

                FormFields::publication(),
                FormFields::seo(),
            ]);
    }
}
