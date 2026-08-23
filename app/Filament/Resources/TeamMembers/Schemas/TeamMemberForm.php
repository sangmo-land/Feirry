<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom et prénom')
                            ->required()
                            ->maxLength(120)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, callable $set, callable $get) {
                                if (blank($get('slug')) && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        FormFields::slug('name', 'team_members'),
                        FormFields::image('photo', 'Photo')
                            ->helperText('Portrait vertical. Sans photo, le site affiche un monogramme.'),
                        TextInput::make('years_experience')
                            ->label("Années d'expérience")
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(60),
                    ])
                    ->columns(2),

                FormFields::localeTabs(fn (string $l) => [
                    TextInput::make("role_{$l}")
                        ->label('Fonction')
                        ->required($l === 'fr')
                        ->maxLength(120)
                        ->columnSpanFull(),
                    Textarea::make("bio_{$l}")
                        ->label('Biographie')
                        ->rows(5)
                        ->columnSpanFull(),
                ]),

                Section::make('Contact et expertise')
                    ->schema([
                        TextInput::make('email')->label('E-mail')->email()->maxLength(180),
                        TextInput::make('phone')->label('Téléphone')->tel()->maxLength(30),
                        TextInput::make('linkedin')->label('LinkedIn')->url()->maxLength(255),
                        FormFields::list('expertise', 'Domaines d’expertise', 'Ajouter un domaine…'),
                    ])
                    ->columns(3),

                Section::make('Publication')
                    ->schema([
                        \Filament\Forms\Components\Toggle::make('is_leadership')
                            ->label('Membre de la direction')
                            ->helperText('Apparaît dans la section Direction.'),
                        \Filament\Forms\Components\Toggle::make('is_published')
                            ->label('Publié')
                            ->default(true),
                        TextInput::make('position')
                            ->label('Ordre d’affichage')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),
            ]);
    }
}
