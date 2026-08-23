<?php

namespace App\Filament\Resources\Milestones\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MilestoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Date clé')
                    ->schema([
                        TextInput::make('year')
                            ->label('Année')
                            ->numeric()
                            ->required()
                            ->minValue(1950)
                            ->maxValue((int) date('Y') + 5),
                        TextInput::make('position')->label('Ordre')->numeric()->default(0),
                        \Filament\Forms\Components\Toggle::make('is_published')->label('Publiée')->default(true),
                        FormFields::image('image', 'Illustration'),
                    ])
                    ->columns(3),

                FormFields::localeTabs(fn (string $l) => [
                    TextInput::make("title_{$l}")
                        ->label('Titre')
                        ->required($l === 'fr')
                        ->maxLength(160)
                        ->columnSpanFull(),
                    Textarea::make("description_{$l}")->label('Description')->rows(4)->columnSpanFull(),
                ]),
            ]);
    }
}
