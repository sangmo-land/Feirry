<?php

namespace App\Filament\Resources\AssistanceRequests\Pages;

use App\Filament\Resources\AssistanceRequests\AssistanceRequestResource;
use App\Models\Campaign;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

/** A file dropped off at the office is entered in the same system (CDC §07). */
class CreateAssistanceRequest extends CreateRecord
{
    protected static string $resource = AssistanceRequestResource::class;

    protected static ?string $title = 'Enregistrer une demande reçue au bureau';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['source'] = 'office';
        $data['idempotency_key'] = 'office-'.Str::uuid();
        $data['campaign_id'] ??= Campaign::displayed()?->id;
        $data['status'] ??= 'received';
        $data['locale'] = 'fr';
        $data['privacy_accepted_at'] = now();
        $data['notice_version'] = config('site.cdp.notice_version');

        return $data;
    }
}
