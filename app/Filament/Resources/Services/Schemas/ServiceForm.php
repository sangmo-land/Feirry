<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    /** Icon names the React front end knows how to render. */
    public const ICONS = [
        'building-2' => 'Bâtiment',
        'paint-roller' => 'Peinture / finitions',
        'hard-hat' => 'Casque de chantier',
        'wrench' => 'Clé / rénovation',
        'ruler' => 'Règle / études',
        'key-round' => 'Clé en main',
        'route' => 'Voirie',
        'factory' => 'Industrie',
        'landmark' => 'Équipement public',
        'home' => 'Résidentiel',
        'compass' => 'Conception',
        'clipboard-check' => 'Contrôle',
        'shield-check' => 'Sécurité',
        'layers' => 'Structure',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité')
                    ->schema([
                        FormFields::titleWithSlug('fr', 'Nom du métier'),
                        FormFields::slug(table: 'services'),
                        Select::make('icon')
                            ->label('Icône')
                            ->options(self::ICONS)
                            ->default('building-2')
                            ->required()
                            ->native(false),
                        TextInput::make('starting_price')
                            ->label('Prix indicatif')
                            ->placeholder('185 000 FCFA / m²')
                            ->maxLength(80)
                            ->helperText('Affiché sur la carte du métier. Laissez vide pour ne rien afficher.'),
                    ])
                    ->columns(2),

                FormFields::localeTabs(fn (string $l) => [
                    FormFields::titleWithSlug($l, 'Nom du métier')
                        ->columnSpan($l === 'fr' ? 1 : 2)
                        ->hidden($l === 'fr'),
                    TextInput::make("tagline_{$l}")
                        ->label('Sous-titre')
                        ->maxLength(120)
                        ->columnSpanFull(),
                    FormFields::excerpt($l),
                    FormFields::richBody($l, 'Présentation détaillée'),
                    FormFields::list("features_{$l}", 'Ce que comprend la prestation', 'Ajouter une prestation…'),
                ]),

                Section::make('Visuels')
                    ->schema([
                        FormFields::image(),
                        FormFields::gallery(),
                    ])
                    ->columns(1),

                FormFields::publication(),
                FormFields::seo(),
            ]);
    }
}
