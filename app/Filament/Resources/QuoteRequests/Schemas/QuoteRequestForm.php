<?php

namespace App\Filament\Resources\QuoteRequests\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteRequestForm
{
    public const STATUSES = [
        'new' => 'Nouvelle',
        'qualified' => 'Qualifiée',
        'site_visit' => 'Visite planifiée',
        'proposal_sent' => 'Devis envoyé',
        'won' => 'Gagnée',
        'lost' => 'Perdue',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Suivi commercial')
                    ->description('La seule partie modifiable : le reste vient du formulaire rempli par le prospect.')
                    ->schema([
                        Select::make('status')
                            ->label('Statut')
                            ->options(self::STATUSES)
                            ->default('new')
                            ->required()
                            ->native(false),
                        DateTimePicker::make('contacted_at')
                            ->label('Premier contact')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Renseignez la date de votre rappel.'),
                        Textarea::make('internal_notes')
                            ->label('Notes internes')
                            ->rows(5)
                            ->columnSpanFull()
                            ->helperText('Jamais visible par le prospect.'),
                    ])
                    ->columns(2),

                Section::make('Demande reçue')
                    ->schema([
                        TextInput::make('reference')->label('Référence')->disabled(),
                        TextInput::make('name')->label('Nom')->disabled(),
                        TextInput::make('company')->label('Société')->disabled(),
                        TextInput::make('email')->label('E-mail')->disabled(),
                        TextInput::make('phone')->label('Téléphone')->disabled(),
                        Select::make('service_id')
                            ->label('Métier concerné')
                            ->relationship('service', 'title_fr')
                            ->disabled(),
                        TextInput::make('project_type')->label('Type de projet')->disabled(),
                        TextInput::make('budget_range')->label('Budget indiqué')->disabled(),
                        TextInput::make('timeline')->label('Échéance souhaitée')->disabled(),
                        TextInput::make('location')->label('Lieu du chantier')->disabled(),
                        TextInput::make('surface_m2')->label('Surface (m²)')->disabled(),
                        TextInput::make('attachment')->label('Pièce jointe')->disabled(),
                        Textarea::make('message')
                            ->label('Message du prospect')
                            ->rows(6)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }
}
