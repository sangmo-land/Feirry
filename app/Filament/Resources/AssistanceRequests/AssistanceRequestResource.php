<?php

namespace App\Filament\Resources\AssistanceRequests;

use App\Filament\Resources\AssistanceRequests\Pages\CreateAssistanceRequest;
use App\Filament\Resources\AssistanceRequests\Pages\EditAssistanceRequest;
use App\Filament\Resources\AssistanceRequests\Pages\ListAssistanceRequests;
use App\Filament\Resources\AssistanceRequests\Schemas\AssistanceRequestForm;
use App\Filament\Resources\AssistanceRequests\Tables\AssistanceRequestsTable;
use App\Models\AssistanceRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Coup de Pouce requests (CDC Coup de Pouce V2 §11–§13).
 * A technical referent only sees the requests assigned to them;
 * the communication role has no access to plans or contact details.
 */
class AssistanceRequestResource extends Resource
{
    protected static ?string $model = AssistanceRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|UnitEnum|null $navigationGroup = 'Demandes entrantes';

    protected static ?string $navigationLabel = 'Coup de Pouce — demandes';

    protected static ?string $modelLabel = 'demande Coup de Pouce';

    protected static ?string $pluralModelLabel = 'demandes Coup de Pouce';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->canSeeAssistanceRequests();
    }

    public static function canCreate(): bool
    {
        return (bool) auth()->user()?->hasRole('admin', 'administration');
    }

    public static function canDelete($record): bool
    {
        return false; // Retention is handled by the documented purge, never by a click.
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['assignee', 'campaign']);
        $user = auth()->user();

        if ($user && ! $user->hasRole('admin', 'administration')) {
            $query->where('assigned_to', $user->id);
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        $n = static::getEloquentQuery()->whereIn('status', ['received', 'incomplete'])->count();

        return $n ? (string) $n : null;
    }

    public static function form(Schema $schema): Schema
    {
        return AssistanceRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssistanceRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssistanceRequests::route('/'),
            'create' => CreateAssistanceRequest::route('/create'),
            'edit' => EditAssistanceRequest::route('/{record}/edit'),
        ];
    }
}
