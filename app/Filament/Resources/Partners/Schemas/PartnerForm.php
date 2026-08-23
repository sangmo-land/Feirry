<?php

namespace App\Filament\Resources\Partners\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Partenaire')
                    ->schema([
                        TextInput::make('name')->label('Nom')->required()->maxLength(160),
                        \Filament\Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                'client' => 'Client',
                                'partner' => 'Partenaire',
                                'supplier' => 'Fournisseur',
                            ])
                            ->default('client')
                            ->native(false),
                        TextInput::make('website')->label('Site web')->url()->maxLength(255),
                        TextInput::make('position')->label('Ordre')->numeric()->default(0),
                        FormFields::image('logo', 'Logo'),
                        \Filament\Forms\Components\Toggle::make('is_published')->label('Publié')->default(true),
                    ])
                    ->columns(3),
            ]);
    }
}
