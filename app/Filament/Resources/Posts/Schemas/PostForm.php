<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Article')
                    ->schema([
                        FormFields::titleWithSlug('fr', 'Titre'),
                        FormFields::slug(table: 'posts'),
                        Select::make('post_category_id')
                            ->label('Rubrique')
                            ->relationship('category', 'name_fr')
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('author_id')
                            ->label('Auteur')
                            ->relationship('author', 'name')
                            ->default(fn () => auth()->id())
                            ->searchable()
                            ->preload()
                            ->native(false),
                        DateTimePicker::make('published_at')
                            ->label('Date de publication')
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Une date future programme la parution.'),
                        TextInput::make('reading_minutes')
                            ->label('Temps de lecture (min)')
                            ->numeric()
                            ->default(4)
                            ->minValue(1)
                            ->maxValue(60),
                        FormFields::list('tags', 'Mots-clés', 'Ajouter un mot-clé…'),
                    ])
                    ->columns(2),

                FormFields::localeTabs(fn (string $l) => [
                    FormFields::titleWithSlug($l, 'Titre')
                        ->columnSpanFull()
                        ->hidden($l === 'fr'),
                    FormFields::excerpt($l, 'Chapô'),
                    FormFields::richBody($l, 'Corps de l’article'),
                ]),

                Section::make('Visuel')
                    ->schema([FormFields::image()]),

                Section::make('Publication')
                    ->schema([
                        Toggle::make('is_published')->label('Publié')->default(true),
                        Toggle::make('is_featured')->label('À la une'),
                    ])
                    ->columns(2),

                FormFields::seo(),
            ]);
    }
}
