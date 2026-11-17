<?php

namespace App\Filament\Resources\Offers\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OfferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Formule')
                ->schema([
                    TextInput::make('slug')->label('Identifiant URL')->required()->maxLength(60)->unique(ignoreRecord: true),
                    TextInput::make('position')->label('Ordre')->numeric()->default(0),
                    Select::make('quote_need')->label('Besoin présélectionné dans « Mon projet »')
                        ->options(['conception' => 'Conception', 'construction' => 'Construction', 'renovation' => 'Rénovation', 'suivi' => 'Suivi', 'autre' => 'Autre'])
                        ->default('conception')->native(false),
                    Toggle::make('is_recommended')->label('Recommandée (badge)'),
                    Toggle::make('is_published')->label('Publiée')->default(true),
                ])->columns(3),
            FormFields::localeTabs(fn (string $l) => [
                TextInput::make("title_{$l}")->label('Titre')->required($l === 'fr')->maxLength(120),
                TextInput::make("subtitle_{$l}")->label('Sous-titre')->maxLength(255),
                TextInput::make("audience_{$l}")->label('Cible')->maxLength(255),
                TextInput::make("price_label_{$l}")->label('Libellé de prix')->helperText('« Sur devis » sauf grille tarifaire validée.')->maxLength(120),
                TextInput::make("cta_{$l}")->label('Texte du bouton')->maxLength(80),
                TagsInput::make("included_{$l}")->label('Inclus')->columnSpanFull(),
                TagsInput::make("excluded_{$l}")->label('Exclus')->columnSpanFull(),
                TagsInput::make("steps_{$l}")->label('Étapes')->columnSpanFull(),
                TagsInput::make("price_factors_{$l}")->label('Facteurs de prix')->columnSpanFull(),
            ]),
        ]);
    }
}
