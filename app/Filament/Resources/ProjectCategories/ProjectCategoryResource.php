<?php

namespace App\Filament\Resources\ProjectCategories;

use App\Filament\Resources\ProjectCategories\Pages\CreateProjectCategory;
use App\Filament\Resources\ProjectCategories\Pages\EditProjectCategory;
use App\Filament\Resources\ProjectCategories\Pages\ListProjectCategories;
use App\Filament\Resources\ProjectCategories\Schemas\ProjectCategoryForm;
use App\Filament\Resources\ProjectCategories\Tables\ProjectCategoriesTable;
use App\Models\ProjectCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProjectCategoryResource extends Resource
{
    protected static ?string $model = ProjectCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Contenu du site';

    protected static ?string $navigationLabel = 'Typologies de projet';

    protected static ?string $modelLabel = 'typologie';

    protected static ?string $pluralModelLabel = 'typologies';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name_fr';

    public static function form(Schema $schema): Schema
    {
        return ProjectCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectCategories::route('/'),
            'create' => CreateProjectCategory::route('/create'),
            'edit' => EditProjectCategory::route('/{record}/edit'),
        ];
    }
}
