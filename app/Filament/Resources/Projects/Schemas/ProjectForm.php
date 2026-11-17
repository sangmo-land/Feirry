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

                Section::make('Fiche projet (site)')
                    ->description('Ce que FEIRRY a réellement fait : n’indiquez que des informations vérifiées.')
                    ->schema([
                        \Filament\Forms\Components\CheckboxList::make('families')
                            ->label('Filtres (familles)')
                            ->options(['architecture' => 'Architecture', 'construction' => 'Construction', 'design' => 'Design', 'suivi' => 'Suivi & expertise'])
                            ->columns(1),
                        \Filament\Forms\Components\CheckboxList::make('roles')
                            ->label('Rôle contractuel de FEIRRY')
                            ->options(['design' => 'Conçu', 'build' => 'Construit', 'follow' => 'Suivi', 'expert' => 'Expertisé'])
                            ->helperText('Vide = « Mission à confirmer » sur le site.')
                            ->columns(1),
                        Select::make('stage')
                            ->label('Stade')
                            ->options(['design' => 'Conception', 'works' => 'Travaux en cours', 'done' => 'Achevé', 'tbc' => 'À confirmer'])
                            ->default('tbc')->required()->native(false),
                        TextInput::make('period')->label('Période')->placeholder('ex. 2024 – 2025')->maxLength(60),
                        Select::make('visibility')
                            ->label('Visibilité')
                            ->options(['draft' => 'Brouillon', 'published' => 'Publié', 'archived' => 'Archivé'])
                            ->default('draft')->required()->native(false)
                            ->helperText('Brouillons et archives ne sont ni affichés ni indexés.'),
                        TextInput::make('cover_position')->label('Cadrage de l’image')->default('center 50%')->helperText('ex. center 30% (déplace le cadrage vers le haut)'),
                        Toggle::make('visual_is_provisional')->label('Visuel provisoire (badge affiché)')->default(true),
                        Toggle::make('photo_rights_confirmed')->label('Droits photo et autorisation de diffusion confirmés'),
                        TextInput::make('photo_credits')->label('Crédits photo')->maxLength(160),
                    ])
                    ->columns(2),

                FormFields::localeTabs(fn (string $l) => [
                    TextInput::make("programme_{$l}")->label('Programme')->maxLength(255)->columnSpanFull(),
                    TextInput::make("mission_{$l}")->label('Mission FEIRRY')->maxLength(255)->columnSpanFull(),
                    TextInput::make("stage_note_{$l}")->label('Précision sur le stade')->maxLength(255)->columnSpanFull(),
                    \Filament\Forms\Components\TagsInput::make("deliverables_{$l}")->label('Livrables')->columnSpanFull(),
                ]),

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
