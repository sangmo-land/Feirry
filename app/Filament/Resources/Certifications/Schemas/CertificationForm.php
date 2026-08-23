<?php

namespace App\Filament\Resources\Certifications\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CertificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Certification')
                    ->schema([
                        TextInput::make('name')->label('Intitulé')->required()->maxLength(160),
                        TextInput::make('issuer')->label('Organisme')->maxLength(160),
                        TextInput::make('year')->label('Année')->numeric()->minValue(1950)->maxValue((int) date('Y')),
                        TextInput::make('position')->label('Ordre')->numeric()->default(0),
                        FormFields::image('logo', 'Logo'),
                        \Filament\Forms\Components\Toggle::make('is_published')->label('Publiée')->default(true),
                    ])
                    ->columns(3),

                FormFields::localeTabs(fn (string $l) => [
                    Textarea::make("description_{$l}")->label('Description')->rows(4)->columnSpanFull(),
                ]),
            ]);
    }
}
