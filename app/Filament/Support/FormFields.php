<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Illuminate\Support\Str;

/**
 * Shared builders for the admin forms.
 *
 * Every editable string exists twice in the schema (`_fr` and `_en`), so
 * without a helper each resource would repeat the same pair of fields a dozen
 * times. These put the two locales side by side in tabs, which is also how the
 * client thinks about the work: write it in French, then translate.
 */
class FormFields
{
    /** Two-locale tab set. `$build` receives the suffix ('fr' or 'en'). */
    public static function localeTabs(callable $build): Tabs
    {
        return Tabs::make('locales')
            ->tabs([
                Tabs\Tab::make('Français')
                    ->icon('heroicon-o-flag')
                    ->schema($build('fr'))
                    ->columns(2),
                Tabs\Tab::make('English')
                    ->icon('heroicon-o-language')
                    ->schema($build('en'))
                    ->columns(2),
            ])
            ->columnSpanFull();
    }

    /** Slug that fills itself from the French title but stays editable. */
    public static function slug(string $from = 'title_fr', string $table = ''): TextInput
    {
        return TextInput::make('slug')
            ->label('Identifiant URL')
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true, table: $table ?: null)
            ->live(onBlur: true)
            ->helperText('Utilisé dans l’adresse de la page. À ne plus modifier une fois la page en ligne.')
            ->afterStateHydrated(function (TextInput $component, ?string $state) use ($from) {
                $component->state($state);
            })
            ->dehydrateStateUsing(fn (?string $state) => $state ? Str::slug($state) : null);
    }

    /** Mirrors the French title into the slug while the record is new. */
    public static function titleWithSlug(string $locale = 'fr', string $label = 'Titre'): TextInput
    {
        $field = TextInput::make("title_{$locale}")
            ->label($locale === 'fr' ? $label : "{$label} (EN)")
            ->maxLength(255);

        if ($locale === 'fr') {
            $field
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function (?string $state, callable $set, callable $get) {
                    if (blank($get('slug')) && filled($state)) {
                        $set('slug', Str::slug($state));
                    }
                });
        }

        return $field;
    }

    public static function richBody(string $locale, string $label = 'Contenu'): RichEditor
    {
        return RichEditor::make("body_{$locale}")
            ->label($locale === 'fr' ? $label : "{$label} (EN)")
            ->toolbarButtons([
                'bold', 'italic', 'underline', 'strike',
                'h2', 'h3', 'bulletList', 'orderedList',
                'link', 'blockquote', 'undo', 'redo',
            ])
            ->columnSpanFull();
    }

    public static function excerpt(string $locale, string $label = 'Accroche'): Textarea
    {
        return Textarea::make("excerpt_{$locale}")
            ->label($locale === 'fr' ? $label : "{$label} (EN)")
            ->rows(3)
            ->maxLength(600)
            ->columnSpanFull();
    }

    /** A JSON list of short strings, edited as tags. */
    public static function list(string $field, string $label, string $placeholder = 'Ajouter…'): TagsInput
    {
        return TagsInput::make($field)
            ->label($label)
            ->placeholder($placeholder)
            ->reorderable()
            ->columnSpanFull();
    }

    public static function image(string $field = 'cover_image', string $label = 'Image de couverture'): FileUpload
    {
        return FileUpload::make($field)
            ->label($label)
            ->image()
            ->imageEditor()
            ->disk('public')
            ->directory('uploads')
            ->maxSize(6144)
            ->helperText('JPG ou WebP, 6 Mo maximum. Format paysage recommandé.');
    }

    public static function gallery(string $field = 'gallery', string $label = 'Galerie'): FileUpload
    {
        return FileUpload::make($field)
            ->label($label)
            ->image()
            ->multiple()
            ->reorderable()
            ->appendFiles()
            ->disk('public')
            ->directory('uploads/gallery')
            ->maxSize(6144)
            ->columnSpanFull();
    }

    /** Publication state, ordering and the two SEO overrides. */
    public static function publication(bool $featured = true): Section
    {
        $fields = [
            Toggle::make('is_published')
                ->label('Publié')
                ->default(true)
                ->helperText('Décochez pour retirer la page du site sans la supprimer.'),
        ];

        if ($featured) {
            $fields[] = Toggle::make('is_featured')
                ->label('Mis en avant')
                ->helperText('Apparaît sur la page d’accueil.');
        }

        $fields[] = TextInput::make('position')
            ->label('Ordre d’affichage')
            ->numeric()
            ->default(0)
            ->helperText('Le plus petit nombre passe en premier.');

        return Section::make('Publication')
            ->schema($fields)
            ->columns(3);
    }

    public static function seo(): Section
    {
        return Section::make('Référencement')
            ->description('Laissez vide pour reprendre automatiquement le titre et l’accroche.')
            ->collapsed()
            ->schema([
                TextInput::make('meta_title')
                    ->label('Titre pour Google')
                    ->maxLength(70)
                    ->helperText('70 caractères maximum.'),
                Textarea::make('meta_description')
                    ->label('Description pour Google')
                    ->rows(2)
                    ->maxLength(175)
                    ->helperText('175 caractères maximum.'),
            ])
            ->columns(1);
    }
}
