<?php

namespace App\Filament\Support;

use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

/** Download links for the private documents attached to a lead. */
class LeadAttachments
{
    public static function field(string $type): TextEntry
    {
        return TextEntry::make('attachments_list')
            ->label('Documents joints (privés)')
            ->columnSpanFull()
            ->state(function ($record) use ($type) {
                $files = $record?->attachments ?? [];

                if (! $files) {
                    return new HtmlString('<em>Aucun document</em>');
                }

                return new HtmlString(collect($files)->map(function ($file, $i) use ($record, $type) {
                    $url = route('admin.lead-file', ['type' => $type, 'id' => $record->id, 'index' => $i]);
                    $kb = isset($file['size']) ? ' ('.round($file['size'] / 1024).' Ko)' : '';

                    return '<a href="'.e($url).'" style="color:#FE5000;font-weight:600">'.e($file['name'] ?? 'document').'</a>'.$kb;
                })->implode('<br>'));
            });
    }
}
