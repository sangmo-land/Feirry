<?php

namespace App\Filament\Resources\Users;

use App\Models\User;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use UnitEnum;

/** Team accounts and roles (CDC Coup de Pouce §12). Technical administrator only. */
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Réglages';

    protected static ?string $navigationLabel = 'Équipe et rôles';

    protected static ?string $modelLabel = 'membre';

    protected static ?string $pluralModelLabel = 'équipe';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasRole('admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('admin') && $record->id !== auth()->id();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nom')->required()->maxLength(120),
            TextInput::make('email')->label('E-mail')->email()->required()->unique(ignoreRecord: true),
            Select::make('role')->label('Rôle')->options(User::ROLES)->required()->native(false)->default('referent')
                ->helperText('Référent : ne voit que les demandes qui lui sont affectées. Communication : aucun accès aux demandes ni aux plans.'),
            TextInput::make('password')->label('Mot de passe')->password()->revealable()
                ->minLength(12)
                ->required(fn ($record) => $record === null)
                ->dehydrated(fn ($state) => filled($state))
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->helperText('12 caractères minimum. Laisser vide pour ne pas le changer.'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->weight('bold'),
                TextColumn::make('email')->label('E-mail'),
                TextColumn::make('role')->label('Rôle')->badge()->formatStateUsing(fn ($s) => User::ROLES[$s] ?? $s),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
