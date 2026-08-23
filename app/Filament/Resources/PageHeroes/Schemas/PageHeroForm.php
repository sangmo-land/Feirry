<?php

namespace App\Filament\Resources\PageHeroes\Schemas;

use App\Filament\Support\FormFields;
use App\Models\PageHero;
use App\Support\Media;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PageHeroForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Texte')
                    ->description('Laissez un champ vide pour reprendre le texte d’origine du site.')
                    ->schema([
                        FormFields::localeTabs(fn (string $locale) => [
                            TextInput::make("title_{$locale}")
                                ->label($locale === 'fr' ? 'Titre' : 'Titre (EN)')
                                ->maxLength(160)
                                ->columnSpanFull(),

                            TextInput::make("title_accent_{$locale}")
                                ->label($locale === 'fr' ? 'Deuxième ligne (en orange)' : 'Deuxième ligne (EN)')
                                ->maxLength(160)
                                ->columnSpanFull()
                                // Only the home page sets a second line; the
                                // others run a single headline.
                                ->visible(fn (?PageHero $record) => $record?->key === 'home')
                                ->helperText('Affichée sous le titre, dans l’orange de la marque.'),

                            Textarea::make("lead_{$locale}")
                                ->label($locale === 'fr' ? 'Accroche' : 'Accroche (EN)')
                                ->rows(3)
                                ->maxLength(400)
                                ->columnSpanFull()
                                // The shipped home lead opens on the founding
                                // year, which is itself editable in the site
                                // settings, so it is written as a token.
                                ->helperText(fn (?PageHero $record) => $record?->key === 'home'
                                    ? 'Écrivez :year pour insérer l’année de création de l’entreprise.'
                                    : null),
                        ]),
                    ]),

                Section::make('Photographie')
                    ->schema([
                        // The seeded pictures are remote URLs, which the upload
                        // field cannot preview — it only knows files on the
                        // disk. Without this the client sees an empty dropzone
                        // on a page that plainly has an image on it.
                        Placeholder::make('current_image')
                            ->label('Image actuelle')
                            ->visible(fn (?PageHero $record) => filled($record?->image))
                            ->content(fn (?PageHero $record) => new HtmlString(
                                '<img src="'.e((string) Media::url($record->image)).'" alt="" '
                                .'style="height:11rem;width:100%;object-fit:cover;border-radius:0.5rem" />'
                            ))
                            ->columnSpanFull(),

                        FormFields::image('image', 'Remplacer l’image')
                            ->helperText(
                                'JPG ou WebP, 6 Mo maximum. Format paysage, 1600 px de large au minimum : '
                                .'l’image occupe la moitié de l’écran et se fond dans le texte sur son bord intérieur.'
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
