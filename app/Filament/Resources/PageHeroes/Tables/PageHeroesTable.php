<?php

namespace App\Filament\Resources\PageHeroes\Tables;

use App\Models\PageHero;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageHeroesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->paginated(false)
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->height(44)
                    ->width(72),

                TextColumn::make('key')
                    ->label('Page')
                    ->weight('bold')
                    ->formatStateUsing(fn (string $state) => PageHero::PAGES[$state][1] ?? $state),

                TextColumn::make('title_fr')
                    ->label('Titre')
                    ->limit(60)
                    ->placeholder('Texte d’origine'),

                TextColumn::make('lead_fr')
                    ->label('Accroche')
                    ->limit(70)
                    ->color('gray')
                    ->placeholder('Texte d’origine'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([]);
    }
}
