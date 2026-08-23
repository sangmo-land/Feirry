<?php

namespace App\Filament\Resources\ProcessSteps\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProcessStepForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormFields::localeTabs(fn (string $l) => [
                    TextInput::make("title_{$l}")
                        ->label('Titre de l’étape')
                        ->required($l === 'fr')
                        ->maxLength(160)
                        ->columnSpanFull(),
                    TextInput::make("duration_{$l}")
                        ->label('Durée indicative')
                        ->placeholder('3 à 8 semaines')
                        ->maxLength(80)
                        ->columnSpanFull(),
                    Textarea::make("description_{$l}")->label('Description')->rows(5)->columnSpanFull(),
                    FormFields::list("deliverables_{$l}", 'Livrables', 'Ajouter un livrable…'),
                ]),

                Section::make('Réglages')
                    ->schema([
                        \Filament\Forms\Components\Select::make('icon')
                            ->label('Icône')
                            ->options(\App\Filament\Resources\Services\Schemas\ServiceForm::ICONS)
                            ->default('compass')
                            ->native(false),
                        TextInput::make('position')->label('Numéro de l’étape')->numeric()->default(0),
                        \Filament\Forms\Components\Toggle::make('is_published')->label('Publiée')->default(true),
                    ])
                    ->columns(3),
            ]);
    }
}
