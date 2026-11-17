<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Filament\Support\FormFields;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Session Coup de Pouce')
                ->description('Les inscriptions ne sont ouvertes que si le statut est « Ouverte » ET que la date du jour est comprise dans la période. Une session passée ne s’affiche jamais comme ouverte.')
                ->schema([
                    TextInput::make('name')->label('Nom interne')->required()->maxLength(160),
                    Select::make('status')->label('Statut')
                        ->options(['draft' => 'Brouillon (fermée)', 'open' => 'Ouverte', 'closed' => 'Clôturée'])
                        ->default('draft')->required()->native(false),
                    DatePicker::make('starts_at')->label('Début')->native(false)->displayFormat('d/m/Y'),
                    DatePicker::make('ends_at')->label('Fin')->native(false)->displayFormat('d/m/Y')->afterOrEqual('starts_at'),
                    TextInput::make('capacity')->label('Nombre de places')->numeric()->minValue(1)
                        ->helperText('Les inscriptions se ferment quand ce nombre est atteint.'),
                    Toggle::make('show_capacity')->label('Afficher le nombre de places'),
                    Toggle::make('consultation_enabled')->label('Parcours « Consultation de dossier »')->default(true),
                    Toggle::make('visit_enabled')->label('Parcours « Visite technique »')->default(true),
                    Toggle::make('documents_required')->label('Documents obligatoires (consultation)'),
                ])->columns(2),
            FormFields::localeTabs(fn (string $l) => [
                TextInput::make("territory_{$l}")->label('Territoire couvert')->maxLength(255)->columnSpanFull(),
                Textarea::make("terms_{$l}")->label('Conditions (éligibilité, gratuité, confidentialité, droit de refus)')->rows(8)->columnSpanFull(),
            ]),
        ]);
    }
}
