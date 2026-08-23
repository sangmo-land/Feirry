<?php

namespace App\Filament\Resources\PageHeroes;

use App\Filament\Resources\PageHeroes\Pages\EditPageHero;
use App\Filament\Resources\PageHeroes\Pages\ListPageHeroes;
use App\Filament\Resources\PageHeroes\Schemas\PageHeroForm;
use App\Filament\Resources\PageHeroes\Tables\PageHeroesTable;
use App\Models\PageHero;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * The opening block of each fixed page.
 *
 * There is no create or delete here on purpose: every row answers to a route
 * that exists in code, so the list is the site's own set of pages and adding
 * to it from the admin would only produce a hero nothing renders.
 */
class PageHeroResource extends Resource
{
    protected static ?string $model = PageHero::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Contenu du site';

    protected static ?string $navigationLabel = 'En-têtes de pages';

    protected static ?string $modelLabel = 'en-tête';

    protected static ?string $pluralModelLabel = 'en-têtes de pages';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return PageHeroForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PageHeroesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPageHeroes::route('/'),
            'edit' => EditPageHero::route('/{record}/edit'),
        ];
    }
}
