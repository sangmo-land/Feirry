<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Auteur')
                    ->schema([
                        TextInput::make('author_name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(120),
                        TextInput::make('company')
                            ->label('Société')
                            ->maxLength(150),
                        Select::make('project_id')
                            ->label('Chantier concerné')
                            ->relationship('project', 'title_fr')
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('rating')
                            ->label('Note')
                            ->options([5 => '5 étoiles', 4 => '4 étoiles', 3 => '3 étoiles'])
                            ->default(5)
                            ->native(false),
                        FormFields::image('photo', 'Photo de l’auteur'),
                    ])
                    ->columns(2),

                FormFields::localeTabs(fn (string $l) => [
                    TextInput::make("author_role_{$l}")
                        ->label('Fonction')
                        ->maxLength(120)
                        ->columnSpanFull(),
                    Textarea::make("content_{$l}")
                        ->label('Témoignage')
                        ->rows(6)
                        ->required($l === 'fr')
                        ->columnSpanFull(),
                ]),

                FormFields::publication(),
            ]);
    }
}
