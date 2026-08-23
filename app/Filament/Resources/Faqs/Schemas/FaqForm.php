<?php

namespace App\Filament\Resources\Faqs\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Classement')
                    ->schema([
                        \Filament\Forms\Components\Select::make('group')
                            ->label('Rubrique')
                            ->options([
                                'general' => 'Généralités',
                                'services' => 'Prestations',
                                'process' => 'Déroulé du chantier',
                                'quote' => 'Devis & paiement',
                            ])
                            ->default('general')
                            ->required()
                            ->native(false),
                        TextInput::make('position')->label('Ordre')->numeric()->default(0),
                        \Filament\Forms\Components\Toggle::make('is_published')->label('Publiée')->default(true),
                    ])
                    ->columns(3),

                FormFields::localeTabs(fn (string $l) => [
                    TextInput::make("question_{$l}")
                        ->label('Question')
                        ->required($l === 'fr')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Textarea::make("answer_{$l}")
                        ->label('Réponse')
                        ->rows(6)
                        ->required($l === 'fr')
                        ->columnSpanFull(),
                ]),
            ]);
    }
}
