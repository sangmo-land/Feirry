<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Lets the client change the details that appear on every page — company name,
 * phone number, address, opening hours — without touching config files or
 * waiting on a deployment. Values are stored in the `settings` table and read
 * back through App\Support\SiteSettings, which falls through to config/site.php.
 */
class SiteSettingsPage extends Page
{
    // Filament\Pages\Page already implements HasSchemas and carries the
    // InteractsWithSchemas trait, so defining form() below is enough.

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Réglages';

    protected static ?string $navigationLabel = 'Paramètres du site';

    protected static ?string $title = 'Paramètres du site';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.site-settings';

    /** @var array<string, mixed> */
    public array $data = [];

    /** Keys mirrored between config/site.php and the settings table. */
    public const KEYS = [
        'brand.name',
        'brand.legal_name',
        'brand.tagline',
        'contact.address',
        'contact.city',
        'contact.phone',
        'contact.whatsapp',
        'contact.email',
        'contact.hours_fr',
        'contact.hours_en',
        'contact.maps_url',
        'contact.latitude',
        'contact.longitude',
        'social.facebook',
        'social.linkedin',
        'social.instagram',
        'social.tiktok',
        'social.youtube',
    ];

    public function mount(): void
    {
        $this->form->fill(
            collect(self::KEYS)
                ->mapWithKeys(fn (string $key) => [
                    str_replace('.', '__', $key) => Setting::get($key) ?? config("site.{$key}"),
                ])
                ->all()
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Identité')
                    ->schema([
                        TextInput::make('brand__name')
                            ->label('Nom commercial')
                            ->required()
                            ->maxLength(60),
                        TextInput::make('brand__legal_name')
                            ->label('Raison sociale')
                            ->maxLength(180),
                        TextInput::make('brand__tagline')
                            ->label('Slogan')
                            ->helperText('Identique dans les deux langues : le slogan fait partie de la marque.')
                            ->maxLength(180)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Coordonnées')
                    ->description('Utilisées dans l’en-tête, le pied de page et les pages Contact et Devis.')
                    ->schema([
                        TextInput::make('contact__phone')
                            ->label('Téléphone principal')
                            ->tel()
                            ->required()
                            ->maxLength(30),
                        TextInput::make('contact__whatsapp')
                            ->label('Numéro WhatsApp')
                            ->maxLength(20)
                            ->helperText('Format international sans « + » ni espace, par exemple 237694451280.'),
                        TextInput::make('contact__email')
                            ->label('E-mail de contact')
                            ->email()
                            ->required()
                            ->maxLength(180),
                        TextInput::make('contact__city')
                            ->label('Ville')
                            ->maxLength(80),
                        TextInput::make('contact__address')
                            ->label('Adresse')
                            ->maxLength(180)
                            ->columnSpanFull(),
                        TextInput::make('contact__hours_fr')
                            ->label('Horaires (français)')
                            ->maxLength(180)
                            ->columnSpanFull(),
                        TextInput::make('contact__hours_en')
                            ->label('Horaires (anglais)')
                            ->maxLength(180)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Localisation')
                    ->description(
                        'Le point affiché sur la carte des pages Entreprise et Contact. '
                        .'Pour le déplacer : ouvrez Google Maps, faites un clic droit sur le bâtiment, '
                        .'cliquez sur les coordonnées pour les copier, puis collez-les ci-dessous.'
                    )
                    ->schema([
                        TextInput::make('contact__latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step('0.000001')
                            // Optional: the map pin is simply not shown until FEIRRY gives its coordinates.
                            ->requiredWith('contact__longitude')
                            ->placeholder('4.047588'),
                        TextInput::make('contact__longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step('0.000001')
                            ->requiredWith('contact__latitude')
                            ->placeholder('9.728659'),
                        TextInput::make('contact__maps_url')
                            ->label('Lien « Nous trouver »')
                            ->url()
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->helperText('Ouvre l’itinéraire dans Google Maps. Un lien maps.app.goo.gl convient.'),
                    ])
                    ->columns(2),

                Section::make('Réseaux sociaux')
                    ->description(
                        'Les liens du pied de page et du bouton flottant, en bas à droite de chaque page. '
                        .'Videz un champ pour retirer le réseau : son icône disparaît des deux endroits. '
                        .'Le bouton WhatsApp reprend le numéro saisi plus haut.'
                    )
                    ->schema([
                        TextInput::make('social__facebook')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(300)
                            ->placeholder('https://facebook.com/feirry'),
                        TextInput::make('social__linkedin')
                            ->label('LinkedIn')
                            ->url()
                            ->maxLength(300)
                            ->placeholder('https://linkedin.com/company/feirry'),
                        TextInput::make('social__instagram')
                            ->label('Instagram')
                            ->url()
                            ->maxLength(300)
                            ->placeholder('https://instagram.com/feirry'),
                        TextInput::make('social__tiktok')
                            ->label('TikTok')
                            ->url()
                            ->maxLength(300)
                            ->placeholder('https://tiktok.com/@feirry'),
                        TextInput::make('social__youtube')
                            ->label('YouTube')
                            ->url()
                            ->maxLength(300)
                            ->placeholder('https://youtube.com/@feirry'),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer')
                ->icon('heroicon-o-check')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $field => $value) {
            $key = str_replace('__', '.', $field);
            $group = explode('.', $key)[0];

            // Stored as an empty string rather than NULL. Setting::get() cannot
            // tell a NULL value from a missing row, and the social links need
            // that distinction: emptied on purpose means hide the network,
            // never saved means use the one config/site.php shipped with.
            Setting::put($key, $value ?? '', $group);
        }

        Notification::make()
            ->title('Paramètres enregistrés')
            ->body('Les modifications sont visibles immédiatement sur le site.')
            ->success()
            ->send();
    }
}
