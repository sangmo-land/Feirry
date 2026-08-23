<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Message reçu')
                    ->schema([
                        TextInput::make('name')->label('Nom')->disabled(),
                        TextInput::make('email')->label('E-mail')->disabled(),
                        TextInput::make('phone')->label('Téléphone')->disabled(),
                        TextInput::make('subject')->label('Objet')->disabled(),
                        Textarea::make('message')
                            ->label('Message')
                            ->rows(10)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Suivi')
                    ->schema([
                        Toggle::make('is_read')->label('Marqué comme lu'),
                        DateTimePicker::make('replied_at')
                            ->label('Répondu le')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}
