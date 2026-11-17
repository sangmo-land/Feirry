<?php

namespace App\Filament\Resources\QuoteRequests;

use App\Filament\Resources\QuoteRequests\Pages\CreateQuoteRequest;
use App\Filament\Resources\QuoteRequests\Pages\EditQuoteRequest;
use App\Filament\Resources\QuoteRequests\Pages\ListQuoteRequests;
use App\Filament\Resources\QuoteRequests\Schemas\QuoteRequestForm;
use App\Filament\Resources\QuoteRequests\Tables\QuoteRequestsTable;
use App\Models\QuoteRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;

    /** Personal data and private documents: administration only (CDC Coup de Pouce §10). */
    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasRole('admin', 'administration');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Demandes entrantes';

    protected static ?string $navigationLabel = 'Demandes de devis';

    protected static ?string $modelLabel = 'demande';

    protected static ?string $pluralModelLabel = 'demandes';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function form(Schema $schema): Schema
    {
        return QuoteRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuoteRequestsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()->where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuoteRequests::route('/'),
            'create' => CreateQuoteRequest::route('/create'),
            'edit' => EditQuoteRequest::route('/{record}/edit'),
        ];
    }
}
