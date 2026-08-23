<?php

namespace App\Filament\Resources\PostCategories\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rubrique')
                    ->schema([
                        TextInput::make('name_fr')
                            ->label('Nom')
                            ->required()
                            ->maxLength(120)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, callable $set, callable $get) {
                                if (blank($get('slug')) && filled($state)) {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                            }),
                        TextInput::make('name_en')->label('Nom (EN)')->maxLength(120),
                        FormFields::slug('name_fr', 'post_categories'),
                        \Filament\Forms\Components\ColorPicker::make('color')
                            ->label('Couleur')
                            ->default('#fe5000'),
                        TextInput::make('position')->label('Ordre')->numeric()->default(0),
                    ])
                    ->columns(2),
            ]);
    }
}
