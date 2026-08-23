<?php

namespace App\Filament\Resources\NewsletterSubscribers\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsletterSubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Abonné')
                    ->schema([
                        TextInput::make('email')->label('E-mail')->email()->required()->maxLength(180),
                        TextInput::make('name')->label('Nom')->maxLength(120),
                        \Filament\Forms\Components\Select::make('locale')
                            ->label('Langue')
                            ->options(['fr' => 'Français', 'en' => 'English'])
                            ->default('fr')
                            ->native(false),
                        \Filament\Forms\Components\Toggle::make('is_active')->label('Actif')->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
