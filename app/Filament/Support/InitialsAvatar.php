<?php

namespace App\Filament\Support;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;

/**
 * Initials drawn locally: the default provider sends each admin's name to
 * ui-avatars.com, a third party (CDC Coup de Pouce §15: no personal data out).
 */
class InitialsAvatar implements AvatarProvider
{
    public function get(Model $record): string
    {
        $initials = collect(preg_split('/\s+/', trim((string) ($record->name ?? '?'))))
            ->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') ?: '?';

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"><rect width="64" height="64" fill="#FE5000"/>'
            .'<text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" font-family="Arial,sans-serif" font-size="26" font-weight="700" fill="#0B090B">'
            .e($initials).'</text></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
