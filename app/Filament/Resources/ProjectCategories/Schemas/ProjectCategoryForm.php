<?php

namespace App\Filament\Resources\ProjectCategories\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Typologie')
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
                        FormFields::slug('name_fr', 'project_categories'),
                        TextInput::make('position')->label('Ordre')->numeric()->default(0),
                        Textarea::make('description_fr')->label('Description')->rows(3)->columnSpanFull(),
                        Textarea::make('description_en')->label('Description (EN)')->rows(3)->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
